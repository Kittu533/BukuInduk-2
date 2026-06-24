<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\NilaiMapel;
use App\Models\KenaikanKelas;

class OrtuRekapController extends Controller
{
    public function index()
{
    $id_siswa = session('id_siswa');

    $siswa = Siswa::findOrFail($id_siswa);

    $semuaNilai = NilaiMapel::with([
        'jadwalMengajar.kelasAktif.kelas',
        'jadwalMengajar.kelasAktif.semester',
        'jadwalMengajar.mapel',
    ])
    ->where('id_siswa', $id_siswa)
    ->get();

    $tahunHeaders = [
        'X',
        'XI',
        'XII'
    ];

    $nilaiPerMapel = [];

    foreach ($semuaNilai as $nilai) {

        $kelasAktif = $nilai->jadwalMengajar?->kelasAktif;

        if (!$kelasAktif) {
            continue;
        }

        $namaKelas = strtoupper(
            $kelasAktif->kelas->nama_kelas ?? ''
        );

        $isGanjil =
            ($kelasAktif->semester->nama_semester ?? '') === 'Ganjil';

        if (str_starts_with($namaKelas, 'XII')) {

            $kolomIndex = $isGanjil ? 4 : 5;

        } elseif (str_starts_with($namaKelas, 'XI')) {

            $kolomIndex = $isGanjil ? 2 : 3;

        } elseif (str_starts_with($namaKelas, 'X')) {

            $kolomIndex = $isGanjil ? 0 : 1;

        } else {

            continue;
        }

        $mapel = $nilai->jadwalMengajar?->mapel;

        if (!$mapel) {
            continue;
        }

        $namaMapel = trim($mapel->nama_mapel);

        $kategori = strtolower(
            trim($mapel->kategori_mapel ?? '')
        );

        $key = $namaMapel . '_' . $kategori;

        if (!isset($nilaiPerMapel[$key])) {

            $nilaiPerMapel[$key] = [
                'nama_mapel' => $namaMapel,
                'kategori'   => $kategori,
                'nilai'      => array_fill(0, 6, '-')
            ];
        }

        $nilaiPerMapel[$key]['nilai'][$kolomIndex] =
            $nilai->nilai_akhir ?? '-';
    }

    $excluded = $siswa->getMapelExcluded();

    $urutanWajib = [
        'Pendidikan Agama Islam',
        'Pendidikan Agama Kristen',
        'Pendidikan Agama Katolik',
        'Pendidikan Pancasila',
        'Bahasa Indonesia',
        'Bahasa Inggris',
        'Matematika',
        'Fisika',
        'Kimia',
        'Biologi',
        'Sejarah',
        'Geografi',
        'Ekonomi',
        'Sosiologi',
        'Informatika',
        'Bahasa Jawa',
        'Seni Budaya',
        'Pendidikan Jasmani, Olahraga dan Kesehatan',
        'Prakarya dan Kewirausahaan',
    ];

    $urutanPilihan = [
        'Bahasa Inggris Tingkat Lanjut',
        'Matematika Tingkat Lanjut',
        'Biologi',
        'Fisika',
        'Kimia',
        'Geografi',
        'Ekonomi',
        'Sosiologi',
        'Bahasa Jepang',
        'Koding dan Kecerdasan',
        'Informatika',
    ];

    $mapelWajib = collect($nilaiPerMapel)
        ->filter(function ($item) use ($excluded) {

            return $item['kategori'] === 'wajib'
                && !in_array(
                    $item['nama_mapel'],
                    $excluded
                );

        })
        ->sortBy(function ($item) use ($urutanWajib) {

            $index = array_search(
                $item['nama_mapel'],
                $urutanWajib
            );

            return $index === false ? 999 : $index;

        })
        ->values();

    $mapelPilihan = collect($nilaiPerMapel)
        ->filter(function ($item) use ($excluded) {

            return $item['kategori'] === 'pilihan'
                && !in_array(
                    $item['nama_mapel'],
                    $excluded
                );

        })
        ->sortBy(function ($item) use ($urutanPilihan) {

            $index = array_search(
                $item['nama_mapel'],
                $urutanPilihan
            );

            return $index === false ? 999 : $index;

        })
        ->values();

    $kenaikanKelas = KenaikanKelas::with('tahunAjaran')
    ->where('id_siswa', $id_siswa)
    ->orderBy('id_tahun')
    ->get();

    $naikKelas = $kenaikanKelas
        ->where('status', 'naik')
        ->values();

    $lulus = $kenaikanKelas
        ->where('status', 'lulus')
        ->first();

    return view(
        'orangtua.rekap.index_rekap',
        compact(
            'siswa',
            'tahunHeaders',
            'mapelWajib',
            'mapelPilihan',
            'kenaikanKelas',
            'naikKelas',
            'lulus'
        )
    );
}
}