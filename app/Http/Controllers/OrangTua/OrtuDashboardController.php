<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\Kehadiran;
use App\Models\NilaiMapel;

/**
 * OrtuDashboardController — Dashboard Orang Tua
 *
 * Menampilkan ringkasan data anak:
 * - Data siswa & status aktif
 * - Jumlah hadir & alfa
 * - Rata-rata nilai
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 */
class OrtuDashboardController extends Controller
{
    public function index()
    {
        $id_siswa = session('id_siswa');

        $siswa = Siswa::findOrFail($id_siswa);

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

        $statusSiswa = $kelasData ? 'Aktif' : 'Tidak Aktif';

        $kelasAktif = $kelasData ? (object) [
            'nama_kelas'     => $kelasData->kelasAktif->kelas->nama_kelas ?? '-',
            'tahun'          => $kelasData->kelasAktif->tahunAjaran->tahun ?? '-',
            'nama_semester'  => $kelasData->kelasAktif->semester->nama_semester ?? '-',
        ] : null;

        return view(
            'orangtua.dashboard.dashboard_ortu',
            compact(
                'siswa',
                'statusSiswa',
                'kelasAktif'
            )
        );
    }
}
