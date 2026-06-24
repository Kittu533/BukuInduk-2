<?php

namespace App\Http\Controllers;

use App\Models\KelasAktif;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use Illuminate\Http\Request;

/**
 * KelasAktifController — CRUD Kelas Aktif (Admin)
 *
 * Fitur:
 * - List kelas aktif (filter tahun & semester aktif)
 * - Tambah kelas baru (auto-generate nama kelas & ID)
 * - Generate otomatis kelas aktif dari semester sebelumnya
 * - Edit & hapus kelas aktif
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 *         Menggunakan KelasAktif::generateId(), Kelas::generateId()
 */
class KelasAktifController extends Controller
{
    public function index(Request $request)
    {
        $menu = "kelasaktif";

        Siswa::syncAutoDropOut();

        $tahunAktif = TahunAjaran::aktif()->firstOrFail();
        $semesterAktif = Semester::aktif()->firstOrFail();

        $idTahun = $request->id_tahun ?? $tahunAktif->id_tahun;
        $namaSemester = $request->semester ?? $semesterAktif->nama_semester;

        $kelasaktif = KelasAktif::with([
                'kelas',
                'tahunAjaran',
                'semester',
                'guru'
            ])
            ->where('id_tahun', $idTahun)
            ->whereHas('semester', function ($query) use ($namaSemester) {
                $query->where('nama_semester', $namaSemester);
            })
            ->get()
            ->sortBy(fn ($item) => $item->kelas->nama_kelas ?? '')
            ->values();

        $tahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->get();
        $semester = collect([
            (object) ['nama_semester' => 'Ganjil'],
            (object) ['nama_semester' => 'Genap'],
        ]);

        return view(
            'admin.kelasaktif.index',
            compact(
                'kelasaktif',
                'menu',
                'tahunAjaran',
                'semester',
                'idTahun',
                'namaSemester',
                'semesterAktif'
            )
        );
    }

