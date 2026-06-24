<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumni;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Support\AcademicRecord;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $menu = 'alumni';

        $angkatanOptions = Siswa::whereIn('status_siswa', [Siswa::STATUS_LULUS, 'Lulus'])
            ->whereNotNull('tahun_masuk')
            ->selectRaw('YEAR(tahun_masuk) as angkatan')
            ->distinct()
            ->orderByDesc('angkatan')
            ->pluck('angkatan');

        $tahunAjaranOptions = Alumni::query()
            ->join('tahun_ajaran', 'tahun_ajaran.id_tahun', '=', 'alumni.id_tahun_lulus')
            ->select('tahun_ajaran.id_tahun', 'tahun_ajaran.tahun')
            ->distinct()
            ->orderByDesc('tahun_ajaran.tahun')
            ->get();

        $query = Siswa::query()
            ->leftJoin('alumni', 'alumni.id_siswa', '=', 'siswa.id_siswa')
            ->leftJoin('tahun_ajaran as tahun_lulus', 'tahun_lulus.id_tahun', '=', 'alumni.id_tahun_lulus')
            ->whereIn('siswa.status_siswa', [Siswa::STATUS_LULUS, 'Lulus'])
            ->select(
                'siswa.*',
                'alumni.id_tahun_lulus',
                'alumni.tanggal_lulus',
                'tahun_lulus.tahun as tahun_lulus_label'
            );

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('siswa.id_siswa', 'like', "%{$search}%")
                  ->orWhere('siswa.nis', 'like', "%{$search}%")
                  ->orWhere('siswa.nisn', 'like', "%{$search}%")
                  ->orWhere('siswa.nama_lengkap', 'like', "%{$search}%");
            });
        }

        if ($request->filled('angkatan')) {
            $query->whereYear('siswa.tahun_masuk', $request->angkatan);
        }

        if ($request->filled('tahun_ajaran')) {
            $query->where('alumni.id_tahun_lulus', $request->tahun_ajaran);
        }

        $alumni = $query
            ->orderByDesc('siswa.tahun_masuk')
            ->orderBy('siswa.nama_lengkap')
            ->paginate(15);

        return view(
            'admin.alumni.index',
            compact(
                'menu',
                'alumni',
                'angkatanOptions',
                'tahunAjaranOptions'
            )
        );
    }

    public function detail(Request $request, $id)
{
    $menu = 'alumni';

    $siswa = Siswa::findOrFail($id);

    /*
    |--------------------------------------------------------------------------
    | Riwayat Kelas Alumni
    |--------------------------------------------------------------------------
    */

    $riwayatAsli = AcademicRecord::riwayatSiswa($id);

    $tahunMasuk = \Carbon\Carbon::parse(
        $siswa->tahun_masuk
    )->format('Y');

    $riwayat = $riwayatAsli->map(function ($item) use ($tahunMasuk) {

        $kelas = $item->kelasAktif->kelas->nama_kelas ?? '-';

        if (str_starts_with($kelas, 'XII')) {

            $tahun = ($tahunMasuk + 2)
                . '/'
                . ($tahunMasuk + 3);

            $kelasOrder = 3;

        } elseif (str_starts_with($kelas, 'XI')) {

            $tahun = ($tahunMasuk + 1)
                . '/'
                . ($tahunMasuk + 2);

            $kelasOrder = 2;

        } else {

            $tahun = $tahunMasuk
                . '/'
                . ($tahunMasuk + 1);

            $kelasOrder = 1;
        }

        $semester =
            $item->kelasAktif->semester->nama_semester ?? '-';

        return (object) [

            'id_kelas_aktif' => $item->id_kelas_aktif,

            'kelas' => $kelas,

            'tahun' => $tahun,

            'semester' => $semester,

            'kelas_order' => $kelasOrder,

            'semester_order' =>
                $semester === 'Ganjil'
                ? 1
                : 2,
        ];
    })
    ->sortBy([
        ['kelas_order', 'asc'],
        ['semester_order', 'asc']
    ])
    ->values();

    /*
    |--------------------------------------------------------------------------
    | Semester Terakhir
    |--------------------------------------------------------------------------
    */

    $selectedKelasAktifId = $request->query('id_kelas_aktif');

    $riwayatAktif =
        AcademicRecord::riwayatTerpilih($siswa, $selectedKelasAktifId);

    /*
    |--------------------------------------------------------------------------
    | Nilai Akademik
    |--------------------------------------------------------------------------
    */

    $nilai = AcademicRecord::nilaiAkademik(
        $siswa->id_siswa,
        $riwayatAktif?->id_kelas_aktif
    );

    $kelompokNilai =
        AcademicRecord::kelompokNilai($nilai);

    /*
    |--------------------------------------------------------------------------
    | Nilai Ekstrakurikuler
    |--------------------------------------------------------------------------
    */

    $ekskul = AcademicRecord::nilaiEkskul(
        $siswa->id_siswa,
        $riwayatAktif?->id_kelas_aktif
    );

    /*
    |--------------------------------------------------------------------------
    | Kehadiran
    |--------------------------------------------------------------------------
    */

    $kehadiran = AcademicRecord::kehadiran(
        $siswa->id_siswa,
        $riwayatAktif?->id_kelas_aktif
    );

    return view(
        'admin.alumni.detail',
        compact(
            'menu',
            'siswa',
            'riwayat',
            'nilai',
            'kelompokNilai',
            'ekskul',
            'kehadiran',
            'riwayatAktif'
        )
    );
}
}
