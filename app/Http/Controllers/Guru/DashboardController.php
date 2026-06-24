<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JadwalMengajar;
use App\Models\KelasAktif;
use App\Models\SiswaKelas;
use App\Models\NilaiEkskul;
use App\Models\Ekstrakurikuler;

class DashboardController extends Controller
{
    public function index()
    {
        $idGuru = session('id_guru');

        $isGuruMapel = (bool) session('is_guru_mapel');
        $isWali      = (bool) session('is_wali');
        $isPembina   = (bool) session('is_pembina');

        /*
        |--------------------------------------------------------------------------
        | GURU MAPEL
        |--------------------------------------------------------------------------
        */

        $totalKelasDiampu = 0;
        $totalSiswaDiampu = 0;

        if ($isGuruMapel) {

            $kelasDiampu = JadwalMengajar::where(
                    'id_guru',
                    $idGuru
                )
                ->pluck('id_kelas_aktif')
                ->unique();

            $totalKelasDiampu = $kelasDiampu->count();

            $totalSiswaDiampu = SiswaKelas::join(
                    'siswa',
                    'siswa_kelas.id_siswa',
                    '=',
                    'siswa.id_siswa'
                )
                ->whereIn(
                    'siswa_kelas.id_kelas_aktif',
                    $kelasDiampu
                )
                ->where(function ($q) {
                    $q->where('siswa.id_siswa', 'LIKE', 'S25%')
                      ->orWhere('siswa.id_siswa', 'LIKE', 'S26%')
                      ->orWhere('siswa.id_siswa', 'LIKE', 'S27%');
                })
                ->distinct()
                ->count('siswa.id_siswa');
        }

        /*
        |--------------------------------------------------------------------------
        | WALI KELAS
        |--------------------------------------------------------------------------
        */

        $kelasWali = null;
        $totalSiswaWali = 0;

        if ($isWali) {

            $kelasWali = KelasAktif::where(
                    'id_guru',
                    $idGuru
                )
                ->orderByDesc('id_kelas_aktif')
                ->first();

            if ($kelasWali) {

                $totalSiswaWali = SiswaKelas::where(
                        'id_kelas_aktif',
                        $kelasWali->id_kelas_aktif
                    )
                    ->distinct()
                    ->count('id_siswa');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PEMBINA EKSKUL
        |--------------------------------------------------------------------------
        */

        $totalAnggotaEkskul = 0;

        if ($isPembina) {

            $ekskul = Ekstrakurikuler::where(
                    'id_guru',
                    $idGuru
                )
                ->first();

            if ($ekskul) {

                $totalAnggotaEkskul = NilaiEkskul::join(
                        'siswa',
                        'nilai_ekskul.id_siswa',
                        '=',
                        'siswa.id_siswa'
                    )
                    ->where(
                        'nilai_ekskul.id_ekskul',
                        $ekskul->id_ekskul
                    )
                    ->where(function ($q) {
                        $q->where('siswa.id_siswa', 'LIKE', 'S25%')
                          ->orWhere('siswa.id_siswa', 'LIKE', 'S26%')
                          ->orWhere('siswa.id_siswa', 'LIKE', 'S27%');
                    })
                    ->distinct()
                    ->count('siswa.id_siswa');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | REMINDER
        |--------------------------------------------------------------------------
        */

        $detailNilaiBelumLengkap = collect();
        $detailSiswaBolos        = collect();
        $detailNilaiKosong       = collect();

        return view(
            'guru.dashboard.dashboard',
            compact(
                'isGuruMapel',
                'isWali',
                'isPembina',

                'totalKelasDiampu',
                'totalSiswaDiampu',

                'kelasWali',
                'totalSiswaWali',

                'totalAnggotaEkskul',

                'detailNilaiBelumLengkap',
                'detailSiswaBolos',
                'detailNilaiKosong'
            )
        );
    }
}