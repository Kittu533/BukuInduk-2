<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\NilaiMapel;
use App\Models\NilaiEkskul;
use App\Models\Kehadiran;
use App\Models\MataPelajaran;
use App\Models\Ekstrakurikuler;
use App\Models\ProfilSekolah;
use App\Models\KenaikanKelas;
use Illuminate\Support\Facades\DB;

/**
 * BukuIndukController — Export PDF Buku Induk & Hasil Belajar
 *
 * 2 template berdasarkan format resmi sekolah:
 * - Template A: Buku Induk Siswa (identitas lengkap per siswa)
 * - Template B: Hasil Belajar Siswa (rekap nilai 3 tahun, 6 semester)
 *
 * @legacy Fitur baru (30 Mei 2026) sesuai format PDF SMA Negeri 3 Cilacap
 */
class BukuIndukController extends Controller
{
    /**
     * TEMPLATE A — Download Buku Induk Siswa (identitas lengkap)
     */
    public function downloadBukuInduk($id_siswa)
    {
        $siswa = Siswa::findOrFail($id_siswa);
        $profil = ProfilSekolah::first();

        // Cari kelas pertama siswa (waktu diterima)
        $kelasPertama = SiswaKelas::with('kelasAktif.kelas')
            ->where('id_siswa', $id_siswa)
            ->orderBy('id_siswa_kelas', 'asc')
            ->first()
            ?->kelasAktif?->kelas?->nama_kelas ?? '';

        $coverHtml = view('admin.profil_sekolah.cover_pdf', [
            'namaSekolah' => $profil?->nama_sekolah ?? 'SMA Negeri 3 Cilacap',
            'namaKelas' => null,
            'namaSiswa' => $siswa->nama_lengkap,
            'nis' => $siswa->nis,
        ])->render();

        $contentHtml = view('admin.export.buku_induk_pdf', compact('siswa', 'profil', 'kelasPertama'))->render();

        $fullHtml = $this->mergeCoverWithContent($coverHtml, $contentHtml);

        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($fullHtml);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('buku_induk_' . $siswa->nama_lengkap . '.pdf');
    }

