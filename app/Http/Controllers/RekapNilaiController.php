<?php

namespace App\Http\Controllers;

use App\Models\KelasAktif;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\NilaiMapel;
use App\Models\NilaiEkskul;
use App\Models\Kehadiran;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\KenaikanKelas;
use App\Models\JadwalMengajar;
use App\Models\ProfilSekolah;
use App\Support\AcademicRecord;
use Illuminate\Support\Facades\DB;

/**
 * RekapNilaiController — Rekap Rapor Lengkap (Admin)
 *
 * Fitur:
 * - List kelas → list siswa → rekap lengkap per siswa
 * - Rekap berisi: nilai akademik (wajib + pilihan) + ekskul + kehadiran
 * - Download rekap sebagai PDF (menggunakan dompdf)
 *
 * @legacy Fitur baru (30 Mei 2026) — tidak ada di versi sebelumnya
 */
class RekapNilaiController extends Controller
{
    /**
     * List kelas untuk rekap
     */
   public function index()
{
    $menu = "rekap";

    $tahun = TahunAjaran::aktif()->firstOrFail();
    $semester = Semester::aktif()->firstOrFail();

    $kelas = KelasAktif::with([
            'kelas',
            'guru',
            'tahunAjaran',
            'semester',
            'siswaKelas'
        ])
        ->where('id_tahun', $tahun->id_tahun)
        ->whereHas('semester', function ($query) use ($semester) {
            $query->where('nama_semester', $semester->nama_semester);
        })
        ->whereHas('kelas')
        ->whereHas('guru')
        ->get()
        ->map(function ($item) {

            $item->jumlah_siswa =
                $item->siswaKelas->count();

            return $item;
        })
        ->sortBy(fn ($item) => $item->kelas->nama_kelas)
        ->values();

    return view(
        'admin.rekap.index',
        compact(
            'kelas',
            'menu'
        )
    );
}

    /**
     * List siswa per kelas
     */
    public function detail($id_kelas_aktif)
    {
        $menu = "rekap";

        $kelasAktif = KelasAktif::with([
            'kelas',
            'semester',
            'tahunAjaran'
        ])->findOrFail($id_kelas_aktif);

        $kelas = (object)[
            'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
            'nama_kelas'     => $kelasAktif->kelas->nama_kelas ?? '-',
            'semester'       => $kelasAktif->semester->nama_semester ?? '-',
            'tahun'          => $kelasAktif->tahunAjaran->tahun ?? '-',
        ];

        $siswa = SiswaKelas::with('siswa')
            ->where('id_kelas_aktif', $id_kelas_aktif)
            ->get();

        return view(
            'admin.rekap.detail',
            compact(
                'kelas',
                'siswa',
                'menu'
            )
        );
    }

