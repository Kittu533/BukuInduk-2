<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;

/**
 * OrtuProfilController — Profil & Ganti Password Orang Tua
 *
 * Fitur:
 * - Lihat data lengkap anak (siswa)
 * - Lihat info kelas, wali kelas, tahun ajaran
 * - Ganti password (verifikasi password lama + hash baru)
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 *         Ditambahkan fitur ganti password (sebelumnya tidak ada)
 */
class OrtuProfilController extends Controller
{
     public function index()
    {
        $menu = "profil";
        $id_siswa = session('id_siswa');

        if (!$id_siswa) {
            return redirect('/login');
        }

        $siswa = Siswa::findOrFail($id_siswa);

        $user = Users::where('id_siswa', $id_siswa)->first();

        $kelasData = SiswaKelas::with([
                'kelasAktif.kelas',
                'kelasAktif.tahunAjaran',
                'kelasAktif.semester',
                'kelasAktif.guru'
            ])
            ->where('id_siswa', $id_siswa)
            ->whereHas('kelasAktif.tahunAjaran', function ($q) {
                $q->where('status', 'aktif');
            })
            ->first();

        $kelasSiswa = $kelasData ? (object) [
            'nama_kelas'    => $kelasData->kelasAktif->kelas->nama_kelas ?? '-',
            'tahun'         => $kelasData->kelasAktif->tahunAjaran->tahun ?? '-',
            'nama_semester' => $kelasData->kelasAktif->semester->nama_semester ?? '-',
            'nama_guru'     => $kelasData->kelasAktif->guru->nama_guru ?? '-',
        ] : null;

        return view(
            'orangtua.profil.index_profil',
            compact(
                'menu',
                'siswa',
                'user',
                'kelasSiswa'
            )
        );
    }

    public function formPassword()
    {
        $menu = "profil";
        return view('orangtua.profil.ganti_password', compact('menu'));
    }

    public function updatePassword(ChangePasswordRequest $request)
    {
        $user = Users::where('id_siswa', session('id_siswa'))->firstOrFail();

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->with('error', 'Password lama tidak cocok');
        }

        $user->update([
            'password' => Hash::make($request->password_baru),
        ]);

        return back()->with('success', 'Password berhasil diubah');
    }
}
