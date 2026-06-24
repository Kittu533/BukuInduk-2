<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Mail\AkunBaruMail;
use App\Models\Users;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\KelasAktif;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * KelolaAkunController — CRUD Akun User (Admin)
 *
 * Fitur:
 * - List akun dengan search & filter kelas
 * - Tambah akun (admin/guru/orangtua) dengan password di-hash
 * - Edit akun (role, link ke guru/siswa)
 * - Hapus akun
 *
 * CATATAN: Controller ini sudah menggunakan Eloquent + Hash sejak awal.
 * Masih ada beberapa query DB::table() untuk join kompleks.
 *
 * @legacy Sudah menggunakan Eloquent sebagian (sebelum refactor 30 Mei 2026)
 */
class KelolaAkunController extends Controller
{

    public function index(Request $request)
{
    $search = $request->search;

    $kelasFilter = $request->kelas;


    /* =========================
       DATA KELAS
    ========================= */

    $kelas = DB::table('kelas')
                ->orderBy('nama_kelas')
                ->get();


    /* =========================
       DATA USERS
    ========================= */

    $users = Users::leftJoin(
                    'siswa_kelas',
                    'users.id_siswa',
                    '=',
                    'siswa_kelas.id_siswa'
                )

                ->leftJoin(
                    'kelas_aktif',
                    'siswa_kelas.id_kelas_aktif',
                    '=',
                    'kelas_aktif.id_kelas_aktif'
                )

                ->leftJoin(
                    'kelas',
                    'kelas_aktif.id_kelas',
                    '=',
                    'kelas.id_kelas'
                );


    /* =========================
       SEARCH
    ========================= */

    if($search){

        $users->where(function($query) use ($search){

            $query->where(
                        'users.name',
                        'like',
                        "%$search%"
                    )

                  ->orWhere(
                        'users.username',
                        'like',
                        "%$search%"
                    )

                  ->orWhere(
                        'users.email',
                        'like',
                        "%$search%"
                    );

        });

    }


    /* =========================
       FILTER KELAS
    ========================= */

    if($kelasFilter){

        $users->where(
                    'kelas.id_kelas',
                    $kelasFilter
                );

    }


    $users = $users

                ->select('users.*')

                ->distinct()

                ->orderBy('users.created_at', 'asc')

                ->paginate(10)

                ->withQueryString();


    return view(
        'admin.kelola-akun.index',
        compact('users', 'kelas')
    );
}


    public function create()
    {
        $guru = Guru::all();

        $kelas = DB::table('kelas')
            ->orderBy('nama_kelas')
            ->get();


        $siswa = DB::table('siswa_kelas')

                    ->join(
                        'siswa',
                        'siswa_kelas.id_siswa',
                        '=',
                        'siswa.id_siswa'
                    )

                    ->join(
                        'kelas_aktif',
                        'siswa_kelas.id_kelas_aktif',
                        '=',
                        'kelas_aktif.id_kelas_aktif'
                    )

                    ->select(
                        'siswa.id_siswa',
                        'siswa.nama_lengkap',
                        'kelas_aktif.id_kelas'
                    )
                    ->where('siswa.status_siswa', Siswa::STATUS_AKTIF)

                    ->get();


        return view(
            'admin.kelola-akun.create',
            compact('guru', 'kelas', 'siswa')
        );
    }



    public function store(Request $request)
    {
        $request->validate([

            'name'     => 'required',
            'username' => 'required|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required'

        ]);

        $id_users = Users::generateId($request->role);


        /* =========================
           SIMPAN DATA
        ========================= */

        Users::create([

            'id_users' => $id_users,

            'name' => $request->name,

            'username' => $request->username,

            'email' => $request->email,

            'password' => Hash::make(
                $request->password
            ),

            'role' => $request->role,

            'id_guru' => $request->id_guru,

            'id_siswa' => $request->id_siswa,

        ]);


        /* =========================
           KIRIM EMAIL KREDENSIAL
        ========================= */

        try {
            Mail::to($request->email)->send(
                new AkunBaruMail(
                    $request->name,
                    $request->username,
                    $request->password,
                    $request->role
                )
            );

            $message = 'Data akun berhasil ditambahkan & email kredensial telah dikirim';
        } catch (\Exception $e) {
            // Log error tapi tetap lanjutkan (akun sudah dibuat)
            Log::warning('Gagal kirim email akun baru: ' . $e->getMessage());
            $message = 'Data akun berhasil ditambahkan (email gagal dikirim — cek konfigurasi mail)';
        }

        return redirect('admin/kelola-akun')
                ->with(
                    'success',
                    $message
                );
    }



    public function edit($id)
{
    $user = Users::findOrFail($id);


    /* =========================
       DATA GURU
    ========================= */

    $guru = Guru::all();


    /* =========================
       DATA KELAS
    ========================= */

    $kelas = DB::table('kelas')
                ->orderBy('nama_kelas')
                ->get();


    /* =========================
       DATA SISWA
    ========================= */

    $siswa = DB::table('siswa_kelas')

                ->join(
                    'siswa',
                    'siswa_kelas.id_siswa',
                    '=',
                    'siswa.id_siswa'
                )

                ->join(
                    'kelas_aktif',
                    'siswa_kelas.id_kelas_aktif',
                    '=',
                    'kelas_aktif.id_kelas_aktif'
                )

                ->select(
                    'siswa.id_siswa',
                    'siswa.nama_lengkap',
                    'kelas_aktif.id_kelas'
                )
                ->where('siswa.status_siswa', Siswa::STATUS_AKTIF)

                ->get();


    /* =========================
       KELAS TERPILIH
    ========================= */

    $kelasSelected = null;

    if($user->id_siswa){

        $kelasSelected = DB::table('siswa_kelas')

            ->join(
                'kelas_aktif',
                'siswa_kelas.id_kelas_aktif',
                '=',
                'kelas_aktif.id_kelas_aktif'
            )

            ->where(
                'siswa_kelas.id_siswa',
                $user->id_siswa
            )

            ->value('kelas_aktif.id_kelas');

    }


    return view(
        'admin.kelola-akun.edit',
        compact(
            'user',
            'guru',
            'kelas',
            'siswa',
            'kelasSelected'
        )
    );
}

    public function update(Request $request, $id)
{
    $user = Users::findOrFail($id);

    $request->validate([

        'name' => 'required',

        'username' =>
            'required|unique:users,username,' .
            $user->id_users .
            ',id_users',

        'email' =>
            'required|email|unique:users,email,' .
            $user->id_users .
            ',id_users',

        'role' => 'required',
        'password' => 'nullable|min:6',

    ]);


    $data = [

        'name' => $request->name,

        'username' => $request->username,

        'email' => $request->email,

        'role' => $request->role,

        'id_guru' => null,

        'id_siswa' => null

    ];


    /* =========================
       ROLE GURU
    ========================= */

    if($request->role == 'guru'){

        $data['id_guru'] = $request->id_guru;

    }


    /* =========================
       ROLE ORANG TUA
    ========================= */

    if($request->role == 'orangtua'){

        $data['id_siswa'] = $request->id_siswa;

    }


    /* =========================
       PASSWORD
    ========================= */

    if($request->password){

        $data['password'] =
            Hash::make($request->password);

    }


    $user->update($data);


    return redirect('admin/kelola-akun')
            ->with(
                'success',
                'Data akun berhasil diupdate'
            );
}


    public function destroy($id)
    {
        $user = Users::findOrFail($id);

        $user->delete();

        return redirect('admin/kelola-akun')
                ->with(
                    'success',
                    'Data akun berhasil dihapus'
                );
    }

}
