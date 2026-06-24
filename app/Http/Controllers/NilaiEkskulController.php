<?php

namespace App\Http\Controllers;

use App\Models\KelasAktif;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\NilaiEkskul;
use App\Models\Ekstrakurikuler;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\RiwayatKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/**
 * NilaiEkskulController — Kelola Nilai Ekstrakurikuler (Admin)
 *
 * Fitur:
 * - List kelas → list siswa → detail nilai ekskul per siswa
 * - Input, edit, hapus nilai ekskul
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 */
class NilaiEkskulController extends Controller
{
    public function index(Request $request)
{
    $menu = "nilai_ekskul";

    $tahunAktif = TahunAjaran::aktif()->firstOrFail();
    $semesterAktif = Semester::aktif()->firstOrFail();

    $idTahun = $request->id_tahun ?? $tahunAktif->id_tahun;
    $namaSemester = $request->semester ?? $semesterAktif->nama_semester;
    $search = $request->search;

    $kelas = KelasAktif::with([
            'kelas',
            'guru',
            'tahunAjaran',
            'semester'
        ])
        ->where('id_tahun', $idTahun)

        ->whereHas('semester', function ($q) use ($namaSemester) {

            $q->where(
                'nama_semester',
                $namaSemester
            );

        })

        ->when($search, function ($query) use ($search) {

            $query->where(function ($q) use ($search) {

                $q->whereHas('kelas', function ($k) use ($search) {

                    $k->where(
                        'nama_kelas',
                        'like',
                        "%{$search}%"
                    );

                })

                ->orWhereHas('guru', function ($g) use ($search) {

                    $g->where(
                        'nama_guru',
                        'like',
                        "%{$search}%"
                    );

                });

            });

        })

        ->whereHas('kelas')
        ->whereHas('guru')

        ->get()

        ->map(function ($item) use (
            $idTahun,
            $namaSemester,
            $tahunAktif,
            $semesterAktif
        ) {

            $isAktif =
                $idTahun == $tahunAktif->id_tahun
                &&
                strtolower($namaSemester)
                ==
                strtolower($semesterAktif->nama_semester);

            if ($isAktif) {

                $siswaIds = SiswaKelas::where(
                        'id_kelas_aktif',
                        $item->id_kelas_aktif
                    )
                    ->pluck('id_siswa');

            } else {

                $siswaIds = RiwayatKelas::where(
                        'id_kelas_aktif',
                        $item->id_kelas_aktif
                    )
                    ->pluck('id_siswa');

            }

            $item->jumlah_siswa = $siswaIds
                ->unique()
                ->count();

            $item->siswa_punya_ekskul = NilaiEkskul::whereIn(
                    'id_siswa',
                    $siswaIds
                )
                ->where(
                    'id_kelas_aktif',
                    $item->id_kelas_aktif
                )
                ->distinct('id_siswa')
                ->count('id_siswa');

            return $item;

        })

        ->sortBy(fn ($item) => $item->kelas->nama_kelas)
        ->values();

    $tahunAjaran = TahunAjaran::orderBy(
        'tahun',
        'desc'
    )->get();

    $semester = ['Ganjil', 'Genap'];

    return view(
        'admin.nilai_ekskul.index_ne',
        compact(
            'kelas',
            'menu',
            'tahunAjaran',
            'semester',
            'idTahun',
            'namaSemester',
            'search'
        )
    );
}

    public function detail($id)
{
    $menu = "nilai_ekskul";

    $kelasAktif = KelasAktif::with([
        'kelas',
        'semester',
        'tahunAjaran'
    ])->findOrFail($id);

    $kelas = (object) [

        'nama_kelas'     => $kelasAktif->kelas->nama_kelas,
        'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
        'nama_semester'  => $kelasAktif->semester->nama_semester ?? '-',
        'tahun'          => $kelasAktif->tahunAjaran->tahun ?? '-',

    ];

    /*
    |--------------------------------------------------------------------------
    | CEK APAKAH SEMESTER AKTIF
    |--------------------------------------------------------------------------
    */

    $tahunAktif = TahunAjaran::aktif()->first();
    $semesterAktif = Semester::aktif()->first();

    $isAktif =
        $kelasAktif->id_tahun == $tahunAktif?->id_tahun
        &&
        $kelasAktif->id_semester == $semesterAktif?->id_semester;

    /*
    |--------------------------------------------------------------------------
    | AMBIL SISWA
    |--------------------------------------------------------------------------
    */

    if ($isAktif) {

        $siswa = SiswaKelas::with('siswa')
            ->where('id_kelas_aktif', $id)
            ->get()
            ->map(fn ($item) => $item->siswa)
            ->filter()
            ->sortBy('nama_lengkap')
            ->values();

    } else {

        $siswa = RiwayatKelas::with('siswa')
            ->where('id_kelas_aktif', $id)
            ->get()
            ->map(fn ($item) => $item->siswa)
            ->filter()
            ->sortBy('nama_lengkap')
            ->values();

    }

    return view(
        'admin.nilai_ekskul.detail_ne',
        compact(
            'kelas',
            'siswa',
            'menu'
        )
    );
}

