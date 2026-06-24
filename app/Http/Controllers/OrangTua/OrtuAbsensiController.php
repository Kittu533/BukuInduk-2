<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\Kehadiran;

/**
 * OrtuAbsensiController — Lihat Kehadiran Anak (Orang Tua)
 *
 * Fitur:
 * - Menampilkan riwayat ketidakhadiran anak (Sakit/Izin/Alpa)
 * - Informasi kelas aktif & semester
 *
 * CATATAN: Orang tua hanya bisa READ, tidak bisa edit.
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 */
class OrtuAbsensiController extends Controller
{
    public function index()
    {
        $menu = "absensi";
        $id_siswa = session('id_siswa');

        $siswa = Siswa::findOrFail($id_siswa);

        // Ambil kelas aktif terbaru siswa
        $kelasData = SiswaKelas::with([
            'kelasAktif.kelas',
            'kelasAktif.tahunAjaran',
            'kelasAktif.semester'
        ])
        ->where('id_siswa', $id_siswa)
        ->orderByDesc('id_siswa_kelas')
        ->first();

        $kelasAktif = $kelasData ? (object) [
            'id_kelas_aktif' => $kelasData->id_kelas_aktif,
            'nama_kelas' => $kelasData->kelasAktif?->kelas?->nama_kelas,
            'nama_semester' => $kelasData->kelasAktif?->semester?->nama_semester,
            'tahun' => $kelasData->kelasAktif?->tahunAjaran?->tahun,
        ] : null;

        // Data absensi hanya untuk kelas aktif
        $absensi = Kehadiran::with([
                'jadwalMengajar.mapel',
                'jadwalMengajar.guru'
            ])
            ->where('id_siswa', $id_siswa)
            ->when($kelasAktif, function ($query) use ($kelasAktif) {
                $query->whereHas('jadwalMengajar', function ($q) use ($kelasAktif) {
                    $q->where(
                        'id_kelas_aktif',
                        $kelasAktif->id_kelas_aktif
                    );
                });
            })
            ->whereIn('status', [
                'Sakit',
                'Izin',
                'Alpa',
                'sakit',
                'izin',
                'alpa'
            ])
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($k) {
                return (object) [
                    'tanggal' => $k->tanggal,
                    'status' => $k->status,
                    'nama_mapel' => $k->jadwalMengajar?->mapel?->nama_mapel ?? '-',
                    'nama_guru' => $k->jadwalMengajar?->guru?->nama_guru ?? '-',
                ];
            });

        return view(
            'orangtua.absensi.index_absensi',
            compact(
                'menu',
                'siswa',
                'kelasAktif',
                'absensi'
            )
        );
    }
}
