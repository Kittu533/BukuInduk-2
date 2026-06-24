<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\KelasAktif;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\Kehadiran;

/**
 * WalasKehadiranController — Pantau Kehadiran Siswa (Wali Kelas)
 *
 * Fitur:
 * - List siswa di kelas wali
 * - Detail riwayat kehadiran per siswa
 *
 * CATATAN: Wali kelas hanya bisa MELIHAT data kehadiran.
 * Input kehadiran dilakukan oleh guru mapel.
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 */
class WalasKehadiranController extends Controller
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
        'wali-kelas.kehadiran.index_absen',
        compact(
            'siswa',
            'menu',
            'kelas'
        )
    );
}

    public function detail($id_siswa)
{
    $menu = "siswa";

    $id_guru = session('id_guru');

    $kelasAktif = KelasAktif::with([
            'kelas',
            'tahunAjaran',
            'semester'
        ])
        ->where('id_guru', $id_guru)
        ->whereHas('siswaKelas')
        ->orderByDesc('id_tahun')
        ->orderByDesc('id_semester')
        ->firstOrFail();

    $detail = (object) [

        'nama_kelas' =>
            $kelasAktif->kelas->nama_kelas,

        'tahun' =>
            $kelasAktif->tahunAjaran->tahun,

        'nama_semester' =>
            $kelasAktif->semester->nama_semester,

    ];

    $siswa = Siswa::findOrFail($id_siswa);

    $kehadiran = Kehadiran::with([
            'jadwalMengajar.mapel'
        ])
        ->where('id_siswa', $id_siswa)

        ->whereHas(
            'jadwalMengajar',
            function ($q) use ($kelasAktif) {

                $q->where(
                    'id_kelas_aktif',
                    $kelasAktif->id_kelas_aktif
                );

            }
        )

        ->orderByDesc('tanggal')
        ->get()

        ->map(function ($k) {

            return (object) [

                'tanggal' =>
                    $k->tanggal,

                'status' =>
                    ucfirst($k->status),

                'nama_mapel' =>
                    $k->jadwalMengajar->mapel->nama_mapel ?? '-',

            ];

        });

    return view(
        'wali-kelas.kehadiran.detail_absen',
        compact(
            'siswa',
            'kehadiran',
            'detail',
            'menu'
        )
    );
}
}
