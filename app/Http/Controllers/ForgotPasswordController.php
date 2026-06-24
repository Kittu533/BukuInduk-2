<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * ForgotPasswordController — Lupa Password
 *
 * Fitur:
 * - Form input email untuk reset password
 * - Generate password baru random + kirim via email
 * - User wajib login & ganti password lagi setelah reset
 *
 * @legacy Fitur baru (30 Mei 2026)
 */
class ForgotPasswordController extends Controller
{
    /**
     * Tampilkan form lupa password
     */
    public function showForm()
    {
        return view('auth.forgot_password');
    }

    /**
     * Proses reset password — kirim password baru via email
     */
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email tidak terdaftar di sistem',
        ]);

        $user = Users::where('email', $request->email)->first();

        // Generate password baru (random 8 karakter)
        $passwordBaru = Str::random(8);

        // Update password (hash)
        $user->update([
            'password' => Hash::make($passwordBaru),
        ]);

        // Kirim email
        try {
            Mail::to($user->email)->send(
                new ResetPasswordMail(
                    $user->name,
                    $user->username,
                    $passwordBaru
                )
            );

            return back()->with('success', 'Password baru telah dikirim ke email Anda. Silakan cek inbox/spam.');
        } catch (\Exception $e) {
            Log::error('Gagal kirim email reset password: ' . $e->getMessage());

            return back()->with('error', 'Gagal mengirim email. Hubungi admin sekolah.');
        }
    }
}
