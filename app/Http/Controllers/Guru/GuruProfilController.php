<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\Guru;
use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * GuruProfilController — Profil & Ganti Password Guru
 *
 * Fitur:
 * - Lihat profil guru (nama, NIP, jabatan, email, username)
 * - Ganti password (verifikasi password lama + hash baru)
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 *         Ditambahkan fitur ganti password (sebelumnya tidak ada)
 */
class GuruProfilController extends Controller
{
    public function index()
    {
        $guru = Guru::with('user')
            ->where('id_guru', session('id_guru'))
            ->firstOrFail();

        return view('guru.profil.index_profil', compact('guru'));
    }

    public function formPassword()
    {
        return view('guru.profil.ganti_password');
    }

    public function updatePassword(ChangePasswordRequest $request)
    {
        $user = Users::where('id_guru', session('id_guru'))->firstOrFail();

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->with('error', 'Password lama tidak cocok');
        }

        $user->update([
            'password' => Hash::make($request->password_baru),
        ]);

        return back()->with('success', 'Password berhasil diubah');
    }
}
