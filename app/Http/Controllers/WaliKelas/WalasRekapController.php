<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\KelasAktif;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\NilaiEkskul;
use App\Models\Kehadiran;
use App\Support\AcademicRecord;

/**
 * WalasRekapController — Rekap Rapor Siswa (Wali Kelas)
 *
 * Fitur:
 * - List siswa di kelas wali
 * - Rekap lengkap per siswa: nilai akademik (wajib + pilihan) + ekskul + kehadiran
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 */
class WalasRekapController extends Controller
{
    public function index()
{
    $menu = "siswa";

    $id_guru = session('id_guru');

    $kelas = KelasAktif::with([
            'kelas',
            'tahunAjaran',
            'semester'
        ])
        ->where('id_guru', $id_guru)
        ->whereHas('siswaKelas')
        ->orderByDesc('id_tahun')
        ->orderByDesc('id_semester')
        ->first();

    $siswa = collect();

    if ($kelas) {

        $siswa = SiswaKelas::with([
                'siswa',
                'kelasAktif.kelas'
            ])
            ->where(
                'id_kelas_aktif',
                $kelas->id_kelas_aktif
            )
            ->get()
            ->filter(fn($item) => $item->siswa)
            ->map(function ($item) {

                return (object) [

                    'id_siswa'      => $item->siswa->id_siswa,
                    'nis'           => $item->siswa->nis,
                    'nama_lengkap'  => $item->siswa->nama_lengkap,
                    'jenis_kelamin' => $item->siswa->jenis_kelamin,

                    'nama_kelas'    =>
                        $item->kelasAktif->kelas->nama_kelas ?? '-',

                ];

            })
            ->sortBy('nama_lengkap')
            ->values();
    }

    return view(
        'wali-kelas.rekap.index_rekap',
        compact(
            'siswa',
            'menu',
            'kelas'
        )
    );
}

    public function rekap($id)
{
    $menu = "rekap";

    $siswa = Siswa::findOrFail($id);

    /*
    |--------------------------------------------------------------------------
    | RIWAYAT KELAS
    |--------------------------------------------------------------------------
    */

    $riwayat = AcademicRecord::riwayatSiswa($id);

    /*
    |--------------------------------------------------------------------------
    | KELAS TERPILIH
    |--------------------------------------------------------------------------
    */

    if (request()->filled('kelas_aktif')) {

        $kelasData = $riwayat->firstWhere(
            'id_kelas_aktif',
            request('kelas_aktif')
        );

    } else {

        $kelasData = SiswaKelas::with([
                'kelasAktif.kelas',
                'kelasAktif.semester',
                'kelasAktif.tahunAjaran'
            ])
            ->where('id_siswa', $id)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | INFO KELAS
    |--------------------------------------------------------------------------
    */

    $kelas_walas = null;

    if ($kelasData) {

        $kelasAktif = $kelasData->kelasAktif;

        $kelas_walas = (object) [

            'nama_kelas' =>
                $kelasAktif?->kelas?->nama_kelas ?? '-',

            'id_kelas_aktif' =>
                $kelasAktif?->id_kelas_aktif,

            'nama_semester' =>
                $kelasAktif?->semester?->nama_semester ?? '-',

            'tahun' =>
                $kelasAktif?->tahunAjaran?->tahun ?? '-',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | DATA AKADEMIK
    |--------------------------------------------------------------------------
    */

    $nilai_all = collect();
    $ekskul = collect();

    $absen = (object) [
        'sakit' => 0,
        'izin'  => 0,
        'alpa'  => 0,
    ];

    if ($kelas_walas && $kelas_walas->id_kelas_aktif) {

        $nilai_all = AcademicRecord::nilaiAkademik(
            $id,
            $kelas_walas->id_kelas_aktif,
            $siswa->getMapelExcluded()
        );

        $ekskul = AcademicRecord::nilaiEkskul(
            $id,
            $kelas_walas->id_kelas_aktif
        );

        $absen = AcademicRecord::kehadiran(
            $id,
            $kelas_walas->id_kelas_aktif
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FALLBACK RIWAYAT
    |--------------------------------------------------------------------------
    */

    if ($nilai_all->isEmpty() && $riwayat->count()) {

        foreach ($riwayat as $item) {

            $nilai = AcademicRecord::nilaiAkademik(
                $id,
                $item->id_kelas_aktif,
                $siswa->getMapelExcluded()
            );

            if ($nilai->isNotEmpty()) {

                $nilai_all = $nilai;

                $ekskul = AcademicRecord::nilaiEkskul(
                    $id,
                    $item->id_kelas_aktif
                );

                $absen = AcademicRecord::kehadiran(
                    $id,
                    $item->id_kelas_aktif
                );

                $kelas_walas = (object) [

                    'nama_kelas' =>
                        $item->kelasAktif?->kelas?->nama_kelas ?? '-',

                    'id_kelas_aktif' =>
                        $item->id_kelas_aktif,

                    'nama_semester' =>
                        $item->kelasAktif?->semester?->nama_semester ?? '-',

                    'tahun' =>
                        $item->kelasAktif?->tahunAjaran?->tahun ?? '-',
                ];

                break;
            }
        }
    }

    [
        'wajib'   => $nilai_wajib,
        'pilihan' => $nilai_pilihan
    ] = AcademicRecord::kelompokNilai($nilai_all);

    return view(
        'wali-kelas.rekap.rekap_walas',
        compact(
            'menu',
            'siswa',
            'kelas_walas',
            'nilai_wajib',
            'nilai_pilihan',
            'ekskul',
            'absen',
            'riwayat'
        )
    );
}
}
