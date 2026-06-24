<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\KelasAktif;
use App\Models\SiswaKelas;
use App\Models\Siswa;
use App\Models\RiwayatKelas;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Support\AcademicRecord;
use Illuminate\Http\Request;

/**
 * SiswaController — Data Siswa per Kelas (Admin)
 *
 * Fitur:
 * - List kelas aktif dengan wali kelas
 * - List siswa per kelas
 * - Detail buku induk siswa
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 */
class SiswaController extends Controller
{
   public function index(Request $request)
{
    $menu = "siswa";

    $keyword = $request->keyword;

    $tahun = TahunAjaran::aktif()->firstOrFail();
    $semester = Semester::aktif()->firstOrFail();

    $baseQuery = KelasAktif::with([
        'kelas',
        'guru',
        'tahunAjaran',
        'semester',
        'siswaKelas'
    ])
        ->where('id_tahun', $tahun->id_tahun)
        ->whereHas('kelas')
        ->whereHas('guru');

    $kelas = (clone $baseQuery)
        ->whereHas('semester', function ($query) use ($semester) {
            $query->where('nama_semester', $semester->nama_semester);
        })
        ->when($keyword, function ($query) use ($keyword) {
            $query->whereHas('siswaKelas.siswa', function ($q) use ($keyword) {
                $q->where('nama_lengkap', 'like', "%{$keyword}%")
                    ->orWhere('nis', 'like', "%{$keyword}%")
                    ->orWhere('nisn', 'like', "%{$keyword}%");
            });
        })
        ->get();

    if ($kelas->isEmpty()) {
        $kelas = (clone $baseQuery)
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('siswaKelas.siswa', function ($q) use ($keyword) {
                    $q->where('nama_lengkap', 'like', "%{$keyword}%")
                        ->orWhere('nis', 'like', "%{$keyword}%")
                        ->orWhere('nisn', 'like', "%{$keyword}%");
                });
            })
            ->get();
    }

    $kelas = $kelas
        ->map(function ($item) {

            $item->jumlah_siswa = $item->siswaKelas
                ->filter(function ($siswaKelas) {
                    return strtolower((string) $siswaKelas->siswa?->status_siswa)
                        === Siswa::STATUS_AKTIF;
                })
                ->count();

            $item->is_priority_intake =
                str_starts_with((string) ($item->kelas->nama_kelas ?? ''), 'X ')
                && $item->jumlah_siswa === 0;

            return $item;
        })
        ->sortBy(function ($item) {

            $namaKelas = $item->kelas->nama_kelas ?? '';

            // kelas X kosong tetap paling atas
            $priority = $item->is_priority_intake ? 0 : 1;

            // urutan tingkat: X -> XI -> XII
            if (str_starts_with($namaKelas, 'XII')) {
                $tingkat = 3;
            } elseif (str_starts_with($namaKelas, 'XI')) {
                $tingkat = 2;
            } else {
                $tingkat = 1;
            }

            // ambil nomor F01, F02, dst
            preg_match('/F(\d+)/', $namaKelas, $match);
            $nomor = isset($match[1]) ? (int) $match[1] : 999;

            return sprintf(
                '%01d-%01d-%03d',
                $priority,
                $tingkat,
                $nomor
            );
        })
        ->values();

    $priorityIntakeClasses = $kelas
        ->filter(fn($item) => $item->is_priority_intake)
        ->pluck('kelas.nama_kelas')
        ->values();

    return view(
        'admin.siswa.index',
        compact(
            'kelas',
            'menu',
            'keyword',
            'tahun',
            'semester',
            'priorityIntakeClasses'
        )
    );
}

    public function show($id)
    {
        $menu = "siswa";

        $kelasAktif = KelasAktif::with([
            'kelas',
            'guru'
        ])->findOrFail($id);

        $siswa = SiswaKelas::with([
            'siswa'
        ])
        ->where('id_kelas_aktif', $id)
        ->orderBy('id_siswa')
        ->get();

        $kelasAktif->jumlah_siswa_aktif = $siswa
            ->filter(fn ($row) => strtolower((string) $row->siswa?->status_siswa) === Siswa::STATUS_AKTIF)
            ->count();
        $kelasAktif->is_priority_intake = str_starts_with((string) ($kelasAktif->kelas->nama_kelas ?? ''), 'X ')
            && $kelasAktif->jumlah_siswa_aktif === 0;

        return view(
            'admin.siswa.show',
            compact(
                'menu',
                'kelasAktif',
                'siswa'
            )
        );
    }

    public function detail($id)
    {
        $menu = "siswa";

        $siswa = Siswa::with([
            'kelas',
            'siswaKelas.kelasAktif.kelas'
        ])
        ->where('id_siswa', $id)
        ->firstOrFail();

        $riwayat = AcademicRecord::riwayatSiswa($id);

        return view(
            'admin.siswa.detail',
            compact(
                'siswa',
                'menu',
                'riwayat'
            )
        );
    }

    public function create()
    {
        $menu = "siswa";

        $tahunAktif = TahunAjaran::aktif()->firstOrFail();
        $semesterAktif = Semester::aktif()->firstOrFail();

        $kelas = Kelas::orderBy('nama_kelas')->get();
        $kelasAktif = $this->activeKelasAktifWithCounts($tahunAktif, $semesterAktif);
        $emptyGradeXClassIds = $this->emptyGradeXClassIds($kelasAktif);

        return view(
            'admin.siswa.create',
            compact(
                'menu',
                'kelas',
                'kelasAktif',
                'emptyGradeXClassIds'
            )
        );
    }

   public function store(Request $request)
    {
    $request->validate([
    'status_masuk'      => 'required|in:baru,pindahan',

        'nis'               => 'required|numeric|unique:siswa,nis',
        'nisn'              => 'nullable|numeric|unique:siswa,nisn',

        'nama_lengkap'      => 'required|max:200',
        'jenis_kelamin'     => 'required',

        'id_kelas'          => 'required',
        'id_kelas_aktif'    => 'required',

        'tahun_masuk'       => 'required|date',

        'no_telp'           => 'nullable|regex:/^[0-9]+$/',
        'no_telp_ortu'      => 'nullable|regex:/^[0-9]+$/',
        'no_telp_wali'      => 'nullable|regex:/^[0-9]+$/',

    ], [

        'nis.unique'        => 'NIS sudah digunakan.',
        'nisn.unique'       => 'NISN sudah digunakan.',

        'nis.numeric'       => 'NIS harus berupa angka.',
        'nisn.numeric'      => 'NISN harus berupa angka.',

        'no_telp.regex'     => 'No HP siswa hanya boleh angka.',
        'no_telp_ortu.regex'=> 'No HP orang tua hanya boleh angka.',
        'no_telp_wali.regex'=> 'No HP wali hanya boleh angka.',
    ]);

    /*
    |--------------------------------------------------------------------------
    | VALIDASI ANGKATAN
    |--------------------------------------------------------------------------
    */

    $tahunMasuk = date('Y', strtotime($request->tahun_masuk));

    $tahunAktif = TahunAjaran::aktif()->firstOrFail();
    $semesterAktif = Semester::aktif()->firstOrFail();
    $selectedKelasAktif = KelasAktif::with('kelas')
        ->where('id_kelas_aktif', $request->id_kelas_aktif)
        ->where('id_tahun', $tahunAktif->id_tahun)
        ->whereHas('semester', function ($query) use ($semesterAktif) {
            $query->where('nama_semester', $semesterAktif->nama_semester);
        })
        ->first();

    if (! $selectedKelasAktif) {
        return back()
            ->withInput()
            ->withErrors([
                'id_kelas_aktif' => 'Kelas aktif yang dipilih tidak sesuai dengan tahun ajaran dan semester aktif.',
            ]);
    }

    if ($selectedKelasAktif->id_kelas !== $request->id_kelas) {
        return back()
            ->withInput()
            ->withErrors([
                'id_kelas' => 'Kelas awal harus sesuai dengan kelas aktif yang dipilih.',
            ]);
    }

        $tahunAwalAktif = (int) substr(
        $tahunAktif->tahun,
        0,
        4
    );

    /*
    |--------------------------------------------------------------------------
    | VALIDASI SISWA BARU
    |--------------------------------------------------------------------------
    */

    if ($request->status_masuk == 'baru') {

        $emptyGradeXClassIds = KelasAktif::with('kelas')
            ->where('id_tahun', $tahunAktif->id_tahun)
            ->whereHas('semester', function ($query) use ($semesterAktif) {
                $query->where('nama_semester', $semesterAktif->nama_semester);
            })
            ->get()
            ->filter(function ($kelasAktif) {
                if (! str_starts_with((string) ($kelasAktif->kelas->nama_kelas ?? ''), 'X ')) {
                    return false;
                }

                $activeStudents = SiswaKelas::with('siswa')
                    ->where('id_kelas_aktif', $kelasAktif->id_kelas_aktif)
                    ->get()
                    ->filter(fn ($siswaKelas) => strtolower((string) $siswaKelas->siswa?->status_siswa) === Siswa::STATUS_AKTIF)
                    ->count();

                return $activeStudents === 0;
            })
            ->pluck('id_kelas_aktif');

        if ($tahunMasuk != $tahunAwalAktif) {

            return back()
                ->withInput()
                ->withErrors([
                    'tahun_masuk' =>
                    'Siswa baru harus menggunakan tahun masuk sesuai Tahun Ajaran aktif.'
                ]);
        }

        if ($emptyGradeXClassIds->isNotEmpty() && ! $emptyGradeXClassIds->contains($request->id_kelas_aktif)) {
            return back()
                ->withInput()
                ->withErrors([
                    'id_kelas_aktif' => 'Siswa baru wajib diisikan dulu ke rombel kelas X yang masih kosong.',
                ]);
        }

    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE ID SISWA
    |--------------------------------------------------------------------------
    */

    $prefix = 'S' . substr($tahunMasuk, -2) . '3';

    $lastSiswa = Siswa::where(
            'id_siswa',
            'like',
            $prefix . '%'
        )
        ->orderBy('id_siswa', 'desc')
        ->first();

    $urutan = $lastSiswa
        ? ((int) substr($lastSiswa->id_siswa, -3)) + 1
        : 1;

    $idSiswa =
        $prefix .
        str_pad(
            $urutan,
            3,
            '0',
            STR_PAD_LEFT
        );

    /*
    |--------------------------------------------------------------------------
    | SIMPAN SISWA
    |--------------------------------------------------------------------------
    */

    $siswa = Siswa::create([

        'id_siswa'          => $idSiswa,

        'nis'               => $request->nis,
        'nisn'              => $request->nisn,

        'nama_lengkap'      => $request->nama_lengkap,

        'tempat_lahir'      => $request->tempat_lahir,
        'tanggal_lahir'     => $request->tanggal_lahir,

            'jenis_kelamin'     => $this->normalizeJenisKelamin($request->jenis_kelamin),

        'agama'             => $request->agama,

        'anak_ke'           => $request->anak_ke,

        'status_keluarga'   => $request->status_keluarga,

        'alamat'            => $request->alamat,

        'no_telp'           => $request->no_telp,

        // KELAS AWAL MASUK
        'id_kelas'          => $request->id_kelas,

        'tahun_masuk'       => $request->tahun_masuk,
        'status_masuk'      => $request->status_masuk,

        'status_siswa'      => 'aktif',

        'asal_sekolah'      => $request->asal_sekolah,

        'nama_ayah'         => $request->nama_ayah,
        'nama_ibu'          => $request->nama_ibu,

        'alamat_ortu'       => $request->alamat_ortu,
        'no_telp_ortu'      => $request->no_telp_ortu,

        'pekerjaan_ayah'    => $request->pekerjaan_ayah,
        'pekerjaan_ibu'     => $request->pekerjaan_ibu,

        'nama_wali'         => $request->nama_wali,
        'alamat_wali'       => $request->alamat_wali,

        'no_telp_wali'      => $request->no_telp_wali,
        'pekerjaan_wali'    => $request->pekerjaan_wali,
    ]);

    /*
    |--------------------------------------------------------------------------
    | POSISI SISWA SEKARANG
    |--------------------------------------------------------------------------
    */

    $lastSK = SiswaKelas::orderBy(
            'id_siswa_kelas',
            'desc'
        )
        ->first();

    $nomorSK = $lastSK
        ? ((int) substr($lastSK->id_siswa_kelas, -3)) + 1
        : 1;

    $idSiswaKelas =
        'SK' .
        date('y') .
        str_pad(
            $nomorSK,
            4,
            '0',
            STR_PAD_LEFT
        );

    SiswaKelas::create([

        'id_siswa_kelas' => $idSiswaKelas,

        'id_siswa'       => $siswa->id_siswa,

        // KELAS AKTIF SAAT INI
        'id_kelas_aktif' => $request->id_kelas_aktif,
    ]);

    RiwayatKelas::firstOrCreate(
        [
            'id_siswa' => $siswa->id_siswa,
            'id_kelas_aktif' => $request->id_kelas_aktif,
        ],
        [
            'id_riwayat_kelas' => RiwayatKelas::generateId(),
        ]
    );

    return redirect('admin/siswa')
        ->with(
            'success',
            'Data siswa berhasil ditambahkan.'
        );

    }

    public function edit($id)
    {
        $menu = "siswa";

        $siswa = Siswa::with([
            'siswaKelas'
        ])->findOrFail($id);

        $tahunAktif = TahunAjaran::aktif()->firstOrFail();
        $semesterAktif = Semester::aktif()->firstOrFail();

        $kelas = Kelas::orderBy('nama_kelas')->get();
        $kelasAktif = $this->activeKelasAktifWithCounts($tahunAktif, $semesterAktif);
        $emptyGradeXClassIds = $this->emptyGradeXClassIds($kelasAktif);

        return view(
            'admin.siswa.edit',
            compact(
                'menu',
                'siswa',
                'kelas',
                'kelasAktif',
                'emptyGradeXClassIds'
            )
        );
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'nis' => 'required|numeric|unique:siswa,nis,' . $id . ',id_siswa',
            'nisn' => 'nullable|numeric|unique:siswa,nisn,' . $id . ',id_siswa',

            'nama_lengkap' => 'required|max:200',
            'jenis_kelamin' => 'required',

            'id_kelas_aktif' => 'required',

            'no_telp' => 'nullable|regex:/^[0-9]+$/',
            'no_telp_ortu' => 'nullable|regex:/^[0-9]+$/',
            'no_telp_wali' => 'nullable|regex:/^[0-9]+$/',
        ],[
            'nis.unique' => 'NIS sudah digunakan.',
            'nisn.unique' => 'NISN sudah digunakan.',
            'nisn.numeric' => 'NISN harus berupa angka.',
        ]);

        $tahunAktif = TahunAjaran::aktif()->firstOrFail();
        $semesterAktif = Semester::aktif()->firstOrFail();
        $selectedKelasAktif = KelasAktif::with('kelas')
            ->where('id_kelas_aktif', $request->id_kelas_aktif)
            ->where('id_tahun', $tahunAktif->id_tahun)
            ->whereHas('semester', function ($query) use ($semesterAktif) {
                $query->where('nama_semester', $semesterAktif->nama_semester);
            })
            ->first();

        if (! $selectedKelasAktif) {
            return back()
                ->withInput()
                ->withErrors([
                    'id_kelas_aktif' => 'Kelas aktif yang dipilih tidak sesuai dengan tahun ajaran dan semester aktif.',
                ]);
        }

        if ($selectedKelasAktif->id_kelas !== $request->id_kelas) {
            return back()
                ->withInput()
                ->withErrors([
                    'id_kelas' => 'Kelas awal harus sesuai dengan kelas aktif yang dipilih.',
                ]);
        }

        $kelasAktif = $this->activeKelasAktifWithCounts($tahunAktif, $semesterAktif);
        $emptyGradeXClassIds = $this->emptyGradeXClassIds($kelasAktif);
        $siswaKelasCurrent = SiswaKelas::with('kelasAktif.kelas')
            ->where('id_siswa', $siswa->id_siswa)
            ->first();

        if (
            strtolower((string) $request->status_siswa) === Siswa::STATUS_AKTIF
            && $siswaKelasCurrent
            && $siswaKelasCurrent->id_kelas_aktif !== $request->id_kelas_aktif
        ) {
            $currentClassName = (string) ($siswaKelasCurrent->kelasAktif->kelas->nama_kelas ?? '');
            $currentClassActiveStudents = SiswaKelas::with('siswa')
                ->where('id_kelas_aktif', $siswaKelasCurrent->id_kelas_aktif)
                ->get()
                ->filter(fn ($row) => strtolower((string) $row->siswa?->status_siswa) === Siswa::STATUS_AKTIF)
                ->count();

            if (
                str_starts_with($currentClassName, 'X ')
                && $currentClassActiveStudents <= 1
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'id_kelas_aktif' => 'Siswa ini adalah siswa aktif terakhir di ' . $currentClassName . '. Pindahkan siswa lain dulu atau isi rombel X yang kosong sebelum memindahkan siswa ini.',
                    ]);
            }

            if (
                $emptyGradeXClassIds->isNotEmpty()
                && $emptyGradeXClassIds->contains($siswaKelasCurrent->id_kelas_aktif)
                && ! $emptyGradeXClassIds->contains($request->id_kelas_aktif)
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'id_kelas_aktif' => 'Prioritaskan pengisian rombel kelas X yang masih kosong sebelum memindahkan siswa ke rombel lain.',
                    ]);
            }
        }

        $siswa->update([

            'nis' => $request->nis,
            'nisn' => $request->nisn,

            'id_kelas' => $request->id_kelas,
            'status_masuk' => $request->status_masuk,

            'asal_sekolah' => $request->asal_sekolah,

            'nama_lengkap' => $request->nama_lengkap,

            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,

            'jenis_kelamin' => $this->normalizeJenisKelamin($request->jenis_kelamin),

            'agama' => $request->agama,

            'anak_ke' => $request->anak_ke,

            'status_keluarga' => $request->status_keluarga,

            'alamat' => $request->alamat,

            'no_telp' => $request->no_telp,

            'status_siswa' => $request->status_siswa,

            'asal_sekolah' => $request->asal_sekolah,

            'nama_ayah' => $request->nama_ayah,
            'nama_ibu' => $request->nama_ibu,

            'alamat_ortu' => $request->alamat_ortu,

            'no_telp_ortu' => $request->no_telp_ortu,

            'pekerjaan_ayah' => $request->pekerjaan_ayah,
            'pekerjaan_ibu' => $request->pekerjaan_ibu,

            'nama_wali' => $request->nama_wali,
            'alamat_wali' => $request->alamat_wali,

            'no_telp_wali' => $request->no_telp_wali,

            'pekerjaan_wali' => $request->pekerjaan_wali,
        ]);

        $siswaKelas = SiswaKelas::where(
            'id_siswa',
            $siswa->id_siswa
        )->first();

        if ($siswaKelas) {

            $siswaKelas->update([
                'id_kelas_aktif' => $request->id_kelas_aktif
            ]);

        }

        RiwayatKelas::firstOrCreate(
            [
                'id_siswa' => $siswa->id_siswa,
                'id_kelas_aktif' => $request->id_kelas_aktif,
            ],
            [
                'id_riwayat_kelas' => RiwayatKelas::generateId(),
            ]
        );

        return redirect(
            'admin/siswa/detail/' . $siswa->id_siswa
        )->with(
            'success',
            'Data siswa berhasil diperbarui.'
        );
    }

    private function activeKelasAktifWithCounts(TahunAjaran $tahunAktif, Semester $semesterAktif)
    {
        return KelasAktif::with('kelas')
            ->where('id_tahun', $tahunAktif->id_tahun)
            ->whereHas('semester', function ($query) use ($semesterAktif) {
                $query->where('nama_semester', $semesterAktif->nama_semester);
            })
            ->get()
            ->map(function ($kelasAktif) {
                $kelasAktif->jumlah_siswa_aktif = SiswaKelas::with('siswa')
                    ->where('id_kelas_aktif', $kelasAktif->id_kelas_aktif)
                    ->get()
                    ->filter(fn ($siswaKelas) => strtolower((string) $siswaKelas->siswa?->status_siswa) === Siswa::STATUS_AKTIF)
                    ->count();

                return $kelasAktif;
            })
            ->sortBy([
                fn ($kelasAktif) => str_starts_with((string) ($kelasAktif->kelas->nama_kelas ?? ''), 'X ') ? 0 : 1,
                fn ($kelasAktif) => $kelasAktif->jumlah_siswa_aktif === 0 ? 0 : 1,
                fn ($kelasAktif) => $kelasAktif->kelas->nama_kelas ?? '',
            ])
            ->values();
    }

    private function emptyGradeXClassIds($kelasAktif)
    {
        return $kelasAktif
            ->filter(function ($kelasAktif) {
                return str_starts_with((string) ($kelasAktif->kelas->nama_kelas ?? ''), 'X ')
                    && (int) $kelasAktif->jumlah_siswa_aktif === 0;
            })
            ->pluck('id_kelas_aktif')
            ->values();
    }

    private function normalizeJenisKelamin(?string $jenisKelamin): ?string
    {
        return match (strtolower(trim((string) $jenisKelamin))) {
            'l', 'laki-laki', 'laki laki' => 'Laki-laki',
            'p', 'perempuan' => 'Perempuan',
            default => $jenisKelamin,
        };
    }
}
