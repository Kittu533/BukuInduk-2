<?php

namespace App\Http\Controllers\PembinaEkskul;

use App\Http\Controllers\Controller;
use App\Models\Ekstrakurikuler;
use App\Models\NilaiEkskul;
use App\Models\SiswaKelas;
use Illuminate\Http\Request;

class PembinaSiswaController extends Controller
{
    public function index()
{
    $idGuru = session('id_guru');

    $siswaAktif = SiswaKelas::select('id_siswa');

    $ekskul = Ekstrakurikuler::withCount([
        'nilaiEkskul as jumlah_anggota' => function ($query) use ($siswaAktif) {

            $query->whereIn(
                'id_siswa',
                $siswaAktif
            );

        }
    ])
    ->where('id_guru', $idGuru)
    ->orderBy('nama_ekskul')
    ->get();

    return view(
        'pembina.siswa.index_siswa',
        compact('ekskul')
    );
}

   public function detail($idEkskul)
{
    $ekskul = Ekstrakurikuler::findOrFail($idEkskul);

    $siswa = NilaiEkskul::with([
            'siswa',
            'kelasAktif.kelas',
            'kelasAktif.tahunAjaran',
            'kelasAktif.semester'
        ])

        ->where('id_ekskul', $idEkskul)

        ->whereHas('kelasAktif.tahunAjaran', function ($q) {

            $q->where('status', 'Aktif');

        })

        ->whereIn(
            'id_siswa',
            SiswaKelas::select('id_siswa')
        )

        ->get()

        ->unique(function ($item) {

            return $item->id_siswa;

        })

        ->sortBy(function ($item) {

            return [
                $item->kelasAktif?->kelas?->nama_kelas ?? '',
                $item->siswa?->nama_lengkap ?? ''
            ];

        })

        ->values();

    return view(
        'pembina.siswa.siswa_pembina',
        compact(
            'ekskul',
            'siswa'
        )
    );
}        
}