    /**
     * TEMPLATE B — Download Hasil Belajar Siswa (rekap 3 tahun)
     */
    public function downloadHasilBelajar($id_siswa)
    {
        $siswa = Siswa::findOrFail($id_siswa);
        $profil = ProfilSekolah::first();

        // ========================================
        // AMBIL SEMUA SEMESTER YANG PERNAH DIIKUTI
        // Diurutkan: tahun ASC → ganjil dulu → genap
        // ========================================
        $siswaKelasList = SiswaKelas::with(['kelasAktif.semester.tahunAjaran'])
            ->where('id_siswa', $id_siswa)
            ->get()
            ->sortBy(fn($sk) => ($sk->kelasAktif?->semester?->tahunAjaran?->tahun ?? '') . '-' . ($sk->kelasAktif?->semester?->nama_semester == 'Ganjil' ? '0' : '1'))
            ->values();

        // Tahun headers (3 tahun = 3 kolom)
        $tahunHeaders = $siswaKelasList
            ->pluck('kelasAktif.semester.tahunAjaran.tahun')
            ->unique()
            ->take(3)
            ->values()
            ->toArray();

        // Pad ke 3 elemen kalau kurang
        while (count($tahunHeaders) < 3) {
            $tahunHeaders[] = '..../...';
        }

        // ========================================
        // MAPEL — DAFTAR LENGKAP (untuk semua kolom)
        // ========================================
        $mapelWajib = MataPelajaran::where('id_mapel', 'LIKE', 'PU%')
            ->orderBy('id_mapel')
            ->get()
            ->unique(fn ($mapel) => $mapel->kode_dasar)
            ->values();

        $mapelPilihan = MataPelajaran::where('id_mapel', 'LIKE', 'PL%')
            ->orderBy('id_mapel')
            ->get()
            ->unique(fn ($mapel) => $mapel->kode_dasar)
            ->values();

        // Filter agama berdasarkan agama siswa
        $excluded = $siswa->getMapelExcluded();
        $mapelWajib = $mapelWajib->whereNotIn('nama_mapel', $excluded)->values();

        // ========================================
        // NILAI PER MAPEL PER SEMESTER
        // Index 0-5 = ganjil/genap × 3 tahun
        // ========================================
        $nilaiPerMapel = [];

        foreach ($siswaKelasList as $idx => $sk) {
            // Tentukan kolom berdasarkan tahun + jenis semester
            $tahun = $sk->kelasAktif?->semester?->tahunAjaran?->tahun;
            $isGanjil = $sk->kelasAktif?->semester?->nama_semester === 'Ganjil';

            $tahunIndex = array_search($tahun, $tahunHeaders);
            if ($tahunIndex === false) continue;

            $kolomIndex = ($tahunIndex * 2) + ($isGanjil ? 0 : 1);

            // Ambil nilai untuk kelas aktif ini
            $nilaiList = NilaiMapel::with('jadwalMengajar.mapel')
                ->where('id_siswa', $id_siswa)
                ->whereHas('jadwalMengajar', fn($q) => $q->where('id_kelas_aktif', $sk->id_kelas_aktif))
                ->get();

            foreach ($nilaiList as $nilai) {
                $idMapel = $nilai->jadwalMengajar?->mapel?->kode_dasar;
                if (!$idMapel) continue;

                if (!isset($nilaiPerMapel[$idMapel])) {
                    $nilaiPerMapel[$idMapel] = array_fill(0, 6, '-');
                }
                $nilaiPerMapel[$idMapel][$kolomIndex] = $nilai->nilai_akhir ?? '-';
            }
        }

        // ========================================
        // EKSKUL PER SEMESTER
        // ========================================
        $daftarEkskul = Ekstrakurikuler::orderBy('nama_ekskul')->get();
        $nilaiEkskulPerSemester = [];

        // Filter ekskul yang diambil siswa
        $ekskulSiswaIds = NilaiEkskul::where('id_siswa', $id_siswa)
            ->pluck('id_ekskul')
            ->unique()
            ->toArray();

        $daftarEkskul = $daftarEkskul->whereIn('id_ekskul', $ekskulSiswaIds)->values();

        foreach ($siswaKelasList as $sk) {
            $tahun = $sk->kelasAktif?->semester?->tahunAjaran?->tahun;
            $isGanjil = $sk->kelasAktif?->semester?->nama_semester === 'Ganjil';

            $tahunIndex = array_search($tahun, $tahunHeaders);
            if ($tahunIndex === false) continue;

            $kolomIndex = ($tahunIndex * 2) + ($isGanjil ? 0 : 1);

            // Untuk simplicity, ambil nilai ekskul siswa saja (tidak per kelas_aktif)
            // karena tabel nilai_ekskul punya id_kelas_aktif
            $ekskulList = NilaiEkskul::where('id_siswa', $id_siswa)
                ->where('id_kelas_aktif', $sk->id_kelas_aktif)
                ->get();

            foreach ($ekskulList as $eskul) {
                if (!isset($nilaiEkskulPerSemester[$eskul->id_ekskul])) {
                    $nilaiEkskulPerSemester[$eskul->id_ekskul] = array_fill(0, 6, '-');
                }
                $nilaiEkskulPerSemester[$eskul->id_ekskul][$kolomIndex] = $eskul->nilai;
            }
        }

        // ========================================
        // KEHADIRAN PER SEMESTER
        // ========================================
        $kehadiranPerSemester = array_fill(0, 6, ['sakit' => '-', 'izin' => '-', 'alpa' => '-']);

        foreach ($siswaKelasList as $sk) {
            $tahun = $sk->kelasAktif?->semester?->tahunAjaran?->tahun;
            $isGanjil = $sk->kelasAktif?->semester?->nama_semester === 'Ganjil';

            $tahunIndex = array_search($tahun, $tahunHeaders);
            if ($tahunIndex === false) continue;

            $kolomIndex = ($tahunIndex * 2) + ($isGanjil ? 0 : 1);

            // Hitung kehadiran berdasarkan jadwal yang termasuk kelas_aktif ini
            $jadwalIds = DB::table('jadwal_mengajar')
                ->where('id_kelas_aktif', $sk->id_kelas_aktif)
                ->pluck('id_jadwal');

            $sakit = Kehadiran::where('id_siswa', $id_siswa)
                ->whereIn('id_jadwal', $jadwalIds)
                ->whereIn('status', ['sakit', 'Sakit'])
                ->count();

            $izin = Kehadiran::where('id_siswa', $id_siswa)
                ->whereIn('id_jadwal', $jadwalIds)
                ->whereIn('status', ['izin', 'Izin'])
                ->count();

            $alpa = Kehadiran::where('id_siswa', $id_siswa)
                ->whereIn('id_jadwal', $jadwalIds)
                ->whereIn('status', ['alpa', 'Alpa'])
                ->count();

            $kehadiranPerSemester[$kolomIndex] = [
                'sakit' => $sakit ?: '-',
                'izin' => $izin ?: '-',
                'alpa' => $alpa ?: '-',
            ];
        }

        // ========================================
        // KENAIKAN / KELULUSAN — Ambil dari DB
        // ========================================
        $kenaikanRecords = KenaikanKelas::where('id_siswa', $id_siswa)
            ->whereIn('id_tahun', collect($tahunHeaders)->map(function ($tahun) {
                return DB::table('tahun_ajaran')->where('tahun', $tahun)->value('id_tahun');
            })->filter()->toArray())
            ->with('tahunAjaran')
            ->get()
            ->keyBy(fn($k) => $k->tahunAjaran?->tahun ?? '');

        $kenaikan = [];
        foreach ($tahunHeaders as $tahun) {
            $record = $kenaikanRecords->get($tahun);
            if ($record) {
                $kenaikan[] = $record->label_status;
            } else {
                $kenaikan[] = '-';
            }
        }

        // Pad ke 3
        while (count($kenaikan) < 3) {
            $kenaikan[] = '-';
        }

        // ========================================
        // RENDER PDF
        // ========================================
        $coverHtml = view('admin.profil_sekolah.cover_pdf', [
            'namaSekolah' => $profil?->nama_sekolah ?? 'SMA Negeri 3 Cilacap',
            'namaKelas' => null,
            'namaSiswa' => $siswa->nama_lengkap,
            'nis' => $siswa->nis,
        ])->render();

        $contentHtml = view('admin.export.hasil_belajar_pdf', compact(
            'siswa', 'profil', 'tahunHeaders', 'mapelWajib', 'mapelPilihan',
            'nilaiPerMapel', 'daftarEkskul', 'nilaiEkskulPerSemester',
            'kehadiranPerSemester', 'kenaikan'
        ))->render();

        $fullHtml = $this->mergeCoverWithContent($coverHtml, $contentHtml);

        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($fullHtml);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('hasil_belajar_' . $siswa->nama_lengkap . '.pdf');
    }

    /**
     * Helper: Gabungkan cover HTML dengan content HTML
     */
    private function mergeCoverWithContent(string $cover, string $content): string
    {
        $coverBody = $this->extractBody($cover);
        $contentBody = $this->extractBody($content);
        $coverStyle = $this->extractStyle($cover);
        $contentStyle = $this->extractStyle($content);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        {$coverStyle}
        {$contentStyle}
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    {$coverBody}
    <div class="page-break"></div>
    {$contentBody}
</body>
</html>
HTML;
    }

    private function extractBody(string $html): string
    {
        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $matches)) {
            return $matches[1];
        }
        return $html;
    }

    private function extractStyle(string $html): string
    {
        if (preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $html, $matches)) {
            return implode("\n", $matches[1]);
        }
        return '';
    }
}
