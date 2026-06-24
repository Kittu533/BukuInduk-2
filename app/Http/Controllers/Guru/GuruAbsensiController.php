<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JadwalMengajar;
use App\Models\Kehadiran;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Http\Requests\StoreKehadiranRequest;
use Illuminate\Support\Facades\DB;

/**
 * GuruAbsensiController — Input Kehadiran oleh Guru Mapel
 *
 * Fitur:
 * - List kelas & siswa per jadwal
 * - Input kehadiran (Hadir/Sakit/Izin/Alpa)
 * - Detail riwayat kehadiran per siswa
 *
 * CATATAN: Kehadiran TIDAK bisa diedit/dihapus setelah disimpan.
 * Hanya admin yang bisa menambah data kehadiran baru.
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 *         Dihapus method edit/delete sesuai requirement
 */
class GuruAbsensiController extends Controller
{
    public function index()
    {
        $idGuru = session('id_guru');
        $tahunAktif = TahunAjaran::aktif()->firstOrFail();
        $semesterAktif = Semester::aktif()->firstOrFail();

        $jadwal = JadwalMengajar::with([
                'kelasAktif.kelas',
                'mapel'
            ])
            ->where('id_guru', $idGuru)
            ->whereHas('kelasAktif', function ($q) use ($tahunAktif, $semesterAktif) {
                $q->where('id_tahun', $tahunAktif->id_tahun)
                    ->whereHas('semester', function ($semesterQuery) use ($semesterAktif) {
                        $semesterQuery->where('nama_semester', $semesterAktif->nama_semester);
                    });
            })
            ->get();

        return view(
            'guru.absensi.index_absensi',
            compact('jadwal')
        );
    }

   public function siswa($id)
{
    $jadwal = JadwalMengajar::with([
        'kelasAktif.kelas',
        'kelasAktif.tahunAjaran',
        'kelasAktif.semester',
        'mapel',
    ])->findOrFail($id);

    /*
    |--------------------------------------------------------------------------
    | STATUS EDIT
    |--------------------------------------------------------------------------
    */

    $canEdit = true;

    $detail = (object) [
        'nama_kelas'      => $jadwal->kelasAktif->kelas->nama_kelas,
        'nama_mapel'      => $jadwal->mapel->nama_mapel,
        'id_kelas_aktif'  => $jadwal->kelasAktif->id_kelas_aktif,
        'tahun'           => $jadwal->kelasAktif->tahunAjaran->tahun,
        'nama_semester'   => $jadwal->kelasAktif->semester->nama_semester,
    ];

    $siswa = SiswaKelas::with('siswa')
        ->where(
            'id_kelas_aktif',
            $detail->id_kelas_aktif
        )
        ->get()
        ->map(function ($sk) {

            return (object) [

                'id_siswa'      => $sk->siswa->id_siswa,
                'nama_lengkap'  => $sk->siswa->nama_lengkap,

            ];

        });

    return view(
        'guru.absensi.siswa_absensi',
        compact(
            'detail',
            'siswa',
            'id',
            'canEdit'
        )
    );
}

   public function input($id, $id_siswa)
{
    $jadwal = JadwalMengajar::with([
        'kelasAktif.kelas',
        'kelasAktif.semester',
        'mapel'
    ])->findOrFail($id);

    /*
    |--------------------------------------------------------------------------
    | BLOKIR SISWA YANG SUDAH LULUS
    |--------------------------------------------------------------------------
    */

    $sudahLulus = DB::table('kenaikan_kelas')
        ->where('id_siswa', $id_siswa)
        ->where('status', 'lulus')
        ->exists();

    if ($sudahLulus) {

        return redirect('guru/absensi/' . $id)
            ->with(
                'error',
                'Siswa yang sudah lulus tidak dapat diinput absensinya.'
            );
    }

    $detail = (object) [

        'nama_kelas' =>
            $jadwal->kelasAktif->kelas->nama_kelas,

        'nama_mapel' =>
            $jadwal->mapel->nama_mapel,

        'tahun' =>
            $jadwal->kelasAktif?->tahunAjaran?->tahun,

        'nama_semester' =>
            $jadwal->kelasAktif?->semester?->nama_semester,

    ];

    $siswa = Siswa::findOrFail($id_siswa);

    $absen = Kehadiran::where(
            'id_siswa',
            $id_siswa
        )
        ->where(
            'id_jadwal',
            $id
        )
        ->orderByDesc('tanggal')
        ->first();

    $tanggal = now()->format('Y-m-d');

    return view(
        'guru.absensi.input_absensi',
        [
            'detail'   => $detail,
            'siswa'    => $siswa,
            'id_siswa' => $id_siswa,
            'id'       => $id,
            'tanggal'  => $tanggal,
            'absen'    => $absen,
        ]
    );
}

    /**
     * Simpan kehadiran — TIDAK bisa diedit/dihapus setelah disimpan
     */
    public function simpan(StoreKehadiranRequest $request)
{
    /*
    |--------------------------------------------------------------------------
    | BLOKIR SISWA YANG SUDAH LULUS
    |--------------------------------------------------------------------------
    */

    $sudahLulus = DB::table('kenaikan_kelas')
        ->where('id_siswa', $request->id_siswa)
        ->where('status', 'lulus')
        ->exists();

    if ($sudahLulus) {

        return redirect()->back()
            ->with(
                'error',
                'Siswa yang sudah lulus tidak dapat diinput absensinya.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE ID KEHADIRAN
    |--------------------------------------------------------------------------
    */

    $idKehadiran = Kehadiran::generateId(
        $request->id_siswa
    );

    /*
    |--------------------------------------------------------------------------
    | SIMPAN DATA
    |--------------------------------------------------------------------------
    */

    Kehadiran::create([

        'id_kehadiran' => $idKehadiran,

        'id_siswa' => $request->id_siswa,

        'id_jadwal' => $request->id_jadwal,

        'tanggal' => $request->tanggal,

        'status' => $request->status,

    ]);

    return redirect(
        'guru/absensi/' . $request->id_jadwal
    )->with(
        'success',
        'Data kehadiran berhasil disimpan.'
    );
}

    public function detailAbsensi($id, $id_siswa)
    {
        $jadwal = JadwalMengajar::with([
            'kelasAktif.kelas',
            'kelasAktif.tahunAjaran',
            'kelasAktif.semester',
            'mapel',
        ])->findOrFail($id);

        $detail = (object) [
            'nama_kelas' => $jadwal->kelasAktif->kelas->nama_kelas,
            'nama_mapel' => $jadwal->mapel->nama_mapel,
            'tahun' => $jadwal->kelasAktif->tahunAjaran->tahun,
            'nama_semester' => $jadwal->kelasAktif->semester->nama_semester,
        ];

        $siswa = Siswa::findOrFail($id_siswa);

        $kehadiran = Kehadiran::where('id_siswa', $id_siswa)
            ->where('id_jadwal', $id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('guru.absensi.detail_absensi', compact('detail', 'siswa', 'kehadiran', 'id'));
    }
}
