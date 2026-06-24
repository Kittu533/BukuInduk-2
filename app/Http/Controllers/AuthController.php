<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

/**
 * AuthController — Login & Logout
 *
 * Fitur:
 * - Login dengan username + password + reCAPTCHA
 * - Password diverifikasi dengan Hash::check() (bcrypt)
 * - Session-based authentication (bukan Laravel Auth)
 * - Redirect berdasarkan role: admin, guru (wali/mapel), orangtua
 *
 * @legacy Refactored (30 Mei 2026)
 *         Fix: password sekarang pakai Hash::check() (sebelumnya plaintext)
 *         Fix: reCAPTCHA secret dipindah ke config/services.php
 */
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi input
        |--------------------------------------------------------------------------
        */
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Verifikasi Google reCAPTCHA
        |--------------------------------------------------------------------------
        */
        $response = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => config('services.recaptcha.secret'),
                'response' => $request->input('g-recaptcha-response'),
            ]
        );

        $result = $response->json();

        if (!$result['success']) {
            return back()->with('error', 'Captcha wajib diisi');
        }

        /*
        |--------------------------------------------------------------------------
        | Login User — dengan Hash::check
        |--------------------------------------------------------------------------
        */
        $user = DB::table('users')
            ->where('username', $request->username)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Username atau password salah');
        }

        if ($user->role == 'orangtua') {
            if (empty($user->id_siswa)) {
                return back()->with('error', 'Akun tidak terhubung dengan siswa');
            }

            $siswa = DB::table('siswa')
                ->where('id_siswa', $user->id_siswa)
                ->first();

            if (! $siswa || strtolower((string) $siswa->status_siswa) !== 'aktif') {
                return back()->with('error', 'Akses siswa sudah tidak aktif');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Session dasar
        |--------------------------------------------------------------------------
        */
        Session::put('login', true);
        Session::put('id_users', $user->id_users);
        Session::put('role', $user->role);

        /*
        |--------------------------------------------------------------------------
        | Nama user per role
        |--------------------------------------------------------------------------
        */
        if ($user->role == 'admin') {
            Session::put('nama_admin', 'SUPER ADMIN');
        } elseif ($user->role == 'orangtua') {
            Session::put('nama_orangtua', $user->name);
            Session::put('nama', $user->name);
        }

        /*
        |--------------------------------------------------------------------------
        | KHUSUS GURU
        |--------------------------------------------------------------------------
        */
        if ($user->role == 'guru') {

            if (empty($user->id_guru)) {
                return back()->with('error', 'Akun guru tidak memiliki id_guru');
            }

            Session::put('id_guru', $user->id_guru);

            $guru = DB::table('guru')
                ->where('id_guru', $user->id_guru)
                ->first();

            Session::put('nama_guru', $guru->nama_guru ?? '-');

            // Cek Wali Kelas
            $isWali = DB::table('kelas_aktif')
                ->where('id_guru', $user->id_guru)
                ->exists();

            // Cek Guru Mapel
            $isGuruMapel = DB::table('jadwal_mengajar')
                ->where('id_guru', $user->id_guru)
                ->exists();

            // Cek Guru Pembina
            $isPembina = DB::table('ekstrakurikuler')
                ->where('id_guru', $user->id_guru)
                ->exists();

            
            Session::put('is_wali', $isWali);
            Session::put('is_guru_mapel', $isGuruMapel);
            Session::put('is_pembina', $isPembina);

            // Redirect ke wali dashboard kalau punya role wali (default)
            // Kalau gak punya role wali, ke guru dashboard
            // Jika memiliki salah satu role guru
            if ($isWali || $isGuruMapel || $isPembina) {
                return redirect('/guru/dashboard');
            }

            return back()->with('error', 'Guru belum memiliki hak akses sistem');
        }

        /*
        |--------------------------------------------------------------------------
        | KHUSUS ORANG TUA
        |--------------------------------------------------------------------------
        */
        if ($user->role == 'orangtua') {
            Session::put('id_siswa', $user->id_siswa);
        }

        /*
        |--------------------------------------------------------------------------
        | Redirect berdasarkan role
        |--------------------------------------------------------------------------
        */
        if ($user->role == 'admin') {
            return redirect('/admin');
        } else {
            return redirect('/orangtua/dashboard');
        }
    }

    public function logout()
    {
        Session::flush();
        return redirect('/');
    }
}
