<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\KelasAktif;
use App\Models\NilaiMapel;
use App\Models\NilaiEkskul;
use App\Models\Kehadiran;
use App\Models\ProfilSekolah;
use Illuminate\Support\Facades\DB;


class OrtuExportController extends Controller
{
    public function exportNilaiOrtu($id_siswa)
{
    $siswa = Siswa::findOrFail($id_siswa);
    $profil = ProfilSekolah::first();

    // Ambil kelas aktif siswa saat ini
        $kelasData = SiswaKelas::with([
        'kelasAktif.kelas',
        'kelasAktif.semester',
        'kelasAktif.tahunAjaran'
    ])
    ->where('id_siswa', $id_siswa)
    ->orderByDesc('id_siswa_kelas')
    ->first();

    $idKelasAktif = $kelasData->id_kelas_aktif;

    // Nilai akademik semester aktif saja
    $nilaiAkademik = DB::table('nilai_mapel')
        ->join('jadwal_mengajar', 'nilai_mapel.id_jadwal', '=', 'jadwal_mengajar.id_jadwal')
        ->join('mata_pelajaran', 'jadwal_mengajar.id_mapel', '=', 'mata_pelajaran.id_mapel')
        ->where('nilai_mapel.id_siswa', $id_siswa)
        ->where('jadwal_mengajar.id_kelas_aktif', $idKelasAktif)
        ->select(
            'mata_pelajaran.id_mapel',
            'mata_pelajaran.nama_mapel',
            'mata_pelajaran.kategori_mapel',
            'nilai_mapel.tugas1',
            'nilai_mapel.tugas2',
            'nilai_mapel.tugas3',
            'nilai_mapel.tugas4',
            'nilai_mapel.tugas5',
            'nilai_mapel.uts',
            'nilai_mapel.uas',
            'nilai_mapel.nilai_akhir'
        )
        ->orderBy('mata_pelajaran.id_mapel')
        ->get();

    // Filter mapel sesuai jurusan
    $excluded = $siswa->getMapelExcluded();

    $nilaiAkademik = $nilaiAkademik->whereNotIn(
        'nama_mapel',
        $excluded
    );

    // Kelompok mapel
    $nilaiWajib = $nilaiAkademik->filter(
        fn($n) => str_starts_with($n->id_mapel, 'MW')
    );

    $nilaiPilihan = $nilaiAkademik->filter(
        fn($n) => str_starts_with($n->id_mapel, 'MP')
    );

    // Ekskul
    $nilaiEkskul = NilaiEkskul::with('ekstrakurikuler')
        ->where('id_siswa', $id_siswa)
        ->get();

    // Kehadiran
    $kehadiran = (object) [
        'sakit' => Kehadiran::where('id_siswa', $id_siswa)
            ->where('status', 'sakit')
            ->count(),

        'izin' => Kehadiran::where('id_siswa', $id_siswa)
            ->where('status', 'izin')
            ->count(),

        'alpa' => Kehadiran::where('id_siswa', $id_siswa)
            ->where('status', 'alpa')
            ->count(),
    ];

    // Generate PDF (tanpa cover)
    $pdf = app('dompdf.wrapper');

    $pdf->loadView(
        'orangtua.export.laporan_nilai_pdf',
        compact(
            'siswa',
            'profil',
            'kelasData',
            'nilaiWajib',
            'nilaiPilihan',
            'nilaiEkskul',
            'kehadiran'
        )
    );

    $pdf->setPaper('A4', 'portrait');

    return $pdf->download(
        'Laporan_Hasil_Belajar_' . $siswa->nis . '.pdf'
    );
}
}
