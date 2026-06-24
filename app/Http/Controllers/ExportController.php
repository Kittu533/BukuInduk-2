<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\KelasAktif;
use App\Models\NilaiMapel;
use App\Models\NilaiEkskul;
use App\Models\Kehadiran;
use App\Models\ProfilSekolah;
use App\Support\AcademicRecord;
use Illuminate\Support\Facades\DB;

/**
 * ExportController — Export PDF (Admin & Orang Tua)
 *
 * Fitur:
 * - Export data siswa per kelas (PDF dengan cover)
 * - Export nilai lengkap per siswa (PDF dengan cover)
 *
 * Setiap PDF wajib ada cover di halaman pertama.
 *
 * Dependency: barryvdh/laravel-dompdf
 *
 * @legacy Fitur baru (30 Mei 2026)
 */
class ExportController extends Controller
{
    /**
     * Export data siswa per kelas (PDF) dengan cover
     */
    public function exportSiswa($id_kelas_aktif)
    {
        $kelasAktif = KelasAktif::with(['kelas', 'guru'])->findOrFail($id_kelas_aktif);

        $siswa = SiswaKelas::with('siswa')
            ->where('id_kelas_aktif', $id_kelas_aktif)
            ->get()
            ->map(fn($sk) => $sk->siswa)
            ->sortBy('nama_lengkap')
            ->values();

        $profil = ProfilSekolah::first();

        // Render cover dengan info kelas
        $coverHtml = view('admin.profil_sekolah.cover_pdf', [
            'namaSekolah' => $profil?->nama_sekolah ?? 'SMA Negeri 3 Cilacap',
            'namaKelas' => $kelasAktif->kelas->nama_kelas ?? null,
            'namaSiswa' => null,
            'nis' => null,
        ])->render();

        // Render isi PDF
        $contentHtml = view('admin.export.siswa_pdf', compact('kelasAktif', 'siswa', 'profil'))->render();

        // Gabungkan cover + content
        $fullHtml = $this->mergeCoverWithContent($coverHtml, $contentHtml);

        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($fullHtml);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('data_siswa_' . ($kelasAktif->kelas->nama_kelas ?? 'kelas') . '.pdf');
    }

    /**
     * Export nilai siswa (PDF) dengan cover — untuk admin dan orang tua
     */
    public function exportNilaiSiswa($id_siswa)
    {
        $siswa = Siswa::findOrFail($id_siswa);
        $profil = ProfilSekolah::first();

        $kelasData = AcademicRecord::riwayatTerpilih($siswa, request('kelas_aktif'));
        $selectedKelasAktifId = $kelasData?->id_kelas_aktif;
        $nilaiAkademik = AcademicRecord::nilaiAkademik($id_siswa, $selectedKelasAktifId, $siswa->getMapelExcluded());
        ['wajib' => $nilaiWajib, 'pilihan' => $nilaiPilihan] = AcademicRecord::kelompokNilai($nilaiAkademik);
        $nilaiEkskul = AcademicRecord::nilaiEkskul($id_siswa, $selectedKelasAktifId);
        $kehadiran = AcademicRecord::kehadiran($id_siswa, $selectedKelasAktifId);

        // Render cover dengan info siswa
        $coverHtml = view('admin.profil_sekolah.cover_pdf', [
            'namaSekolah' => $profil?->nama_sekolah ?? 'SMA Negeri 3 Cilacap',
            'namaKelas' => $kelasData?->kelasAktif?->kelas?->nama_kelas ?? null,
            'namaSiswa' => $siswa->nama_lengkap,
            'nis' => $siswa->nis,
        ])->render();

        // Render isi PDF
        $contentHtml = view('admin.export.nilai_siswa_pdf', compact(
            'siswa', 'profil', 'kelasData', 'nilaiWajib', 'nilaiPilihan', 'nilaiEkskul', 'kehadiran'
        ))->render();

        // Gabungkan cover + content
        $fullHtml = $this->mergeCoverWithContent($coverHtml, $contentHtml);

        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($fullHtml);
        $pdf->setPaper('A4');

        return $pdf->download('nilai_' . $siswa->nama_lengkap . '.pdf');
    }

