<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\NilaiMapel;
use Illuminate\Support\Facades\DB;

/**
 * AdminController — Dashboard Admin
 *
 * Menampilkan statistik keseluruhan:
 * - Total siswa, guru, kelas, mata pelajaran
 * - Distribusi gender siswa
 * - Rata-rata nilai per kelas
 * - Top 10 nilai tertinggi
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 */
class AdminController extends Controller
{
    public function dashboard()
    {
        $menu = "dashboard";

        /*
        |------------------------------------------------------------------
        | TOTAL DATA
        |------------------------------------------------------------------
        */
        $totalSiswa = Siswa::count();
        $totalGuru = Guru::count();
        $totalKelas = Kelas::count();
        $totalMapel = MataPelajaran::count();

        /*
        |------------------------------------------------------------------
        | GENDER SISWA
        |------------------------------------------------------------------
        */
        $gender = Siswa::selectRaw("
                CASE
                    WHEN LOWER(jenis_kelamin) IN ('l', 'laki-laki', 'laki laki') THEN 'Laki-laki'
                    WHEN LOWER(jenis_kelamin) IN ('p', 'perempuan') THEN 'Perempuan'
                    ELSE jenis_kelamin
                END as jenis_kelamin,
                COUNT(*) as total
            ")
            ->groupBy('jenis_kelamin')
            ->pluck('total', 'jenis_kelamin');

        /*
        |------------------------------------------------------------------
        | RATA-RATA NILAI PER KELAS
        |------------------------------------------------------------------
        */
        $rataNilaiKelas = DB::table('nilai_mapel')
            ->join('siswa', 'nilai_mapel.id_siswa', '=', 'siswa.id_siswa')
            ->join('siswa_kelas', 'siswa.id_siswa', '=', 'siswa_kelas.id_siswa')
            ->join('kelas_aktif', 'siswa_kelas.id_kelas_aktif', '=', 'kelas_aktif.id_kelas_aktif')
            ->join('kelas', 'kelas_aktif.id_kelas', '=', 'kelas.id_kelas')
            ->select('kelas.nama_kelas', DB::raw('AVG(nilai_mapel.nilai_akhir) as rata_nilai'))
            ->groupBy('kelas.nama_kelas')
            ->orderBy('kelas.nama_kelas', 'asc')
            ->get();

        /*
        |------------------------------------------------------------------
        | 10 NILAI TERTINGGI
        |------------------------------------------------------------------
        */
        $topNilai = DB::table('nilai_mapel')
            ->join('siswa', 'nilai_mapel.id_siswa', '=', 'siswa.id_siswa')
            ->join('siswa_kelas', 'siswa.id_siswa', '=', 'siswa_kelas.id_siswa')
            ->join('kelas_aktif', 'siswa_kelas.id_kelas_aktif', '=', 'kelas_aktif.id_kelas_aktif')
            ->join('kelas', 'kelas_aktif.id_kelas', '=', 'kelas.id_kelas')
            ->select('siswa.nama_lengkap', 'kelas.nama_kelas', 'nilai_mapel.nilai_akhir')
            ->orderBy('nilai_mapel.nilai_akhir', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard.index', compact(
            'menu',
            'totalSiswa',
            'totalGuru',
            'totalKelas',
            'totalMapel',
            'gender',
            'rataNilaiKelas',
            'topNilai'
        ));
    }
}
