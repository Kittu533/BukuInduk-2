<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\JadwalMengajar;
use App\Models\MataPelajaran;
use App\Models\NilaiMapel;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalMengajarController extends Controller
{
    // ==========================
    // TAMPIL DATA
    // ==========================
    public function index(Request $request)
    {
        $menu = "jadwal";

        $search = $request->search;
        $semesterFilter = $request->semester;

        $tahun = TahunAjaran::aktif()->firstOrFail();
        $semester = Semester::aktif()->firstOrFail();

        $hasSemesterMapel = MataPelajaran::hasSemesterMapelColumn();

        $jadwal = DB::table('jadwal_mengajar')
            ->join(
                'guru',
                'jadwal_mengajar.id_guru',
                '=',
                'guru.id_guru'
            )
            ->join(
                'mata_pelajaran',
                'jadwal_mengajar.id_mapel',
                '=',
                'mata_pelajaran.id_mapel'
            )
            ->join(
                'kelas_aktif',
                'jadwal_mengajar.id_kelas_aktif',
                '=',
                'kelas_aktif.id_kelas_aktif'
            )
            ->join(
                'semester',
                'kelas_aktif.id_semester',
                '=',
                'semester.id_semester'
            )
            ->where('kelas_aktif.id_tahun', $tahun->id_tahun)
            ->where('semester.nama_semester', $semester->nama_semester)
            ->when(
                $hasSemesterMapel && $semesterFilter,
                function ($query) use ($semesterFilter) {
                    $query->where(
                        'mata_pelajaran.semester_mapel',
                        $semesterFilter
                    );
                }
            )
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('guru.id_guru', 'like', "%{$search}%")
                        ->orWhere('guru.nama_guru', 'like', "%{$search}%")
                        ->orWhere('mata_pelajaran.id_mapel', 'like', "%{$search}%")
                        ->orWhere('mata_pelajaran.nama_mapel', 'like', "%{$search}%");
                });
            })
            ->select(
                'guru.id_guru',
                'guru.nama_guru',
                'mata_pelajaran.id_mapel',
                'mata_pelajaran.nama_mapel',
                $hasSemesterMapel
                    ? 'mata_pelajaran.semester_mapel'
                    : DB::raw('NULL as semester_mapel')
            )
            ->groupBy(
                'guru.id_guru',
                'guru.nama_guru',
                'mata_pelajaran.id_mapel',
                'mata_pelajaran.nama_mapel'
            )
            ->when($hasSemesterMapel, function ($query) {
                $query->groupBy('mata_pelajaran.semester_mapel');
            })
            ->orderBy('guru.nama_guru', 'asc')
            ->get();

        return view(
            'admin.jadwal-mengajar.index',
            compact(
                'jadwal',
                'menu',
                'semesterFilter'
            )
        );
    }

    // ==========================
    // FORM CREATE
    // ==========================
    public function create()
    {
        $menu = "jadwal";

        $tahun = TahunAjaran::aktif()->firstOrFail();
        $semester = Semester::aktif()->firstOrFail();
        $hasSemesterMapel = MataPelajaran::hasSemesterMapelColumn();

        $kode = $this->generateJadwalId();
        $guru = Guru::orderBy('nama_guru', 'asc')->get();

        $mapel = DB::table('mata_pelajaran')
            ->when($hasSemesterMapel, function ($query) use ($semester) {
                $query->where('semester_mapel', $semester->nama_semester)
                    ->orderBy('semester_mapel', 'asc');
            })
            ->orderBy('nama_mapel', 'asc')
            ->get();

        $kelasaktif = $this->activeKelasAktifOptions($tahun, $semester);

        return view(
            'admin.jadwal-mengajar.create',
            compact(
                'kode',
                'guru',
                'mapel',
                'kelasaktif',
                'menu'
            )
        );
    }

    // ==========================
    // SIMPAN DATA
    // ==========================
    public function store(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|string|unique:jadwal_mengajar,id_jadwal',
            'id_guru' => 'required|string|exists:guru,id_guru',
            'id_mapel' => 'required|string|exists:mata_pelajaran,id_mapel',
            'id_kelas_aktif' => 'required|string|exists:kelas_aktif,id_kelas_aktif',
        ]);

        DB::table('jadwal_mengajar')->insert([
            'id_jadwal' => $request->id_jadwal,
            'id_guru' => $request->id_guru,
            'id_mapel' => $request->id_mapel,
            'id_kelas_aktif' => $request->id_kelas_aktif,
        ]);

        NilaiMapel::ensureDraftsForJadwal($request->id_jadwal);

        return redirect('/admin/jadwal-mengajar')
            ->with(
                'success',
                'Data jadwal berhasil ditambahkan'
            );
    }

    // ==========================
    // DETAIL DATA
    // ==========================
    public function detail(Request $request, $id)
    {
        $menu = "jadwal";

        $hasSemesterMapel = MataPelajaran::hasSemesterMapelColumn();
        $tahun = TahunAjaran::aktif()->firstOrFail();
        $semester = Semester::aktif()->firstOrFail();
        $mapelId = $request->query('mapel');

        $detail = DB::table('jadwal_mengajar')
            ->join(
                'guru',
                'jadwal_mengajar.id_guru',
                '=',
                'guru.id_guru'
            )
            ->join(
                'mata_pelajaran',
                'jadwal_mengajar.id_mapel',
                '=',
                'mata_pelajaran.id_mapel'
            )
            ->join(
                'kelas_aktif',
                'jadwal_mengajar.id_kelas_aktif',
                '=',
                'kelas_aktif.id_kelas_aktif'
            )
            ->join(
                'semester',
                'kelas_aktif.id_semester',
                '=',
                'semester.id_semester'
            )
            ->join(
                'kelas',
                'kelas_aktif.id_kelas',
                '=',
                'kelas.id_kelas'
            )
            ->where('jadwal_mengajar.id_guru', $id)
            ->where('kelas_aktif.id_tahun', $tahun->id_tahun)
            ->where('semester.nama_semester', $semester->nama_semester)
            ->when($mapelId, function ($query) use ($mapelId) {
                $query->where('jadwal_mengajar.id_mapel', $mapelId);
            })
            ->select(
                'jadwal_mengajar.id_jadwal',
                'guru.nama_guru',
                'mata_pelajaran.id_mapel',
                'mata_pelajaran.nama_mapel',
                'mata_pelajaran.kategori_mapel',
                $hasSemesterMapel
                    ? 'mata_pelajaran.semester_mapel'
                    : DB::raw('NULL as semester_mapel'),
                'kelas.nama_kelas'
            )
            ->orderBy('kelas.nama_kelas', 'asc')
            ->get();

        if ($detail->isEmpty()) {
            return redirect('/admin/jadwal-mengajar')
                ->with('success', 'Data jadwal tidak ditemukan pada konteks akademik aktif.');
        }

        return view(
            'admin.jadwal-mengajar.detail',
            compact(
                'detail',
                'menu'
            )
        );
    }

    // ==========================
    // FORM EDIT
    // ==========================
    public function edit($id)
    {
        $menu = "jadwalmengajar";
        $hasSemesterMapel = MataPelajaran::hasSemesterMapelColumn();
        $tahun = TahunAjaran::aktif()->firstOrFail();
        $semester = Semester::aktif()->firstOrFail();

        $data = DB::table('jadwal_mengajar')
            ->where('id_jadwal', $id)
            ->first();

        $guru = Guru::orderBy('nama_guru', 'asc')->get();

        $mapel = DB::table('mata_pelajaran')
            ->when($hasSemesterMapel, function ($query) use ($semester, $data) {
                $query->where(function ($builder) use ($semester, $data) {
                    $builder->where('semester_mapel', $semester->nama_semester)
                        ->orWhere('id_mapel', $data->id_mapel);
                })->orderBy('semester_mapel', 'asc');
            })
            ->orderBy('nama_mapel', 'asc')
            ->get();

        $kelasaktif = $this->activeKelasAktifOptions($tahun, $semester, $data->id_kelas_aktif);

        return view(
            'admin.jadwal-mengajar.edit',
            compact(
                'data',
                'guru',
                'mapel',
                'kelasaktif',
                'menu'
            )
        );
    }

    // ==========================
    // UPDATE DATA
    // ==========================
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_guru' => 'required|string|exists:guru,id_guru',
            'id_mapel' => 'required|string|exists:mata_pelajaran,id_mapel',
            'id_kelas_aktif' => 'required|string|exists:kelas_aktif,id_kelas_aktif',
        ]);

        DB::table('jadwal_mengajar')
            ->where('id_jadwal', $id)
            ->update([
                'id_guru' => $request->id_guru,
                'id_mapel' => $request->id_mapel,
                'id_kelas_aktif' => $request->id_kelas_aktif,
            ]);

        NilaiMapel::ensureDraftsForJadwal($id);

        return redirect('/admin/jadwal-mengajar')
            ->with(
                'success',
                'Data jadwal berhasil diupdate'
            );
    }

    // ==========================
    // HAPUS DATA
    // ==========================
    public function destroy($id)
    {
        DB::table('jadwal_mengajar')
            ->where('id_jadwal', $id)
            ->delete();

        return redirect('/admin/jadwal-mengajar')
            ->with(
                'success',
                'Data jadwal berhasil dihapus'
            );
    }

    private function activeKelasAktifOptions(TahunAjaran $tahun, Semester $semester, ?string $includeId = null)
    {
        return DB::table('kelas_aktif')
            ->join(
                'semester',
                'kelas_aktif.id_semester',
                '=',
                'semester.id_semester'
            )
            ->join(
                'kelas',
                'kelas_aktif.id_kelas',
                '=',
                'kelas.id_kelas'
            )
            ->select(
                'kelas_aktif.id_kelas_aktif',
                'kelas.nama_kelas',
                'semester.nama_semester'
            )
            ->where(function ($query) use ($tahun, $semester, $includeId) {
                $query->where(function ($builder) use ($tahun, $semester) {
                    $builder->where('kelas_aktif.id_tahun', $tahun->id_tahun)
                        ->where('semester.nama_semester', $semester->nama_semester);
                });

                if ($includeId) {
                    $query->orWhere('kelas_aktif.id_kelas_aktif', $includeId);
                }
            })
            ->orderBy('kelas.nama_kelas', 'asc')
            ->get();
    }

    private function generateJadwalId(): string
    {
        $lastId = JadwalMengajar::query()
            ->orderBy('id_jadwal', 'desc')
            ->value('id_jadwal');

        if (! $lastId) {
            return 'J000001';
        }

        if (! preg_match('/^(.*?)(\d+)$/', $lastId, $matches)) {
            return $lastId . '1';
        }

        $prefix = $matches[1];
        $number = $matches[2];

        return $prefix . str_pad(
            ((int) $number) + 1,
            strlen($number),
            '0',
            STR_PAD_LEFT
        );
    }
}