    public function exportRekapHasilBelajar($id_siswa)
{
    $siswa = Siswa::findOrFail($id_siswa);
    $profil = ProfilSekolah::first();

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
    ->where('id_siswa', $id_siswa)
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
            ($kelasAktif->semester->nama_semester ?? '') === 'Ganjil';

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
        ->where('kategori', 'wajib')
        ->filter(fn ($item) =>
            !in_array($item['nama_mapel'], $excluded))
        ->values();

    $mapelPilihan = collect($nilaiPerMapel)
        ->where('kategori', 'pilihan')
        ->filter(fn ($item) =>
            !in_array($item['nama_mapel'], $excluded))
        ->values();

    /*
    |--------------------------------------------------------------------------
    | EKSTRAKURIKULER
    |--------------------------------------------------------------------------
    */

    $nilaiEkskulData = NilaiEkskul::with([
        'ekstrakurikuler',
        'kelasAktif.kelas',
        'kelasAktif.semester'
    ])
    ->where('id_siswa', $id_siswa)
    ->get();

    $daftarEkskul = $nilaiEkskulData
        ->pluck('ekstrakurikuler')
        ->filter()
        ->unique('id_ekskul')
        ->values();

    $nilaiEkskulPerSemester = [];

    foreach ($daftarEkskul as $ekskul) {

        $nilaiEkskulPerSemester[$ekskul->id_ekskul] =
            array_fill(0, 6, '-');
    }

    foreach ($nilaiEkskulData as $item) {

        if (
            !$item->kelasAktif ||
            !$item->kelasAktif->kelas ||
            !$item->kelasAktif->semester
        ) {
            continue;
        }

        $namaKelas = strtoupper(
            $item->kelasAktif->kelas->nama_kelas
        );

        $semester = strtolower(
            $item->kelasAktif->semester->nama_semester
        );

        if (str_starts_with($namaKelas, 'XII')) {

            $kolom = $semester == 'ganjil'
                ? 4
                : 5;

        } elseif (str_starts_with($namaKelas, 'XI')) {

            $kolom = $semester == 'ganjil'
                ? 2
                : 3;

        } elseif (str_starts_with($namaKelas, 'X')) {

            $kolom = $semester == 'ganjil'
                ? 0
                : 1;

        } else {

            continue;
        }

        $nilaiEkskulPerSemester[
            $item->id_ekskul
        ][$kolom] = $item->nilai;
    }

    /*
    |--------------------------------------------------------------------------
    | KEHADIRAN
    |--------------------------------------------------------------------------
    */

    $kehadiranPerSemester = [];

    for ($i = 0; $i < 6; $i++) {

        $kehadiranPerSemester[$i] = [
            'sakit' => '-',
            'izin'  => '-',
            'alpa'  => '-',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | KENAIKAN KELAS
    |--------------------------------------------------------------------------
    */

    $kenaikan = [
        'NAIK KE KELAS XI',
        'NAIK KE KELAS XII',
        'LULUS'
    ];

    /*
    |--------------------------------------------------------------------------
    | PDF
    |--------------------------------------------------------------------------
    */

    $pdf = app('dompdf.wrapper');

    $pdf->loadView(
        'admin.export.rekap_hasil_belajar_pdf',
        compact(
            'siswa',
            'profil',
            'tahunHeaders',
            'mapelWajib',
            'mapelPilihan',
            'daftarEkskul',
            'nilaiEkskulPerSemester',
            'kehadiranPerSemester',
            'kenaikan'
        )
    );

    $pdf->setPaper('A4', 'portrait');

    return $pdf->download(
        'rekap_hasil_belajar_' . $siswa->nis . '.pdf'
    );
}

    /**
     * Gabungkan cover HTML dengan content HTML
     * Cover di page 1, content di page 2+
     */
    private function mergeCoverWithContent(string $cover, string $content): string
    {
        // Ekstrak <body> dari masing-masing
        $coverBody = $this->extractBody($cover);
        $contentBody = $this->extractBody($content);

        // Ekstrak <style> dari masing-masing
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
