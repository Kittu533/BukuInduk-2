<?php

namespace App\Http\Controllers;

use App\Models\KenaikanKelas;
use App\Models\Siswa;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\KelasAktif;
use App\Models\RiwayatKelas;
use App\Models\SiswaKelas;
use App\Services\AcademicYearRolloverService;
use Illuminate\Http\Request;
use InvalidArgumentException;

/**
 * KenaikanKelasController — Kelola Kenaikan/Kelulusan Siswa (Admin)
 *
 * Admin dapat menentukan status siswa per tahun ajaran:
 * - naik (Naik Kelas)
 * - tidak_naik (Tidak Naik)
 * - lulus (Lulus)
 *
 * Data ini muncul di kolom "Kenaikan/Kelulusan" pada PDF Hasil Belajar.
 *
 * @legacy Fitur baru (31 Mei 2026)
 */
class KenaikanKelasController extends Controller
{
    public function prosesTahunBaru(AcademicYearRolloverService $service)
    {
        $tahun = TahunAjaran::aktif()->first();

        if (! $tahun) {
            return redirect()
                ->back()
                ->with('error', 'Aktifkan tahun ajaran yang sedang berjalan terlebih dahulu.');
        }

        $semesterAktif = $this->resolveSourceSemester($tahun);
        $semesterTujuan = Semester::where('nama_semester', 'Ganjil')->firstOrFail();
        $targetTahun = TahunAjaran::where('id_tahun', '>', $tahun->id_tahun)
            ->orderBy('id_tahun')
            ->first();

        if (! $targetTahun) {
            return redirect()
                ->back()
                ->with('error', 'Tahun ajaran baru belum tersedia');
        }

        try {
            $summary = $service->process(
                $tahun,
                $targetTahun,
                $semesterAktif,
                $semesterTujuan
            );
        } catch (InvalidArgumentException $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->back()
            ->with(
                'success',
                "Proses tahun ajaran baru selesai. {$summary['promoted']} siswa naik kelas, {$summary['graduated']} siswa masuk alumni."
            );
    }

    private function resolveSourceSemester(TahunAjaran $tahun): Semester
    {
        $preferredNames = ['Genap', 'Ganjil'];

        foreach ($preferredNames as $semesterName) {
            $semester = Semester::where('nama_semester', $semesterName)->first();

            if (! $semester) {
                continue;
            }

            $hasClasses = KelasAktif::where('id_tahun', $tahun->id_tahun)
                ->whereHas('semester', function ($query) use ($semesterName) {
                    $query->where('nama_semester', $semesterName);
                })
                ->exists();

            if ($hasClasses) {
                return $semester;
            }
        }

        $fallback = KelasAktif::with('semester')
            ->where('id_tahun', $tahun->id_tahun)
            ->whereHas('semester')
            ->orderByDesc('id_semester')
            ->first();

        if ($fallback?->semester) {
            return $fallback->semester;
        }

        return Semester::aktif()->firstOrFail();
    }

    public function index()
{
    $menu = "kenaikan";

    $tahun = TahunAjaran::aktif()->firstOrFail();
    $semester = Semester::aktif()->firstOrFail();
    $targetTahun = TahunAjaran::where('id_tahun', '>', $tahun->id_tahun)
        ->orderBy('id_tahun')
        ->first();
    $sourceSemester = $this->resolveSourceSemester($tahun);
    $gradeXActiveStudents = KelasAktif::with(['kelas', 'semester', 'siswaKelas.siswa'])
        ->where('id_tahun', $tahun->id_tahun)
        ->whereHas('semester', function ($query) use ($sourceSemester) {
            $query->where('nama_semester', $sourceSemester->nama_semester);
        })
        ->get()
        ->filter(function ($kelasAktif) {
            return str_starts_with((string) ($kelasAktif->kelas->nama_kelas ?? ''), 'X ');
        });
    $gradeXActiveStudentsTotal = $gradeXActiveStudents
        ->sum(function ($kelasAktif) {
            return $kelasAktif->siswaKelas
                ->filter(fn ($siswaKelas) => strtolower((string) $siswaKelas->siswa?->status_siswa) === Siswa::STATUS_AKTIF)
                ->count();
        });
    $emptyGradeXClasses = $gradeXActiveStudents
        ->filter(function ($kelasAktif) {
            $activeStudents = $kelasAktif->siswaKelas
                ->filter(fn ($siswaKelas) => strtolower((string) $siswaKelas->siswa?->status_siswa) === Siswa::STATUS_AKTIF)
                ->count();

            return $activeStudents === 0;
        })
        ->map(fn ($kelasAktif) => $kelasAktif->kelas->nama_kelas ?? null)
        ->filter()
        ->values();
    $gradeXIntakeReady = $emptyGradeXClasses->isEmpty();
    $usesFallbackSemester = strtolower((string) $sourceSemester->nama_semester) !== 'genap';
    $canProcessTahunBaru = (bool) $targetTahun && $gradeXIntakeReady;

    $kelas = KelasAktif::with([
            'kelas',
            'guru',
            'tahunAjaran',
            'semester',
            'siswaKelas'
        ])
        ->where('id_tahun', $tahun->id_tahun)
        ->whereHas('semester', function ($query) use ($semester) {
            $query->where('nama_semester', $semester->nama_semester);
        })
        ->whereHas('kelas')
        ->whereHas('guru')
        ->get()
        ->map(function ($item) {

            $item->jumlah_siswa =
                $item->siswaKelas->count();

            return $item;
        })
        ->sortBy(fn ($item) => $item->kelas->nama_kelas)
        ->values();

    return view(
        'admin.kenaikan.index',
        compact(
            'kelas',
            'menu',
            'tahun',
            'semester',
            'targetTahun',
            'sourceSemester',
            'gradeXActiveStudentsTotal',
            'gradeXIntakeReady',
            'emptyGradeXClasses',
            'usesFallbackSemester',
            'canProcessTahunBaru'
        )
    );
}

    public function detail($id_kelas_aktif)
{
    $menu = "kenaikan";

    $kelasAktif = KelasAktif::with([
        'kelas',
        'semester',
        'tahunAjaran'
    ])->findOrFail($id_kelas_aktif);

    $kelas = (object)[
        'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
        'nama_kelas'     => $kelasAktif->kelas->nama_kelas ?? '-',
        'semester'       => $kelasAktif->semester->nama_semester ?? '-',
        'tahun'          => $kelasAktif->tahunAjaran->tahun ?? '-',
    ];

    $siswa = SiswaKelas::with('siswa')
        ->where('id_kelas_aktif', $id_kelas_aktif)
        ->get();

    return view(
        'admin.kenaikan.detail',
        compact(
            'kelas',
            'siswa',
            'menu'
        )
    );
}

    public function show($id_siswa)
    {
        $menu = "kenaikan";

        $siswa = Siswa::findOrFail($id_siswa);

        $tahunDiikuti = RiwayatKelas::with(
                'kelasAktif.tahunAjaran'
            )
            ->where('id_siswa', $id_siswa)
            ->get()
            ->pluck('kelasAktif.tahunAjaran')
            ->filter()
            ->unique('id_tahun')
            ->values();

        $kenaikanList = KenaikanKelas::where(
                'id_siswa',
                $id_siswa
            )
            ->get()
            ->keyBy('id_tahun');

        return view(
            'admin.kenaikan.show',
            compact(
                'siswa',
                'tahunDiikuti',
                'kenaikanList',
                'menu'
            )
        );
    }

    public function update(Request $request, $id_siswa)
    {
        $request->validate([
            'tahun' => 'required|array',
            'tahun.*' => 'required|in:naik,tidak_naik,lulus'
        ]);

        foreach ($request->tahun as $id_tahun => $status) {

            KenaikanKelas::updateOrCreate(
                [
                    'id_siswa' => $id_siswa,
                    'id_tahun' => $id_tahun
                ],
                [
                    'status' => $status
                ]
            );
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'Status kenaikan berhasil diperbarui'
            );
    }
}
