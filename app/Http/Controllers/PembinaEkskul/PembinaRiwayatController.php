<?php

namespace App\Http\Controllers\PembinaEkskul;

use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler;
use App\Models\NilaiEkskul;
use App\Models\KelasAktif;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PembinaRiwayatController extends Controller
{
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
            'pembina.riwayat.index_riwayat_pembina',
            compact('ekskul')
        );
    }
   
    public function detail($idEkskul)
{
    $menu = "riwayat";

    $ekskul = Ekstrakurikuler::findOrFail($idEkskul);

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
        ->whereNotExists(function ($q) {

            $q->select(DB::raw(1))
                ->from('kenaikan_kelas')
                ->whereColumn(
                    'kenaikan_kelas.id_siswa',
                    'siswa.id_siswa'
                )
                ->where(
                    'status',
                    'lulus'
                );
        })
        ->select(
            'siswa.id_siswa',
            'siswa.nama_lengkap',
            'nilai_ekskul.nilai'
        )
        ->orderBy('siswa.nama_lengkap')
        ->get();

    return view(
        'pembina.riwayat.detail_riwayat_pembina',
        compact(
            'menu',
            'ekskul',
            'siswa'
        )
    );
}
}