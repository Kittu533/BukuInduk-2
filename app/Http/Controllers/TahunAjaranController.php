<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\KelasAktif;
use App\Models\Semester;
use Illuminate\Http\Request;

/**
 * TahunAjaranController — Kelola Tahun Ajaran (Admin)
 *
 * Fitur:
 * - List tahun ajaran
 * - Auto-generate tahun baru (increment dari terakhir)
 * - Set tahun aktif (hanya 1 yang aktif)
 * - Hapus tahun ajaran
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 *         Menggunakan TahunAjaran::generateId() dan scope aktif()
 */
class TahunAjaranController extends Controller
{
    public function index()
    {
        $menu = "tahunajaran";
        Siswa::syncAutoDropOut();
        $tahun = TahunAjaran::orderBy('tahun', 'desc')->get();

        return view('admin.tahun_ajaran.index', compact('tahun', 'menu'));
    }

   public function autoTahun()
    {
        Siswa::syncAutoDropOut();

        $last = TahunAjaran::orderBy('id_tahun', 'desc')->first();

        if ($last) {

            $pecah = explode('/', $last->tahun);

            $tahunAwal  = (int) $pecah[0] + 1;
            $tahunAkhir = (int) $pecah[1] + 1;

        } else {

            $tahunAwal  = date('Y');
            $tahunAkhir = date('Y') + 1;
        }

        $payload = [
            'id_tahun' => TahunAjaran::generateId(),
            'tahun'    => $tahunAwal . '/' . $tahunAkhir,
        ];

        if (TahunAjaran::hasStatusColumn()) {
            $payload['status'] = 'tidak';
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN TAHUN AJARAN BARU
        |--------------------------------------------------------------------------
        */

        $tahunBaru = TahunAjaran::create($payload);

        /*
        |--------------------------------------------------------------------------
        | GENERATE KELAS AKTIF SEMESTER GANJIL
        |--------------------------------------------------------------------------
        */

        if ($last) {

            $semesterGanjil = Semester::where(
                'nama_semester',
                'Ganjil'
            )->first();

            if ($semesterGanjil) {

                $kelasLama = KelasAktif::where(
                        'id_tahun',
                        $last->id_tahun
                    )
                    ->where(
                        'id_semester',
                        $semesterGanjil->id_semester
                    )
                    ->get();

                foreach ($kelasLama as $item) {

                    $lastKA = KelasAktif::orderByDesc(
                        'id_kelas_aktif'
                    )->first();

                    $nomor = $lastKA
                        ? ((int) substr($lastKA->id_kelas_aktif, 2)) + 1
                        : 1;

                    $idBaru = 'KA' .
                        str_pad(
                            $nomor,
                            3,
                            '0',
                            STR_PAD_LEFT
                        );

                    KelasAktif::create([
                        'id_kelas_aktif' => $idBaru,
                        'id_kelas'       => $item->id_kelas,
                        'id_tahun'       => $tahunBaru->id_tahun,
                        'id_semester'    => $semesterGanjil->id_semester,
                        'id_guru'        => $item->id_guru,
                        'is_kunci_nilai' => 0,
                    ]);
                }
            }
        }

        return redirect('/admin/tahun-ajaran')
            ->with(
                'success',
                'Tahun ajaran berhasil ditambahkan dan kelas semester ganjil berhasil dibuat'
            );
    }

    public function setAktif($id)
    {
        Siswa::syncAutoDropOut();

        $tahunAjaran = TahunAjaran::findOrFail($id);

        if ($tahunAjaran->is_arsip) {
            return redirect()->back()
                ->with('error', 'Tahun ajaran arsip hanya bisa dilihat dan tidak bisa diaktifkan kembali');
        }

        if (! TahunAjaran::hasStatusColumn()) {
            return redirect()->back()
                ->with('success', 'Kolom status tahun ajaran belum tersedia, data aktif tidak diubah');
        }

        TahunAjaran::query()->update(['status' => 'tidak']);
        $tahunAjaran->update(['status' => 'aktif']);

        return redirect()->back()
            ->with('success', 'Tahun ajaran berhasil diaktifkan');
    }
}
