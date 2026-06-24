<?php

namespace App\Http\Controllers;

use App\Models\Kehadiran;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\KelasAktif;
use App\Models\JadwalMengajar;
use App\Models\TahunAjaran;
use App\Models\Semester;
use Illuminate\Http\Request;
use App\Http\Requests\StoreKehadiranRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * KehadiranController — Kelola Kehadiran Siswa (Admin)
 *
 * Fitur:
 * - List kelas → list siswa → detail kehadiran per siswa
 * - Input kehadiran baru
 * - Statistik: hadir, sakit, izin, alpa, persentase
 *
 * CATATAN: Edit dan Delete kehadiran DIHAPUS sesuai requirement.
 * Data kehadiran bersifat permanen setelah diinput.
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 *         Dihapus: edit(), update(), destroy(), destroyKelas()
 */
class KehadiranController extends Controller
{
   public function index(Request $request)
    {
        $menu = "kehadiran";

        $tahunAktif = TahunAjaran::aktif()->firstOrFail();
        $semesterAktif = Semester::aktif()->firstOrFail();

        $idTahun = $request->id_tahun ?? $tahunAktif->id_tahun;
        $namaSemester = $request->semester ?? $semesterAktif->nama_semester;
        $search = $request->search;

        $kelas = KelasAktif::with([
                'kelas',
                'guru',
                'tahunAjaran',
                'semester',
                'siswaKelas'
            ])
            ->where('id_tahun', $idTahun)
            ->whereHas('semester', function ($query) use ($namaSemester) {
                $query->where('nama_semester', $namaSemester);
            })

            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->whereHas('kelas', function ($kelas) use ($search) {

                        $kelas->where(
                            'nama_kelas',
                            'like',
                            "%{$search}%"
                        );

                    })

                    ->orWhereHas('guru', function ($guru) use ($search) {

                        $guru->where(
                            'nama_guru',
                            'like',
                            "%{$search}%"
                        );

                    });

                });

            })

            ->whereHas('kelas')
            ->whereHas('guru')
            ->orderBy('id_kelas')
            ->get()

            ->map(function ($item) {

                $siswaIds = $item->siswaKelas->pluck('id_siswa');

                $item->total_tidak_hadir = Kehadiran::whereIn(
                    'id_siswa',
                    $siswaIds
                )
                ->whereIn('status', [
                    'sakit',
                    'izin',
                    'alpa',
                    'Sakit',
                    'Izin',
                    'Alpa'
                ])
                ->count();

                return $item;
            });

        $tahunAjaran = TahunAjaran::orderBy(
            'tahun',
            'desc'
        )->get();

        $semester = $this->masterSemesterOptions();

        return view(
            'admin.kehadiran.index',
            compact(
                'kelas',
                'menu',
                'tahunAjaran',
                'semester',
                'idTahun',
                'namaSemester'
            )
        );
    }

    private function masterSemesterOptions(): Collection
    {
        return collect([
            (object) ['nama_semester' => 'Ganjil'],
            (object) ['nama_semester' => 'Genap'],
        ]);
    }

    public function detail($id)
    {
        $menu = "kehadiran";

        $kelasAktif = KelasAktif::with('kelas')->findOrFail($id);

        $kelas = (object) [
            'nama_kelas' => $kelasAktif->kelas->nama_kelas,
            'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
        ];

        $siswa = SiswaKelas::with('siswa')
            ->where('id_kelas_aktif', $id)
            ->get()
            ->map(fn($sk) => (object) [
                'id_siswa' => $sk->siswa->id_siswa,
                'nis' => $sk->siswa->nis,
                'nama_lengkap' => $sk->siswa->nama_lengkap,
                'jenis_kelamin' => $sk->siswa->jenis_kelamin,
            ])
            ->sortBy('nama_lengkap')
            ->values();

        return view('admin.kehadiran.detail', compact('kelas', 'siswa', 'menu'));
    }

    public function detailSiswa($id)
    {
        $menu = "kehadiran";

        $siswa = Siswa::findOrFail($id);

        $kehadiran = Kehadiran::with([
                'jadwalMengajar.mapel',
                'jadwalMengajar.guru',
                'jadwalMengajar.kelasAktif.semester',
                'jadwalMengajar.kelasAktif.tahunAjaran'
            ])
            ->where('id_siswa', $id)
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($k) {

                return (object) [
                    'id_kehadiran' => $k->id_kehadiran,
                    'id_siswa' => $k->id_siswa,
                    'tanggal' => $k->tanggal,
                    'status' => $k->status,

                    'nama_mapel' =>
                        $k->jadwalMengajar->mapel->nama_mapel ?? '-',

                    'nama_guru' =>
                        $k->jadwalMengajar->guru->nama_guru ?? '-',

                    'nama_semester' =>
                        $k->jadwalMengajar->kelasAktif->semester->nama_semester ?? '-',

                    'tahun' =>
                        $k->jadwalMengajar->kelasAktif->tahunAjaran->tahun_ajaran ?? '-',
                ];
            });

        // Statistik
        $sakit = Kehadiran::where('id_siswa', $id)
            ->whereIn('status', ['sakit', 'Sakit'])
            ->count();

        $izin = Kehadiran::where('id_siswa', $id)
            ->whereIn('status', ['izin', 'Izin'])
            ->count();

        $alpa = Kehadiran::where('id_siswa', $id)
            ->whereIn('status', ['alpa', 'Alpa'])
            ->count();

        $total_tidak_hadir = $sakit + $izin + $alpa;

        $total_pertemuan = 100;

        $hadir = $total_pertemuan - $total_tidak_hadir;

        $persentase = $total_pertemuan > 0
            ? round(($hadir / $total_pertemuan) * 100)
            : 0;

        // Posisi siswa saat ini
        $kelas = SiswaKelas::with([
                'kelasAktif',
                'kelasAktif.kelas',
                'kelasAktif.semester',
                'kelasAktif.tahunAjaran'
            ])
            ->where('id_siswa', $id)
            ->first();

        return view(
            'admin.kehadiran.detail-siswa',
            compact(
                'siswa',
                'kehadiran',
                'hadir',
                'sakit',
                'izin',
                'alpa',
                'total_tidak_hadir',
                'persentase',
                'kelas',
                'menu'
            )
        );
    }

    public function create($id)
    {
        $menu = "kehadiran";

        $siswa = Siswa::findOrFail($id);

        $kelasData = SiswaKelas::with(['kelasAktif.kelas'])
            ->where('id_siswa', $id)
            ->first();

        if (!$kelasData) {
            return redirect('admin/kehadiran')
                ->with('error', 'Siswa belum memiliki kelas aktif');
        }

        $kelas = (object) [
            'nama_kelas' => $kelasData->kelasAktif->kelas->nama_kelas,
            'id_kelas_aktif' => $kelasData->kelasAktif->id_kelas_aktif,
        ];

        $jadwal = JadwalMengajar::with(['mapel', 'guru'])
            ->where('id_kelas_aktif', $kelas->id_kelas_aktif)
            ->get()
            ->map(fn($j) => (object) [
                'id_jadwal' => $j->id_jadwal,
                'nama_mapel' => $j->mapel->nama_mapel,
                'nama_guru' => $j->guru->nama_guru,
            ]);

        return view('admin.kehadiran.create', compact('siswa', 'kelas', 'jadwal', 'menu'));
    }

    public function store(StoreKehadiranRequest $request)
    {
        Kehadiran::create([
            'id_kehadiran' => Kehadiran::generateId($request->id_siswa),
            'id_siswa'     => $request->id_siswa,
            'id_jadwal'    => $request->id_jadwal,
            'tanggal'      => $request->tanggal,
            'status'       => $request->status,
        ]);

        return redirect('admin/kehadiran/detail/' . $request->id_siswa)
            ->with('success', 'Data kehadiran berhasil ditambahkan');
    }

    /**
     * Edit dan Delete DIHAPUS sesuai requirement:
     * "data kehadiran tidak bisa dihapus dan diedit"
     *
     * Method edit(), update(), destroy(), destroyKelas() sudah dihapus.
     */
}