    /**
     * Rekap lengkap per siswa (nilai akademik + ekskul + kehadiran)
     */
   public function rekapSiswa($id)
{
    $menu = "rekap";

    $siswa = Siswa::findOrFail($id);

    /*
    |--------------------------------------------------------------------------
    | NILAI AKADEMIK 6 SEMESTER
    |--------------------------------------------------------------------------
    */

    $semuaNilai = NilaiMapel::with([
        'jadwalMengajar.kelasAktif.kelas',
        'jadwalMengajar.kelasAktif.semester',
        'jadwalMengajar.mapel',
    ])
    ->where('id_siswa', $id)
    ->get();

    $tahunHeaders = ['X', 'XI', 'XII'];

    $nilaiPerMapel = [];

    foreach ($semuaNilai as $nilai) {

        $kelasAktif = $nilai->jadwalMengajar?->kelasAktif;

        if (!$kelasAktif) {
            continue;
        }

        $namaKelas = strtoupper(
            $kelasAktif->kelas->nama_kelas ?? ''
        );

        $isGanjil =
            ($kelasAktif->semester->nama_semester ?? '')
            === 'Ganjil';

        if (str_starts_with($namaKelas, 'XII')) {

            $kolomIndex = $isGanjil ? 4 : 5;

        } elseif (str_starts_with($namaKelas, 'XI')) {

            $kolomIndex = $isGanjil ? 2 : 3;

        } elseif (str_starts_with($namaKelas, 'X')) {

            $kolomIndex = $isGanjil ? 0 : 1;

        } else {

            continue;
        }

        $mapel = $nilai->jadwalMengajar?->mapel;

        if (!$mapel) {
            continue;
        }

        $namaMapel = trim($mapel->nama_mapel);

        $kategori = strtolower(
            trim($mapel->kategori_mapel ?? '')
        );

        $key = $namaMapel . '_' . $kategori;

        if (!isset($nilaiPerMapel[$key])) {

            $nilaiPerMapel[$key] = [
                'nama_mapel' => $namaMapel,
                'kategori'   => $kategori,
                'nilai'      => array_fill(0, 6, '-')
            ];
        }

        $nilaiPerMapel[$key]['nilai'][$kolomIndex] =
            $nilai->nilai_akhir ?? '-';
    }

    $excluded = $siswa->getMapelExcluded();

    $mapelWajib = collect($nilaiPerMapel)
        ->filter(fn ($item) =>
            $item['kategori'] === 'wajib'
            &&
            !in_array(
                $item['nama_mapel'],
                $excluded
            )
        )
        ->values();

    $mapelPilihan = collect($nilaiPerMapel)
        ->filter(fn ($item) =>
            $item['kategori'] === 'pilihan'
            &&
            !in_array(
                $item['nama_mapel'],
                $excluded
            )
        )
        ->values();

    /*
    |--------------------------------------------------------------------------
    | NILAI EKSKUL 6 SEMESTER
    |--------------------------------------------------------------------------
    */

    $nilaiEkskul = [];

    $semuaEkskul = NilaiEkskul::with([
        'ekstrakurikuler',
        'kelasAktif.kelas',
        'kelasAktif.semester'
    ])
    ->where('id_siswa', $id)
    ->get();

    foreach ($semuaEkskul as $item) {

        $kelasAktif = $item->kelasAktif;

        if (!$kelasAktif) {
            continue;
        }

        $namaKelas = strtoupper(
            $kelasAktif->kelas->nama_kelas ?? ''
        );

        $isGanjil =
            ($kelasAktif->semester->nama_semester ?? '')
            === 'Ganjil';

        if (str_starts_with($namaKelas, 'XII')) {

            $kolom = $isGanjil ? 4 : 5;

        } elseif (str_starts_with($namaKelas, 'XI')) {

            $kolom = $isGanjil ? 2 : 3;

        } else {

            $kolom = $isGanjil ? 0 : 1;
        }

        $namaEkskul =
            $item->ekstrakurikuler->nama_ekskul ?? '-';

        if (!isset($nilaiEkskul[$namaEkskul])) {

            $nilaiEkskul[$namaEkskul] = [
                'nama'  => $namaEkskul,
                'nilai' => array_fill(0, 6, '-')
            ];
        }

        $nilaiEkskul[$namaEkskul]['nilai'][$kolom]
            = $item->nilai;
    }

    /*
    |--------------------------------------------------------------------------
    | KEHADIRAN 6 SEMESTER
    |--------------------------------------------------------------------------
    */

    $kehadiran = [
        'sakit' => array_fill(0, 6, '-'),
        'izin'  => array_fill(0, 6, '-'),
        'alpa'  => array_fill(0, 6, '-'),
    ];

    $kelasAktifSiswa = $semuaNilai
        ->pluck('jadwalMengajar.kelasAktif')
        ->filter()
        ->unique('id_kelas_aktif');

    foreach ($kelasAktifSiswa as $kelasAktif) {

        $namaKelas = strtoupper(
            $kelasAktif->kelas->nama_kelas ?? ''
        );

        $isGanjil =
            ($kelasAktif->semester->nama_semester ?? '')
            === 'Ganjil';

        if (str_starts_with($namaKelas, 'XII')) {

            $kolom = $isGanjil ? 4 : 5;

        } elseif (str_starts_with($namaKelas, 'XI')) {

            $kolom = $isGanjil ? 2 : 3;

        } elseif (str_starts_with($namaKelas, 'X')) {

            $kolom = $isGanjil ? 0 : 1;

        } else {

            continue;
        }

        $jadwalIds = JadwalMengajar::where(
                'id_kelas_aktif',
                $kelasAktif->id_kelas_aktif
            )
            ->pluck('id_jadwal');

        $kehadiran['sakit'][$kolom] = Kehadiran::where('id_siswa', $id)
            ->whereIn('id_jadwal', $jadwalIds)
            ->where('status', 'sakit')
            ->count();

        $kehadiran['izin'][$kolom] = Kehadiran::where('id_siswa', $id)
            ->whereIn('id_jadwal', $jadwalIds)
            ->where('status', 'izin')
            ->count();

        $kehadiran['alpa'][$kolom] = Kehadiran::where('id_siswa', $id)
            ->whereIn('id_jadwal', $jadwalIds)
            ->where('status', 'alpa')
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | KENAIKAN / KELULUSAN
    |--------------------------------------------------------------------------
    */

    $kenaikanKelas = KenaikanKelas::with('tahunAjaran')
        ->where('id_siswa', $id)
        ->orderBy('id_tahun')
        ->get();

    $naikKelas = $kenaikanKelas
        ->where('status', 'naik')
        ->values();

    $lulus = $kenaikanKelas
        ->where('status', 'lulus')
        ->first();

    return view(
        'admin.rekap.rekap_siswa',
        compact(
            'menu',
            'siswa',
            'tahunHeaders',
            'mapelWajib',
            'mapelPilihan',
            'nilaiEkskul',
            'kehadiran',
            'kenaikanKelas',
            'naikKelas',
            'lulus'
        )
    );
}

    /**
     * Download rekap sebagai PDF (dengan cover)
     */
    public function downloadPdf($id)
    {
        $siswa = Siswa::findOrFail($id);
        $profil = ProfilSekolah::first();

        $kelasData = AcademicRecord::riwayatTerpilih($siswa, request('kelas_aktif'));
        $selectedKelasAktifId = $kelasData?->id_kelas_aktif;
        $nilaiAkademik = AcademicRecord::nilaiAkademik($id, $selectedKelasAktifId, $siswa->getMapelExcluded());
        $nilaiEkskul = AcademicRecord::nilaiEkskul($id, $selectedKelasAktifId);
        $kehadiran = AcademicRecord::kehadiran($id, $selectedKelasAktifId);

        // Render cover
        $coverHtml = view('admin.profil_sekolah.cover_pdf', [
            'namaSekolah' => $profil?->nama_sekolah ?? 'SMA Negeri 3 Cilacap',
            'namaKelas' => $kelasData?->kelasAktif?->kelas?->nama_kelas ?? null,
            'namaSiswa' => $siswa->nama_lengkap,
            'nis' => $siswa->nis,
        ])->render();

        // Render isi rekap
        $contentHtml = view('admin.rekap.rekap_pdf', compact(
            'siswa', 'profil', 'kelasData', 'nilaiAkademik', 'nilaiEkskul', 'kehadiran'
        ))->render();

        // Gabungkan
        $coverBody = preg_match('/<body[^>]*>(.*?)<\/body>/is', $coverHtml, $m1) ? $m1[1] : $coverHtml;
        $contentBody = preg_match('/<body[^>]*>(.*?)<\/body>/is', $contentHtml, $m2) ? $m2[1] : $contentHtml;
        preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $coverHtml, $sc);
        preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $contentHtml, $sct);
        $coverStyle = implode("\n", $sc[1] ?? []);
        $contentStyle = implode("\n", $sct[1] ?? []);

        $fullHtml = "<!DOCTYPE html><html><head><meta charset='utf-8'><style>{$coverStyle}{$contentStyle}.page-break{page-break-before:always;}</style></head><body>{$coverBody}<div class='page-break'></div>{$contentBody}</body></html>";

        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($fullHtml);
        $pdf->setPaper('A4');

        return $pdf->download('rekap_nilai_' . $siswa->nama_lengkap . '.pdf');
    }
}
