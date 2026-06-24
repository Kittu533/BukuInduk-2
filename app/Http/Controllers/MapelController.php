<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use Illuminate\Http\Request;

/**
 * MapelController — CRUD Mata Pelajaran (Admin)
 *
 * Fitur:
 * - List semua mata pelajaran
 * - Tambah, edit, hapus mata pelajaran
 * - Kategori: Wajib, Pilihan, Muatan Lokal
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 */
class MapelController extends Controller
{
    public function index(Request $request)
    {
        $menu = "mapel";

        $search = $request->search;
        $semester = $request->semester;

        $query = MataPelajaran::query();

        /*
        |--------------------------------------------------------------------------
        | FILTER SEMESTER
        |--------------------------------------------------------------------------
        */

        if ($semester) {

            $query->where(
                'semester_mapel',
                $semester
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($search) {

            $query->where(function ($q) use ($search) {

                $q->where(
                    'id_mapel',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'nama_mapel',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'kategori_mapel',
                    'like',
                    "%{$search}%"
                );

            });
        }

        $query->orderByRaw("
            REPLACE(
                REPLACE(id_mapel,'-GJ',''),
                '-GP',
                ''
            ) ASC
        ");

        if (MataPelajaran::hasSemesterMapelColumn()) {

            $query->orderByRaw("
                CASE semester_mapel
                    WHEN 'Ganjil' THEN 1
                    ELSE 2
                END
            ");
        }

        $mapel = $query->get();

        return view(
            'admin.mapel.index',
            compact(
                'mapel',
                'menu'
            )
        );
    }

    public function create()
    {
        $menu = "mapel";
        return view('admin.mapel.create', compact('menu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_dasar'      => 'required|numeric',
            'nama_mapel'      => 'required|max:100',
            'kategori_mapel'  => 'required|in:Wajib,Pilihan',
            'tingkat'         => 'required|in:X,XI,XII',
            'semester_mapel'  => 'required|in:Ganjil,Genap',
        ]);

        /*
        |--------------------------------------------------------------------------
        | PREFIX KATEGORI
        |--------------------------------------------------------------------------
        */

        $prefixKategori =
            $request->kategori_mapel == 'Wajib'
                ? 'MW'
                : 'MP';

        /*
        |--------------------------------------------------------------------------
        | PREFIX TINGKAT
        |--------------------------------------------------------------------------
        */

        $prefixTingkat = $request->tingkat;

        /*
        |--------------------------------------------------------------------------
        | KODE SEMESTER
        |--------------------------------------------------------------------------
        */

        $kodeSemester =
            $request->semester_mapel == 'Ganjil'
                ? '1'
                : '2';

        /*
        |--------------------------------------------------------------------------
        | FORMAT KODE DASAR
        |--------------------------------------------------------------------------
        */

        $kodeDasar = str_pad(
            $request->kode_dasar,
            3,
            '0',
            STR_PAD_LEFT
        );

        /*
        |--------------------------------------------------------------------------
        | GENERATE ID MAPEL
        |--------------------------------------------------------------------------
        */

        $idMapel =
            $prefixKategori .
            $prefixTingkat .
            $kodeSemester .
            $kodeDasar;

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DUPLIKAT KODE
        |--------------------------------------------------------------------------
        */

        if (
            MataPelajaran::where(
                'id_mapel',
                $idMapel
            )->exists()
        ) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Kode mapel sudah digunakan.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DUPLIKAT MAPEL
        |--------------------------------------------------------------------------
        */

        $sudahAda = MataPelajaran::where(
                'nama_mapel',
                $request->nama_mapel
            )
            ->where(
                'semester_mapel',
                $request->semester_mapel
            )
            ->where(
                'id_mapel',
                'LIKE',
                $prefixKategori .
                $prefixTingkat .
                '%'
            )
            ->exists();

        if ($sudahAda) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Mapel tersebut sudah ada pada tingkat dan semester yang sama.'
                );
        }

        MataPelajaran::create([

            'id_mapel'         => $idMapel,

            'nama_mapel'       => $request->nama_mapel,

            'kategori_mapel'   => $request->kategori_mapel,

            'semester_mapel'   => $request->semester_mapel,

        ]);

        return redirect('/admin/mapel')
            ->with(
                'success',
                'Data mapel berhasil ditambahkan.'
            );
    }

    public function edit($id)
    {
        $menu = "mapel";
        $data = MataPelajaran::findOrFail($id);

        return view('admin.mapel.edit', compact('data', 'menu'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:50',
            'kategori_mapel' => 'required|in:Wajib,Pilihan,Muatan Lokal',
        ]);

        $mapel = MataPelajaran::findOrFail($id);
        $mapel->update([
            'nama_mapel' => $request->nama_mapel,
            'kategori_mapel' => $request->kategori_mapel,
        ]);

        return redirect('/admin/mapel')
            ->with('success', 'Data mapel berhasil diupdate');
    }

    public function destroy($id)
    {
        MataPelajaran::findOrFail($id)->delete();

        return redirect('/admin/mapel')
            ->with('success', 'Data mapel berhasil dihapus');
    }
}
