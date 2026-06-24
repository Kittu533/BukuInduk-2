<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * ResetPasswordEmailController — Reset Password via Email (untuk user yang sudah login)
 *
 * Fitur:
 * - User logged in bisa request password baru langsung dari halaman profil
 * - Generate password random 8 karakter, hash, simpan ke DB
 * - Kirim password baru ke email user
 *
 * Berbeda dengan ForgotPasswordController:
 * - ForgotPassword → user belum login, input email manual
 * - ResetPasswordEmail → user sudah login, ambil dari session
 *
 * @legacy Fitur baru (30 Mei 2026) — untuk semua role: guru, wali, ortu, admin
 */
class ResetPasswordEmailController extends Controller
{
    public function reset()
    {
        $idUsers = session('id_users');

        if (!$idUsers) {
            return redirect('/');
        }

        $user = Users::find($idUsers);

        if (!$user) {
            return back()->with('error', 'Akun tidak ditemukan');
        }

        if (empty($user->email)) {
            return back()->with('error', 'Akun tidak memiliki email terdaftar');
        }

        // Generate password baru random
        $passwordBaru = Str::random(8);

        // Update DB
        $user->update([
            'password' => Hash::make($passwordBaru),
        ]);

        // Kirim email
        try {
            Mail::to($user->email)->send(
                new ResetPasswordMail($user->name, $user->username, $passwordBaru)
            );

            return back()->with('success', 'Password baru telah dikirim ke email ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Gagal kirim email reset password (logged in): ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email. Hubungi admin.');
        }
    }
}
