<?php

namespace App\Http\Controllers;

use App\Models\Ekstrakurikuler;
use Illuminate\Http\Request;

/**
 * EkskulController — CRUD Ekstrakurikuler (Admin)
 *
 * Fitur:
 * - List semua ekstrakurikuler
 * - Tambah (auto-generate ID), edit, hapus
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 *         Menggunakan Ekstrakurikuler::generateId()
 */
class EkskulController extends Controller
{
    public function index(Request $request)
    {
        $menu = "ekskul";

        $search = $request->search;

        $ekskul = Ekstrakurikuler::when($search, function ($query, $search) {

                $query->where('id_ekskul', 'like', "%{$search}%")
                    ->orWhere('nama_ekskul', 'like', "%{$search}%");

            })
            ->orderByRaw('CAST(SUBSTRING(id_ekskul, 3) AS UNSIGNED) ASC')
            ->get();

        return view(
            'admin.ekskul.index',
            compact(
                'ekskul',
                'menu'
            )
        );
    }
    public function create()
    {
        $menu = "ekskul";
        $kode = Ekstrakurikuler::generateId();

        return view('admin.ekskul.create', compact('menu', 'kode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_ekskul' => 'required|string|max:5|unique:ekstrakurikuler,id_ekskul',
            'nama_ekskul' => 'required|string|max:25',
        ]);

        Ekstrakurikuler::create($request->only('id_ekskul', 'nama_ekskul'));

        return redirect('/admin/ekskul')
            ->with('success', 'Data ekskul berhasil ditambahkan');
    }

    public function edit($id)
    {
        $menu = "ekskul";
        $data = Ekstrakurikuler::findOrFail($id);

        return view('admin.ekskul.edit', compact('data', 'menu'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_ekskul' => 'required|string|max:25',
        ]);

        $ekskul = Ekstrakurikuler::findOrFail($id);
        $ekskul->update($request->only('nama_ekskul'));

        return redirect('/admin/ekskul')
            ->with('success', 'Data ekskul berhasil diupdate');
    }

    public function destroy($id)
    {
        Ekstrakurikuler::findOrFail($id)->delete();

        return redirect('/admin/ekskul')
            ->with('success', 'Data ekskul berhasil dihapus');
    }
}
