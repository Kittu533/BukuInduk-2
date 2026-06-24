<?php

namespace App\Http\Controllers;

use App\Models\KelasAktif;
use App\Models\RiwayatKelas;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\NilaiMapel;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Support\AcademicRecord;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

/**
 * NilaiAkademikController — Kelola Nilai Akademik (Admin)
 *
 * Fitur:
 * - List kelas → list siswa → detail nilai per siswa
 * - Edit nilai dibatasi semester aktif dan batas_edit_nilai
 * - Kalkulasi nilai akhir otomatis via NilaiMapel::hitungNilaiAkhir()
 * - Filter mata pelajaran berdasarkan agama siswa
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 *         Fix: rumus nilai akhir sekarang konsisten (40% tugas + 30% UTS + 30% UAS)
 *         Fix: filter agama menggunakan Siswa::getMapelExcluded()
 */
class NilaiAkademikController extends Controller
{

   public function index(Request $request)
{
    $menu = "nilai_akademik";

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
        ->whereHas('semester', function ($query) use ($namaSemester) {
            $query->where('nama_semester', $namaSemester);
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

            /*
            |--------------------------------------------------------------------------
            | JUMLAH SISWA
            |--------------------------------------------------------------------------
            */

            $isAktif =
                $idTahun == $tahunAktif->id_tahun
                &&
                strtolower((string) $namaSemester) == strtolower((string) $semesterAktif->nama_semester);

            if ($isAktif) {

                $jumlahSiswa = SiswaKelas::where(
                        'id_kelas_aktif',
                        $item->id_kelas_aktif
                    )
                    ->distinct()
                    ->count('id_siswa');

            } else {

                $jumlahSiswa = RiwayatKelas::where(
                        'id_kelas_aktif',
                        $item->id_kelas_aktif
                    )
                    ->distinct()
                    ->count('id_siswa');

            }

            $item->jumlah_siswa = $jumlahSiswa;

            /*
            |--------------------------------------------------------------------------
            | RATA-RATA NILAI
            |--------------------------------------------------------------------------
            */

            $rata = NilaiMapel::whereHas(
                    'jadwalMengajar',
                    function ($q) use ($item) {

                        $q->where(
                            'id_kelas_aktif',
                            $item->id_kelas_aktif
                        );

                    }
                )
                ->avg('nilai_akhir');

            $item->rata_nilai = $rata
                ? round($rata, 1)
                : null;

            return $item;

        })

        ->sortBy(function ($item) {

            return $item->kelas->nama_kelas ?? '';

        })

        ->values();

    $tahunAjaran = TahunAjaran::orderBy(
        'tahun',
        'desc'
    )->get();

    $semester = $this->masterSemesterOptions();

    return view(
        'admin.nilai_akademik.index_na',
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

private function masterSemesterOptions(): Collection
{
    return collect([
        (object) ['nama_semester' => 'Ganjil'],
        (object) ['nama_semester' => 'Genap'],
    ]);
}

public function detail($id)
{
    $menu = "nilai";

    $kelasAktif = KelasAktif::with([
        'kelas',
        'semester',
        'tahunAjaran'
    ])->findOrFail($id);

    $kelas = (object) [
        'nama_kelas'      => $kelasAktif->kelas->nama_kelas,
        'id_kelas_aktif'  => $kelasAktif->id_kelas_aktif,
        'nama_semester'   => $kelasAktif->semester->nama_semester ?? '-',
        'tahun'           => $kelasAktif->tahunAjaran->tahun ?? '-',
    ];

    /*
    |--------------------------------------------------------------------------
    | Cek apakah kelas aktif saat ini
    |--------------------------------------------------------------------------
    */

    $tahunAktif = TahunAjaran::aktif()->first();
    $semesterAktif = Semester::aktif()->first();

    $isSemesterAktif =
        $kelasAktif->id_tahun == $tahunAktif->id_tahun &&
        $kelasAktif->id_semester == $semesterAktif->id_semester;

    /*
    |--------------------------------------------------------------------------
    | Semester aktif = siswa_kelas
    | Semester lama = riwayat_kelas
    |--------------------------------------------------------------------------
    */

    if ($isSemesterAktif) {

        $dataSiswa = SiswaKelas::with('siswa')
            ->where('id_kelas_aktif', $id)
            ->orderBy('id_siswa')
            ->get();

    } else {

        $dataSiswa = RiwayatKelas::with('siswa')
            ->where('id_kelas_aktif', $id)
            ->orderBy('id_siswa')
            ->get();

    }

    $siswa = $dataSiswa
        ->map(function ($item) {

            if (!$item->siswa) {
                return null;
            }

            return (object) [

                'id_siswa'      => $item->siswa->id_siswa,
                'nis'           => $item->siswa->nis,
                'nama_lengkap'  => $item->siswa->nama_lengkap,
                'jenis_kelamin' => $item->siswa->jenis_kelamin,

            ];

        })
        ->filter()
        ->values();

    return view(
        'admin.nilai_akademik.detail_na',
        compact(
            'kelas',
            'siswa',
            'menu'
        )
    );
}

    public function detailSiswa($id)
{
    $menu = "nilai_akademik";

    $siswa = Siswa::findOrFail($id);

    // Semua riwayat untuk dropdown
    $riwayat = AcademicRecord::riwayatSiswa($id);

    /*
    |--------------------------------------------------------------------------
    | DEFAULT = KELAS AKTIF SISWA
    |--------------------------------------------------------------------------
    */

    if (request()->filled('kelas_aktif')) {

        $kelasData = AcademicRecord::riwayatTerpilih(
            $siswa,
            request('kelas_aktif')
        );

    } else {

        $kelasData = SiswaKelas::with([
            'kelasAktif.kelas',
            'kelasAktif.semester',
            'kelasAktif.tahunAjaran',
        ])
        ->where('id_siswa', $id)
        ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | DATA KELAS
    |--------------------------------------------------------------------------
    */

    $kelas = null;

    if ($kelasData && $kelasData->kelasAktif) {

        $kelas = (object) [
            'nama_kelas'     =>
                $kelasData->kelasAktif->kelas->nama_kelas ?? '-',

            'id_kelas_aktif' =>
                $kelasData->kelasAktif->id_kelas_aktif,

            'nama_semester'  =>
                $kelasData->kelasAktif->semester->nama_semester ?? '-',

            'tahun'          =>
                $kelasData->kelasAktif->tahunAjaran->tahun ?? '-',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | NILAI SESUAI KELAS TERPILIH
    |--------------------------------------------------------------------------
    */

    $nilai_all = collect();

    if ($kelas) {

        $nilai_all = AcademicRecord::nilaiAkademik(
            $id,
            $kelas->id_kelas_aktif,
            $siswa->getMapelExcluded()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FALLBACK JIKA NILAI KOSONG
    |--------------------------------------------------------------------------
    */

    if ($nilai_all->isEmpty() && $riwayat->count()) {

        foreach ($riwayat as $item) {

            $nilai_all = AcademicRecord::nilaiAkademik(
                $id,
                $item->id_kelas_aktif,
                $siswa->getMapelExcluded()
            );

            if ($nilai_all->isNotEmpty()) {

                $kelas = (object) [

                    'nama_kelas' =>
                        $item->kelasAktif->kelas->nama_kelas ?? '-',

                    'id_kelas_aktif' =>
                        $item->kelasAktif->id_kelas_aktif,

                    'nama_semester' =>
                        $item->kelasAktif->semester->nama_semester ?? '-',

                    'tahun' =>
                        $item->kelasAktif->tahunAjaran->tahun ?? '-',
                ];

                break;
            }
        }
    }

    [
        'wajib'   => $nilai_wajib,
        'pilihan' => $nilai_pilihan
    ] = AcademicRecord::kelompokNilai($nilai_all);

    $canEdit = $kelasData
        && $kelasData->kelasAktif
        && $kelasData->kelasAktif->semester
        && $kelasData->kelasAktif->semester->status === 'aktif'
        && $kelasData->kelasAktif->semester->masihBisaEditNilai();

    [, $editLockReason] = $this->resolveEditState($kelasData);

    return view(
        'admin.nilai_akademik.detail_siswa_na',
        compact(
            'menu',
            'siswa',
            'kelas',
            'nilai_wajib',
            'nilai_pilihan',
            'riwayat',
            'canEdit',
            'editLockReason'
        )
    );
}

    public function edit($id)
    {
        $menu = "nilai_akademik";

        $siswa = Siswa::findOrFail($id);
        $riwayat = AcademicRecord::riwayatSiswa($id);
        $kelasData = AcademicRecord::riwayatTerpilih($siswa, request('kelas_aktif'));

        abort_if(!$kelasData, 404, 'Riwayat kelas siswa tidak ditemukan.');

        [$canEdit, $editLockReason] = $this->resolveEditState($kelasData);
        $semester = $kelasData->kelasAktif->semester;

        $kelas = (object) [
            'nama_kelas' => $kelasData->kelasAktif->kelas->nama_kelas,
            'id_kelas_aktif' => $kelasData->kelasAktif->id_kelas_aktif,
            'nama_semester' => $semester->nama_semester,
            'tahun' => $semester->tahunAjaran->tahun,
            'status_semester' => $semester->status,
            'batas_edit_nilai' => $semester->batas_edit_nilai,
        ];

        $nilai = AcademicRecord::nilaiAkademik($id, $kelas->id_kelas_aktif, $siswa->getMapelExcluded());

        return view('admin.nilai_akademik.edit_siswa_na', compact(
            'menu', 'siswa', 'nilai', 'kelas', 'riwayat', 'canEdit', 'editLockReason'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kelas_aktif' => 'required|string|exists:kelas_aktif,id_kelas_aktif',
            'id_nilai' => 'required|array',
            'id_nilai.*' => 'required|string|exists:nilai_mapel,id_nilai',
            'tugas1' => 'required|array',
            'tugas1.*' => 'nullable|integer|min:0|max:100',
            'tugas2' => 'required|array',
            'tugas2.*' => 'nullable|integer|min:0|max:100',
            'tugas3' => 'required|array',
            'tugas3.*' => 'nullable|integer|min:0|max:100',
            'tugas4' => 'required|array',
            'tugas4.*' => 'nullable|integer|min:0|max:100',
            'tugas5' => 'required|array',
            'tugas5.*' => 'nullable|integer|min:0|max:100',
            'uts' => 'required|array',
            'uts.*' => 'nullable|integer|min:0|max:100',
            'uas' => 'required|array',
            'uas.*' => 'nullable|integer|min:0|max:100',
        ]);

        $siswa = Siswa::findOrFail($id);
        $kelasData = AcademicRecord::riwayatTerpilih($siswa, $request->kelas_aktif);

        [$canEdit, $editLockReason] = $this->resolveEditState($kelasData);

        if (! $canEdit) {
            return redirect('admin/nilai_akademik/detail/' . $id . '?kelas_aktif=' . $request->kelas_aktif)
                ->with('error', $editLockReason);
        }

        foreach ($request->id_nilai as $key => $id_nilai) {
            $nilaiMapel = NilaiMapel::where('id_nilai', $id_nilai)
                ->where('id_siswa', $id)
                ->whereHas('jadwalMengajar', function ($query) use ($request) {
                    $query->where('id_kelas_aktif', $request->kelas_aktif);
                })
                ->firstOrFail();

            $nilaiMapel->update([
                'tugas1' => $request->tugas1[$key] ?? null,
                'tugas2' => $request->tugas2[$key] ?? null,
                'tugas3' => $request->tugas3[$key] ?? null,
                'tugas4' => $request->tugas4[$key] ?? null,
                'tugas5' => $request->tugas5[$key] ?? null,
                'uts' => $request->uts[$key] ?? null,
                'uas' => $request->uas[$key] ?? null,
            ]);

            // Hitung ulang nilai akhir menggunakan method di model
            $nilaiMapel->simpanNilaiAkhir();
        }

        return redirect('admin/nilai_akademik/detail/' . $id . '?kelas_aktif=' . $request->kelas_aktif)
            ->with('success', 'Data nilai berhasil diperbarui');
    }

    private function resolveEditState($kelasData): array
    {
        if (! $kelasData || ! $kelasData->kelasAktif || ! $kelasData->kelasAktif->semester) {
            return [false, 'Riwayat kelas siswa tidak ditemukan.'];
        }

        $semester = $kelasData->kelasAktif->semester;

        if ($semester->status !== 'aktif') {
            return [false, 'Nilai semester lama hanya bisa dilihat. Edit hanya dibuka untuk semester aktif.'];
        }

        if (! $semester->masihBisaEditNilai()) {
            return [false, 'Batas edit nilai semester aktif sudah lewat. Nilai dikunci dari UI normal.'];
        }

        return [true, null];
    }
}
