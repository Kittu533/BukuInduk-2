<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\KelasAktif;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\NilaiEkskul;
use App\Models\Ekstrakurikuler;
use Illuminate\Http\Request;

/**
 * WalasEkskulController — Kelola Nilai Ekstrakurikuler (Wali Kelas)
 *
 * Fitur:
 * - List siswa di kelas wali
 * - Detail nilai ekskul per siswa
 * - Input nilai ekskul baru (dengan cek duplikat)
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 *         Ditambahkan validasi request dan NilaiEkskul::generateId()
 */
class WalasEkskulController extends Controller
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

                $jumlahEkskul = NilaiEkskul::where(
                        'id_siswa',
                        $item->id_siswa
                    )
                    ->where(
                        'id_kelas_aktif',
                        $item->id_kelas_aktif
                    )
                    ->count();

                return (object) [

                    'id_siswa'      => $item->siswa->id_siswa,
                    'nis'           => $item->siswa->nis,
                    'nama_lengkap'  => $item->siswa->nama_lengkap,
                    'jenis_kelamin' => $item->siswa->jenis_kelamin,

                    'nama_kelas'    =>
                        $item->kelasAktif->kelas->nama_kelas ?? '-',

                    'jumlah_ekskul' =>
                        $jumlahEkskul,

                ];

            })
            ->sortBy('nama_lengkap')
            ->values();
    }

    return view(
        'wali-kelas.ekskul.index_ekskul',
        compact(
            'siswa',
            'menu',
            'kelas'
        )
    );
}

    public function detail($id)
{
    $menu = "ekskul";

    $siswa = Siswa::findOrFail($id);

    $kelasSiswa = SiswaKelas::with([
            'kelasAktif.kelas',
            'kelasAktif.semester',
            'kelasAktif.tahunAjaran'
        ])
        ->where('id_siswa', $id)
        ->first();

    $data = collect();

    if ($kelasSiswa) {

        $data = NilaiEkskul::with(
                'ekstrakurikuler'
            )
            ->where(
                'id_siswa',
                $id
            )
            ->where(
                'id_kelas_aktif',
                $kelasSiswa->id_kelas_aktif
            )
            ->orderBy('id_ekskul')
            ->get();
    }

    return view(
        'wali-kelas.ekskul.detail_ekskul',
        compact(
            'data',
            'menu',
            'siswa',
            'kelasSiswa'
        )
    );
}
}
