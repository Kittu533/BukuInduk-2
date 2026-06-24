<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\KelasAktif;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\JadwalMengajar;
use App\Models\NilaiMapel;
use App\Support\AcademicRecord;

/**
 * WalasNilaiController — Pantau Nilai Siswa (Wali Kelas)
 *
 * Fitur:
 * - List siswa dengan indikator kelengkapan nilai (Lengkap/Belum Lengkap)
 * - Detail nilai per siswa (wajib + pilihan, filter agama)
 *
 * CATATAN: Wali kelas hanya bisa MELIHAT nilai.
 * Input nilai dilakukan oleh guru mapel, edit oleh admin.
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 *         Ditambahkan indikator kelengkapan nilai per siswa
 */
class WalasNilaiController extends Controller
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

                    $jumlahMapel = JadwalMengajar::where(
                            'id_kelas_aktif',
                            $item->id_kelas_aktif
                        )
                        ->count();

                    $jumlahNilai = NilaiMapel::where(
                            'id_siswa',
                            $item->id_siswa
                        )
                        ->whereHas('jadwalMengajar', function ($q) use ($item) {

                            $q->where(
                                'id_kelas_aktif',
                                $item->id_kelas_aktif
                            );

                        })
                        ->count();

                    return (object) [

                        'id_siswa'      => $item->siswa->id_siswa,
                        'nis'           => $item->siswa->nis,
                        'nama_lengkap'  => $item->siswa->nama_lengkap,
                        'jenis_kelamin' => $item->siswa->jenis_kelamin,

                        'nama_kelas'    =>
                            $item->kelasAktif->kelas->nama_kelas ?? '-',

                        'status_nilai'  =>
                            $jumlahNilai >= $jumlahMapel
                                ? 'Lengkap'
                                : 'Belum Lengkap',

                    ];

                })
                ->sortBy('nama_lengkap')
                ->values();

        }

        return view(
            'wali-kelas.nilai.index_nilai',
            compact(
                'siswa',
                'menu',
                'kelas'
            )
        );
    }
    

    public function detailSiswa($id)
    {
        $menu = "nilai";

        $siswa = Siswa::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | RIWAYAT KELAS SISWA
        |--------------------------------------------------------------------------
        */

        $riwayat = AcademicRecord::riwayatSiswa($id);

        /*
        |--------------------------------------------------------------------------
        | KELAS YANG DIPILIH
        |--------------------------------------------------------------------------
        */

        if (request()->filled('kelas_aktif')) {

            $kelasData = $riwayat
                ->firstWhere(
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
        | NILAI AKADEMIK
        |--------------------------------------------------------------------------
        */

        $nilai_all = collect();

        if ($kelas_walas && $kelas_walas->id_kelas_aktif) {

            $nilai_all = AcademicRecord::nilaiAkademik(
                $id,
                $kelas_walas->id_kelas_aktif,
                $siswa->getMapelExcluded()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FALLBACK KE RIWAYAT YANG PUNYA NILAI
        |--------------------------------------------------------------------------
        */

        if ($nilai_all->isEmpty()) {

            foreach ($riwayat as $item) {

                $nilai = AcademicRecord::nilaiAkademik(
                    $id,
                    $item->id_kelas_aktif,
                    $siswa->getMapelExcluded()
                );

                if ($nilai->isNotEmpty()) {

                    $nilai_all = $nilai;

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

        /*
        |--------------------------------------------------------------------------
        | KELOMPOK NILAI
        |--------------------------------------------------------------------------
        */

        [
            'wajib'   => $nilai_wajib,
            'pilihan' => $nilai_pilihan
        ] = AcademicRecord::kelompokNilai($nilai_all);

        return view(
            'wali-kelas.nilai.detail_nilai',
            compact(
                'menu',
                'siswa',
                'kelas_walas',
                'nilai_wajib',
                'nilai_pilihan',
                'riwayat'
            )
        );
    }
}
