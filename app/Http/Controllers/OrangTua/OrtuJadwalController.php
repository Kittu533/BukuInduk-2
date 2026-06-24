<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\JadwalMengajar;

/**
 * OrtuJadwalController — Lihat Jadwal Pelajaran Anak (Orang Tua)
 *
 * Fitur:
 * - Menampilkan jadwal mata pelajaran & guru pengajar
 *   berdasarkan kelas aktif anak
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 */
class OrtuJadwalController extends Controller
{
    public function index()
    {
        $menu = "jadwal";
        $id_siswa = session('id_siswa');

        $siswa = Siswa::findOrFail($id_siswa);

        // Ambil kelas siswa pada tahun ajaran yang aktif
        $kelasData = SiswaKelas::with([
            'kelasAktif.kelas',
            'kelasAktif.tahunAjaran',
            'kelasAktif.semester'
        ])
        ->where('id_siswa', $id_siswa)
        ->whereHas('kelasAktif.tahunAjaran', function ($q) {
            $q->where('status', 'aktif');
        })
        ->first();

        $kelasAktif = null;
        $jadwal = collect();

        if ($kelasData && $kelasData->kelasAktif) {

            $kelasAktif = (object) [
                'id_kelas_aktif' => $kelasData->kelasAktif->id_kelas_aktif,
                'nama_kelas'     => $kelasData->kelasAktif->kelas->nama_kelas ?? '-',
                'tahun'          => $kelasData->kelasAktif->tahunAjaran->tahun ?? '-',
                'nama_semester'  => $kelasData->kelasAktif->semester->nama_semester ?? '-',
            ];

            $jadwal = JadwalMengajar::with([
                    'mapel',
                    'guru'
                ])
                ->where('id_kelas_aktif', $kelasAktif->id_kelas_aktif)
                ->orderBy('id_jadwal')
                ->get()
                ->map(function ($j) {
                    return (object) [
                        'nama_mapel' => $j->mapel->nama_mapel ?? '-',
                        'nama_guru'  => $j->guru->nama_guru ?? '-',
                    ];
                });
        }

        return view(
            'orangtua.jadwal.index_jadwal',
            compact(
                'menu',
                'siswa',
                'kelasAktif',
                'jadwal'
            )
        );
    }
}
