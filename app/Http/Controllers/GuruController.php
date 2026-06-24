<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Http\Requests\StoreGuruRequest;
use App\Http\Requests\UpdateGuruRequest;
use Illuminate\Http\Request;

/**
 * GuruController — CRUD Data Guru (Admin)
 *
 * Fitur:
 * - List guru dengan search (nama/NIP/tugas mengajar)
 * - Tambah, edit, hapus data guru
 * - Detail profil guru
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 *         Ditambahkan Form Request validation
 */
class GuruController extends Controller
{
    public function index(Request $request)
    {
        $menu = "guru";

        $search = $request->search;

        $guru = Guru::query()

            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where(
                        'nama_guru',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'nip',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'tugas_mengajar',
                        'like',
                        "%{$search}%"
                    );

                });

            })

            ->orderBy('nama_guru', 'asc')
            ->get();

        return view(
            'admin.guru.index',
            compact(
                'guru',
                'menu'
            )
        );
    }

    public function create()
    {
        $menu = "guru";
        return view('admin.guru.create', compact('menu'));
    }

    public function store(StoreGuruRequest $request)
    {
        Guru::create($request->validated());

        return redirect('/admin/guru')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function detail($id)
    {
        $menu = "guru";
        $guru = Guru::findOrFail($id);

        return view('admin.guru.detail', compact('guru', 'menu'));
    }

    public function edit($id)
    {
        $menu = "guru";
        $data = Guru::findOrFail($id);

        return view('admin.guru.edit', compact('data', 'menu'));
    }

    public function update(UpdateGuruRequest $request, $id)
    {
        $guru = Guru::findOrFail($id);
        $guru->update($request->validated());

        return redirect('/admin/guru')
            ->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        $guru->delete();

        return redirect('/admin/guru')
            ->with('success', 'Data berhasil dihapus');
    }
}
