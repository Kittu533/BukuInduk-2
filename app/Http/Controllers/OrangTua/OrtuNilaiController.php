<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\NilaiEkskul;
use App\Models\Kehadiran;
use App\Support\AcademicRecord;

/**
 * OrtuNilaiController — Lihat Nilai Anak (Orang Tua)
 *
 * Fitur:
 * - Nilai akademik (wajib + pilihan) per semester aktif
 * - Nilai ekstrakurikuler
 * - Ringkasan kehadiran (sakit/izin/alpa)
 *
 * CATATAN: Orang tua hanya bisa READ, tidak bisa edit.
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 */
class OrtuNilaiController extends Controller
{
    public function index()
    {
        $menu = "nilai";
        $id = session('id_siswa');

        $siswa = Siswa::findOrFail($id);
        $riwayat = AcademicRecord::riwayatSiswa($id);
        $kelasData = AcademicRecord::riwayatTerpilih($siswa, request('kelas_aktif'));

        $kelas_walas = $kelasData ? (object) [
            'nama_kelas' => $kelasData->kelasAktif->kelas->nama_kelas,
            'id_kelas_aktif' => $kelasData->kelasAktif->id_kelas_aktif,
            'nama_semester' => $kelasData->kelasAktif->semester->nama_semester,
            'tahun' => $kelasData->kelasAktif->tahunAjaran->tahun,
        ] : null;

        $nilai_all = AcademicRecord::nilaiAkademik($id, $kelas_walas?->id_kelas_aktif, $siswa->getMapelExcluded());
        ['wajib' => $nilai_wajib, 'pilihan' => $nilai_pilihan] = AcademicRecord::kelompokNilai($nilai_all);
         $ekskul = AcademicRecord::nilaiEkskul(
            $id,
            $kelas_walas?->id_kelas_aktif
        )
        ->map(function ($ne) {

            return (object) [
                'nama_ekskul' =>
                    $ne->ekstrakurikuler->nama_ekskul ?? '-',

                'nilai' =>
                    $ne->nilai,
            ];

        });
            
        $absen = AcademicRecord::kehadiran($id, $kelas_walas?->id_kelas_aktif);

        return view('orangtua.nilai.index_nilai', compact(
            'menu', 'siswa', 'kelas_walas', 'nilai_wajib', 'nilai_pilihan', 'ekskul', 'absen', 'riwayat'
        ));
    }
}