    public function detailSiswa($id)
{
    $menu = "nilai_ekskul";

    $siswaModel = Siswa::findOrFail($id);

    /*
    |--------------------------------------------------------------------------
    | KELAS AKTIF SISWA
    |--------------------------------------------------------------------------
    */

    $kelasData = SiswaKelas::with([
        'kelasAktif.kelas',
        'kelasAktif.semester',
        'kelasAktif.tahunAjaran'
    ])
    ->where('id_siswa', $id)
    ->first();

    /*
    |--------------------------------------------------------------------------
    | NILAI EKSKUL HANYA SEMESTER AKTIF
    |--------------------------------------------------------------------------
    */

    $data = collect();

    if ($kelasData) {

        $data = NilaiEkskul::with('ekstrakurikuler')
            ->where('id_siswa', $id)
            ->where(
                'id_kelas_aktif',
                $kelasData->id_kelas_aktif
            )
            ->orderBy('id_ekskul')
            ->get();

    }

    /*
    |--------------------------------------------------------------------------
    | DATA SISWA
    |--------------------------------------------------------------------------
    */

    $siswa = (object) [

        'id_siswa'      => $siswaModel->id_siswa,
        'nama_lengkap'  => $siswaModel->nama_lengkap,
        'nis'           => $siswaModel->nis,

        'nama_kelas'    =>
            $kelasData?->kelasAktif?->kelas?->nama_kelas
            ?? '-',

        'tahun'         =>
            $kelasData?->kelasAktif?->tahunAjaran?->tahun
            ?? '-',

        'nama_semester' =>
            $kelasData?->kelasAktif?->semester?->nama_semester
            ?? '-',

    ];

    /*
    |--------------------------------------------------------------------------
    | STATUS EDIT
    |--------------------------------------------------------------------------
    */

    [$canEdit, $editLockReason] = $this->resolveEditState(
        $kelasData
    );

    return view(
        'admin.nilai_ekskul.detail_siswa_ne',
        compact(
            'data',
            'menu',
            'siswa',
            'canEdit',
            'editLockReason'
        )
    );
}

    public function editSiswa($id)
    {
        $menu = "nilai_ekskul";

        $data = NilaiEkskul::with(['ekstrakurikuler', 'siswa'])
            ->where('id_siswa', $id)
            ->get();

        return view('admin.nilai_ekskul.edit_ne', compact('data', 'menu', 'id'));
    }

    public function updateSiswa(Request $request, $id)
    {
        foreach ($request->nilai as $id_nilai => $nilai) {
            NilaiEkskul::where('id_nilai_ekskul', $id_nilai)
                ->update(['nilai' => $nilai]);
        }

        return redirect()->back()
            ->with('success', 'Berhasil update nilai ekstrakurikuler.');
    }

    public function create($id)
    {
        $menu = "nilai_ekskul";

        $siswaModel = Siswa::findOrFail($id);
        $kelasData = SiswaKelas::with(['kelasAktif.kelas', 'kelasAktif.semester.tahunAjaran'])
            ->where('id_siswa', $id)
            ->first();

        $siswa = (object) [
            'id_siswa' => $siswaModel->id_siswa,
            'nama_lengkap' => $siswaModel->nama_lengkap,
            'nis' => $siswaModel->nis,
            'id_kelas_aktif' => $kelasData?->kelasAktif?->id_kelas_aktif ?? '',
            'nama_kelas' => $kelasData?->kelasAktif?->kelas?->nama_kelas ?? '-',
            'tahun' => $kelasData?->kelasAktif?->semester?->tahunAjaran?->tahun ?? '-',
            'nama_semester' => $kelasData?->kelasAktif?->semester?->nama_semester ?? '-',
        ];

        $ekskul = Ekstrakurikuler::orderBy('nama_ekskul')->get();

        return view('admin.nilai_ekskul.create_ne', compact('menu', 'siswa', 'ekskul'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id_siswa',
            'id_ekskul' => 'required|exists:ekstrakurikuler,id_ekskul',
            'nilai' => 'required|numeric|min:0|max:100',
        ]);

        $payload = [
            'id_nilai_ekskul' => NilaiEkskul::generateId(),
            'id_siswa' => $request->id_siswa,
            'id_ekskul' => $request->id_ekskul,
            'nilai' => $request->nilai,
        ];

        if (Schema::hasColumn('nilai_ekskul', 'id_kelas_aktif')) {
            $payload['id_kelas_aktif'] = Siswa::where('id_siswa', $request->id_siswa)
                ->value('id_kelas_aktif');
        }

        NilaiEkskul::create($payload);

        return redirect('admin/nilai_ekskul/detail/' . $request->id_siswa)
            ->with('success', 'Data nilai ekstrakurikuler berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $nilai = NilaiEkskul::findOrFail($id);
        $idSiswa = $nilai->id_siswa;
        $nilai->delete();

        return redirect('admin/nilai_ekskul/detail/' . $idSiswa)
            ->with('success', 'Data nilai ekstrakurikuler berhasil dihapus.');
    }

    private function resolveEditState($kelasData): array
    {
        if (! $kelasData || ! $kelasData->kelasAktif || ! $kelasData->kelasAktif->semester) {
            return [false, 'Data kelas siswa tidak ditemukan.'];
        }

        $semester = $kelasData->kelasAktif->semester;

        if ($semester->status !== 'aktif') {
            return [
                false,
                'Nilai semester lama hanya bisa dilihat. Edit hanya dibuka untuk semester aktif.'
            ];
        }

        if (! $semester->masihBisaEditNilai()) {
            return [
                false,
                'Batas edit nilai semester aktif sudah lewat. Nilai dikunci dari UI normal.'
            ];
        }

        return [true, null];
    }
}
