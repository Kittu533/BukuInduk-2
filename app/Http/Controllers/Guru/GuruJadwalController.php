<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JadwalMengajar;
use App\Models\SiswaKelas;
use App\Models\KelasAktif;
use App\Models\Semester;
use App\Models\TahunAjaran;

/**
 * GuruJadwalController — Jadwal Mengajar Guru
 *
 * Menampilkan daftar jadwal mengajar guru yang login,
 * beserta detail siswa per kelas.
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 */
class GuruJadwalController extends Controller
{
    public function index()
    {
        $idGuru = session('id_guru');
        $tahunAktif = TahunAjaran::aktif()->firstOrFail();
        $semesterAktif = Semester::aktif()->firstOrFail();

        $jadwal = JadwalMengajar::with([
                'kelasAktif.kelas',
                'mapel'
            ])
            ->where('id_guru', $idGuru)
            ->whereHas('kelasAktif', function ($q) use ($tahunAktif, $semesterAktif) {
                $q->where('id_tahun', $tahunAktif->id_tahun)
                    ->whereHas('semester', function ($semesterQuery) use ($semesterAktif) {
                        $semesterQuery->where('nama_semester', $semesterAktif->nama_semester);
                    });
            })
            ->get();

        return view(
            'guru.jadwal.index_jadwal',
            compact('jadwal')
        );
    }

    public function show($id)
    {
        $idGuru = session('id_guru');

        $jadwal = JadwalMengajar::with(['kelasAktif.kelas', 'mapel'])
            ->where('id_jadwal', $id)
            ->where('id_guru', $idGuru)
            ->firstOrFail();

        $detail = (object) [
            'id_jadwal' => $jadwal->id_jadwal,
            'nama_kelas' => $jadwal->kelasAktif->kelas->nama_kelas,
            'nama_mapel' => $jadwal->mapel->nama_mapel,
            'id_kelas_aktif' => $jadwal->kelasAktif->id_kelas_aktif,
        ];

        $siswa = SiswaKelas::with('siswa')
            ->where('id_kelas_aktif', $detail->id_kelas_aktif)
            ->get()
            ->map(fn($sk) => (object) [
                'id_siswa' => $sk->siswa->id_siswa,
                'nama_lengkap' => $sk->siswa->nama_lengkap,
            ]);
            
        return view('guru.jadwal.detail_jadwal', compact('detail', 'siswa'));
    }
}
