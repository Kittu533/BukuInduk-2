<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\Guru;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;

/**
 * WalasProfileController — Profil & Ganti Password Wali Kelas
 *
 * Fitur:
 * - Lihat profil guru/wali (nama, NIP, jabatan, email, username)
 * - Ganti password (verifikasi password lama + hash baru)
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 *         Ditambahkan fitur ganti password (sebelumnya tidak ada)
 */
class WalasProfileController extends Controller
{
    public function index()
    {
        $id_guru = session('id_guru');

        if (!$id_guru) {
            return redirect('/login');
        }

        $guru = Guru::with('user')
            ->where('id_guru', $id_guru)
            ->firstOrFail();

        return view('wali-kelas.profile-walas.profile', compact('guru'));
    }

    public function formPassword()
    {
        return view('wali-kelas.profile-walas.ganti_password');
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
