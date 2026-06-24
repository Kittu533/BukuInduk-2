<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\KelasAktif;
use App\Models\SiswaKelas;
use App\Models\Siswa;
use App\Support\AcademicRecord;

/**
 * WalasSiswaController — Data Siswa per Kelas Wali
 *
 * Fitur:
 * - List siswa di kelas yang diwalikan
 * - Detail buku induk per siswa
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 *         Fix: detail() sebelumnya join ke 'siswa.id_kelas' yang tidak ada,
 *         sekarang menggunakan Siswa::with('kelasAktif.kelas')
 */
class WalasSiswaController extends Controller
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

                    'id_siswa'       => $item->siswa->id_siswa,
                    'nis'            => $item->siswa->nis,
                    'nama_lengkap'   => $item->siswa->nama_lengkap,
                    'jenis_kelamin'  => $item->siswa->jenis_kelamin,
                    'nama_kelas'     => $item->kelasAktif->kelas->nama_kelas ?? '-',

                ];

            })
            ->sortBy('nama_lengkap')
            ->values();

    }

    return view(
        'wali-kelas.siswa.index_walas',
        compact(
            'siswa',
            'menu',
            'kelas'
        )
    );
}

    public function detail($id)
{
    $menu = "siswa";

    $siswa = Siswa::findOrFail($id);

    $kelasSiswa = SiswaKelas::with([
        'kelasAktif.kelas'
    ])
    ->where('id_siswa', $id)
    ->first();

    $siswa->nama_kelas =
        $kelasSiswa?->kelasAktif?->kelas?->nama_kelas ?? '-';

    $riwayat = AcademicRecord::riwayatSiswa($id);

    return view(
        'wali-kelas.siswa.detail_siswa_walas',
        compact(
            'siswa',
            'menu',
            'riwayat'
        )
    );
}
}
