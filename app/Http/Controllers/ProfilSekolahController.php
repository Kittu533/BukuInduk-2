<?php

namespace App\Http\Controllers;

use App\Models\ProfilSekolah;
use Illuminate\Http\Request;

/**
 * ProfilSekolahController — Kelola Profil Sekolah (Admin)
 *
 * Fitur:
 * - Lihat & edit profil sekolah (nama, alamat, kepsek, logo, dll)
 * - Download cover rapor sebagai PDF
 *
 * @legacy Fitur baru (30 Mei 2026) — tidak ada di versi sebelumnya
 */
class ProfilSekolahController extends Controller
{
    public function index()
    {
        $menu = "profil_sekolah";
        $profil = ProfilSekolah::first();

        return view('admin.profil_sekolah.index', compact('profil', 'menu'));
    }

    public function edit()
    {
        $menu = "profil_sekolah";
        $profil = ProfilSekolah::first();

        return view('admin.profil_sekolah.edit', compact('profil', 'menu'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:200',
            'alamat' => 'nullable|string|max:300',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|string|max:100',
            'kepala_sekolah' => 'nullable|string|max:150',
            'nip_kepala_sekolah' => 'nullable|string|max:30',
            'akreditasi' => 'nullable|string|max:5',
            'npsn' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $profil = ProfilSekolah::first();

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logo', 'public');
            $data['logo'] = $path;
        }

        if ($profil) {
            $profil->update($data);
        } else {
            ProfilSekolah::create($data);
        }

        return redirect('/admin/profil-sekolah')
            ->with('success', 'Profil sekolah berhasil diperbarui');
    }

    /**
     * Download cover rapor (PDF)
     */
    public function downloadCover()
    {
        $profil = ProfilSekolah::first();

        if (!$profil) {
            return back()->with('error', 'Profil sekolah belum diisi');
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.profil_sekolah.cover_pdf', [
            'namaSekolah' => $profil->nama_sekolah,
            'namaKelas' => null,
            'namaSiswa' => null,
            'nis' => null,
        ]);
        $pdf->setPaper('A4');

        return $pdf->download('cover_buku_induk.pdf');
    }
}