    public function create()
    {
        $menu = "kelasaktif";

        $tahun = TahunAjaran::aktif()->firstOrFail();
        $semester = Semester::aktif()->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | HANYA BOLEH TAMBAH KELAS DI SEMESTER GANJIL
        |--------------------------------------------------------------------------
        */

        if (strtolower($semester->nama_semester) != 'ganjil') {

            return back()
                ->withInput()
                ->withErrors([
                    'prefix_kelas' =>
                    'Penambahan kelas hanya dapat dilakukan pada awal Tahun Ajaran (Semester Ganjil).'
                ]);
        }

        $kode = KelasAktif::generateId();

        $kelas = Kelas::orderBy('nama_kelas')->get();
        $guru = Guru::orderBy('nama_guru')->get();

        return view(
            'admin.kelasaktif.create',
            compact(
                'kode',
                'tahun',
                'semester',
                'kelas',
                'guru',
                'menu'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kelas_aktif' => 'required|string|unique:kelas_aktif,id_kelas_aktif',
            'prefix_kelas'   => 'required|string',
            'id_guru'        => 'required|string|exists:guru,id_guru',
        ]);

        $tahun = TahunAjaran::aktif()->firstOrFail();
        $semester = Semester::aktif()->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | HANYA BOLEH TAMBAH KELAS DI SEMESTER GANJIL
        |--------------------------------------------------------------------------
        */

        if (strtolower($semester->nama_semester) != 'ganjil') {

            return back()
                ->withInput()
                ->withErrors([
                    'prefix_kelas' =>
                    'Penambahan kelas hanya dapat dilakukan pada Tahun Ajaran Baru.'
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CARI NOMOR KELAS TERAKHIR
        |--------------------------------------------------------------------------
        */

        $prefix = trim($request->prefix_kelas);

        $existingKelas = Kelas::where(
            'nama_kelas',
            'LIKE',
            $prefix . '%'
        )->get();

        $max = 0;

        foreach ($existingKelas as $k) {

            preg_match('/(\d+)$/', $k->nama_kelas, $hasil);

            if (
                isset($hasil[1]) &&
                (int) $hasil[1] > $max
            ) {
                $max = (int) $hasil[1];
            }
        }

        $nomorBaru = $max + 1;

        /*
        |--------------------------------------------------------------------------
        | GENERATE NAMA KELAS
        |--------------------------------------------------------------------------
        */

        $namaKelas = $prefix . str_pad(
            $nomorBaru,
            2,
            '0',
            STR_PAD_LEFT
        );

        /*
        |--------------------------------------------------------------------------
        | GENERATE ID KELAS
        |--------------------------------------------------------------------------
        */

        if (str_starts_with($prefix, 'XII')) {

            $idKelas = 'KXII' . str_pad(
                $nomorBaru,
                2,
                '0',
                STR_PAD_LEFT
            );

        } elseif (str_starts_with($prefix, 'XI')) {

            $idKelas = 'KXI' . str_pad(
                $nomorBaru,
                2,
                '0',
                STR_PAD_LEFT
            );

        } else {

            $idKelas = 'KX' . str_pad(
                $nomorBaru,
                2,
                '0',
                STR_PAD_LEFT
            );
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DUPLIKAT ID
        |--------------------------------------------------------------------------
        */

        if (Kelas::where('id_kelas', $idKelas)->exists()) {

            return back()
                ->withInput()
                ->withErrors([
                    'prefix_kelas' =>
                        'ID kelas ' . $idKelas . ' sudah digunakan.'
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN KELAS
        |--------------------------------------------------------------------------
        */

        Kelas::create([
            'id_kelas'   => $idKelas,
            'nama_kelas' => $namaKelas,
        ]);

        /*
        |--------------------------------------------------------------------------
        | SIMPAN KELAS AKTIF
        |--------------------------------------------------------------------------
        */

        KelasAktif::create([
            'id_kelas_aktif' => $request->id_kelas_aktif,
            'id_kelas'       => $idKelas,
            'id_tahun'       => $tahun->id_tahun,
            'id_semester'    => $semester->id_semester,
            'id_guru'        => $request->id_guru,
        ]);

        return redirect('/admin/kelasaktif')
            ->with(
                'success',
                'Data berhasil ditambahkan'
            );
    }

    public function generate()
    {
        Siswa::syncAutoDropOut();

        $tahun = TahunAjaran::aktif()->firstOrFail();
        $semester = Semester::aktif()->firstOrFail();

        $exists = KelasAktif::where('id_tahun', $tahun->id_tahun)
            ->where('id_semester', $semester->id_semester)
            ->exists();

        if ($exists) {
            return redirect('/admin/kelasaktif')
                ->with('success', 'Kelas aktif semester ini sudah ada');
        }

        $dataLama = KelasAktif::orderBy('id_kelas_aktif')->get();

        foreach ($dataLama as $d) {
            KelasAktif::create([
                'id_kelas_aktif' => KelasAktif::generateId(),
                'id_kelas' => $d->id_kelas,
                'id_tahun' => $tahun->id_tahun,
                'id_semester' => $semester->id_semester,
                'id_guru' => $d->id_guru,
            ]);
        }

        return redirect('/admin/kelasaktif')
            ->with('success', 'Generate kelas aktif berhasil');
    }

    public function edit($id)
    {
        $menu = "kelasaktif";

        $data = KelasAktif::with('kelas')->findOrFail($id);
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $guru = Guru::orderBy('nama_guru')->get();
        $tahun = TahunAjaran::aktif()->first();
        $semester = Semester::aktif()->first();

        return view('admin.kelasaktif.edit', compact('data', 'kelas', 'guru', 'tahun', 'semester', 'menu'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_guru' => 'required|exists:guru,id_guru',
        ]);

        $kelasAktif = KelasAktif::findOrFail($id);

        $kelasAktif->update([
            'id_guru' => $request->id_guru,
        ]);

        return redirect('/admin/kelasaktif')
            ->with(
                'success',
                'Data kelas berhasil diperbarui.'
            );
    }
}
