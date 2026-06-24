<?php

namespace App\Http\Controllers\PembinaEkskul;

use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler;
use App\Models\NilaiEkskul;
use App\Models\KelasAktif;
use App\Models\SiswaKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembinaNilaiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CEK KELAS BOLEH EDIT
    |--------------------------------------------------------------------------
    */

    private function bolehEdit($idKelasAktif)
    {
        $kelasAktif = KelasAktif::with('semester')
            ->find($idKelasAktif);

        if (!$kelasAktif) {
            return false;
        }

        if ($kelasAktif->is_kunci_nilai == 1) {
            return false;
        }

        if (!$kelasAktif->semester) {
            return false;
        }

        if ($kelasAktif->semester->status != 'aktif') {
            return false;
        }

        if (
            method_exists(
                $kelasAktif->semester,
                'masihBisaEditNilai'
            )
            &&
            !$kelasAktif->semester->masihBisaEditNilai()
        ) {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | CEK KELAS XII
    |--------------------------------------------------------------------------
    */

    private function kelasXII($idKelasAktif)
    {
        return KelasAktif::join(
                'kelas',
                'kelas_aktif.id_kelas',
                '=',
                'kelas.id_kelas'
            )
            ->where(
                'kelas_aktif.id_kelas_aktif',
                $idKelasAktif
            )
            ->where(
                'kelas.nama_kelas',
                'LIKE',
                'XII%'
            )
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | CEK SISWA LULUS
    |--------------------------------------------------------------------------
    */

    private function siswaLulus($idSiswa)
    {
        return DB::table('kenaikan_kelas')
            ->where('id_siswa', $idSiswa)
            ->where('status', 'lulus')
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | DAFTAR EKSKUL
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $idGuru = session('id_guru');

        $ekskul = Ekstrakurikuler::where(
                'id_guru',
                $idGuru
            )
            ->orderBy('nama_ekskul')
            ->get();

        return view(
            'pembina.nilai.index_pembina',
            compact('ekskul')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DAFTAR KELAS
    |--------------------------------------------------------------------------
    */

    public function kelas($idEkskul)
{
    $ekskul = Ekstrakurikuler::findOrFail($idEkskul);

    $kelas = NilaiEkskul::join(
            'siswa_kelas',
            'nilai_ekskul.id_siswa',
            '=',
            'siswa_kelas.id_siswa'
        )
        ->join(
            'kelas_aktif',
            'siswa_kelas.id_kelas_aktif',
            '=',
            'kelas_aktif.id_kelas_aktif'
        )
        ->join(
            'kelas',
            'kelas_aktif.id_kelas',
            '=',
            'kelas.id_kelas'
        )
        ->where(
            'nilai_ekskul.id_ekskul',
            $idEkskul
        )
        ->select(
            'kelas_aktif.id_kelas_aktif',
            'kelas.nama_kelas'
        )
        ->groupBy(
            'kelas_aktif.id_kelas_aktif',
            'kelas.nama_kelas'
        )
        ->orderByRaw("
            CASE
                WHEN kelas.nama_kelas LIKE 'X %' THEN 1
                WHEN kelas.nama_kelas LIKE 'XI %' THEN 2
                WHEN kelas.nama_kelas LIKE 'XII%' THEN 3
                ELSE 4
            END
        ")
        ->orderBy('kelas.nama_kelas')
        ->get()
        ->map(function ($item) {

            $item->kelas_xii =
                str_starts_with(
                    strtoupper($item->nama_kelas),
                    'XII'
                );

            $item->boleh_edit = !$item->kelas_xii;

            return $item;
        });

    return view(
        'pembina.nilai.kelas_pembina',
        compact(
            'ekskul',
            'kelas'
        )
    );
}

        /*
        |--------------------------------------------------------------------------
        | DETAIL NILAI
        |--------------------------------------------------------------------------
        */

    public function detail($idEkskul, $idKelasAktif)
    {
        $ekskul = Ekstrakurikuler::findOrFail($idEkskul);

        $kelas = KelasAktif::join(
                'kelas',
                'kelas_aktif.id_kelas',
                '=',
                'kelas.id_kelas'
            )
            ->join(
                'tahun_ajaran',
                'kelas_aktif.id_tahun',
                '=',
                'tahun_ajaran.id_tahun'
            )
            ->join(
                'semester',
                'kelas_aktif.id_semester',
                '=',
                'semester.id_semester'
            )
            ->where(
                'kelas_aktif.id_kelas_aktif',
                $idKelasAktif
            )
            ->select(
                'kelas.nama_kelas',
                'tahun_ajaran.tahun',
                'semester.nama_semester',
                'kelas_aktif.is_kunci_nilai'
            )
            ->first();

        $siswa = NilaiEkskul::join(
                'siswa',
                'nilai_ekskul.id_siswa',
                '=',
                'siswa.id_siswa'
            )
            ->where(
                'nilai_ekskul.id_ekskul',
                $idEkskul
            )
            ->where(
                'nilai_ekskul.id_kelas_aktif',
                $idKelasAktif
            )
            ->whereIn(
                'nilai_ekskul.id_siswa',
                SiswaKelas::select('id_siswa')
            )
            ->select(
                'nilai_ekskul.id_nilai_ekskul',
                'nilai_ekskul.id_siswa',
                'nilai_ekskul.id_kelas_aktif',
                'siswa.nama_lengkap',
                'nilai_ekskul.nilai',

                DB::raw("
                    EXISTS(
                        SELECT 1
                        FROM kenaikan_kelas k
                        WHERE k.id_siswa = siswa.id_siswa
                        AND k.status = 'lulus'
                    ) as sudah_lulus
                ")
            )
            ->orderBy('siswa.nama_lengkap')
            ->get();

        $bolehEdit = true;

        return view(
            'pembina.nilai.detail_pembina',
            compact(
                'ekskul',
                'kelas',
                'siswa',
                'bolehEdit'
            )
        );
    }

    public function updateNilai(Request $request, $id)
    {
        $request->validate([
            'nilai' => 'required'
        ]);

        $nilai = NilaiEkskul::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | BLOKIR SISWA YANG SUDAH LULUS
        |--------------------------------------------------------------------------
        */

        if (
            $this->siswaLulus(
                $nilai->id_siswa
            )
        ) {

            return back()->with(
                'error',
                'Nilai siswa yang sudah lulus tidak dapat diubah.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE NILAI
        |--------------------------------------------------------------------------
        */

        $nilai->update([
            'nilai' => $request->nilai
        ]);

        return back()->with(
            'success',
            'Nilai ekstrakurikuler berhasil diperbarui.'
        );
    }
}