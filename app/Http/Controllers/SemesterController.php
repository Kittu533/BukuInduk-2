<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\SemesterProgressionService;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use InvalidArgumentException;

/**
 * SemesterController — Kelola Semester (Admin)
 *
 * Fitur:
 * - List semester dengan tahun ajaran
 * - Auto-generate semester (toggle Ganjil/Genap)
 * - Set semester aktif
 * - Edit (termasuk batas_edit_nilai) 
 *
 * @legacy Refactored dari DB::table() ke Eloquent (30 Mei 2026)
 *         Ditambahkan field batas_edit_nilai untuk fitur deadline edit nilai guru
 */
class SemesterController extends Controller
{
    public function index()
    {
        $menu = "semester";
        Siswa::syncAutoDropOut();

        $tahunAktif = TahunAjaran::aktif()->first();
        $semesterAktif = Semester::aktif()->first();
        $canProcessSemesterBaru = $tahunAktif
            && $semesterAktif
            && strtolower((string) $semesterAktif->nama_semester) === 'ganjil';
        $semester = $this->buildMasterSemesterRows($tahunAktif, $semesterAktif);

        return view(
            'admin.semester.index',
            compact(
                'semester',
                'menu',
                'tahunAktif',
                'semesterAktif',
                'canProcessSemesterBaru'
            )
        );
    }

    public function prosesSemesterBaru(SemesterProgressionService $service)
    {
        $tahunAktif = TahunAjaran::aktif()->first();
        $semesterAktif = Semester::aktif()->first();

        if (! $tahunAktif) {
            return redirect('/admin/semester')
                ->with('error', 'Aktifkan tahun ajaran yang sedang berjalan terlebih dahulu.');
        }

        if (! $semesterAktif) {
            return redirect('/admin/semester')
                ->with('error', 'Aktifkan semester terlebih dahulu sebelum memproses semester baru.');
        }

        if (strtolower($semesterAktif->nama_semester) !== 'ganjil') {
            return redirect('/admin/semester')
                ->with('error', 'Proses semester baru hanya boleh dijalankan saat semester aktif masih Ganjil.');
        }

        $semesterGenap = Semester::where('nama_semester', 'Genap')->firstOrFail();

        try {
            $summary = $service->process($tahunAktif, $semesterAktif, $semesterGenap);
        } catch (InvalidArgumentException $exception) {
            return redirect('/admin/semester')
                ->with('error', $exception->getMessage());
        }

        return redirect('/admin/semester')
            ->with(
                'success',
                "Proses semester baru selesai. {$summary['moved']} penempatan siswa dipindahkan dari {$summary['from_semester']} ke {$summary['to_semester']}."
            );
    }

    public function autoSemester()
    {
        return redirect('/admin/semester')
            ->with('error', 'Data semester bersifat tetap: hanya Ganjil dan Genap. Yang berubah hanya semester aktif pada tahun ajaran berjalan.');
    }

    public function setAktif($id)
    {
        Siswa::syncAutoDropOut();

        if (! Semester::hasStatusColumn()) {
            return redirect('/admin/semester')
                ->with('success', 'Kolom status semester belum tersedia, data aktif tidak diubah');
        }

        Semester::query()->update(['status' => 'tidak']);
        Semester::where('id_semester', $id)->update(['status' => 'aktif']);

        return redirect('/admin/semester')
            ->with('success', 'Semester berhasil diaktifkan');
    }

    private function buildMasterSemesterRows(?TahunAjaran $tahunAktif, ?Semester $semesterAktif): Collection
    {
        $masterSemester = Semester::query()
            ->whereIn('nama_semester', ['Ganjil', 'Genap'])
            ->orderBy('id_semester')
            ->get()
            ->groupBy('nama_semester');

        return collect(['Ganjil', 'Genap'])->map(function (string $namaSemester) use ($masterSemester, $tahunAktif, $semesterAktif) {
            $record = $masterSemester->get($namaSemester)?->first();

            return (object) [
                'id_semester' => $record?->id_semester ?? '-',
                'nama_semester' => $namaSemester,
                'tahun_ajaran_terkait' => $tahunAktif?->tahun ?? '-',
                'status_label' => $semesterAktif && strtolower((string) $semesterAktif->nama_semester) === strtolower($namaSemester)
                    ? 'Aktif'
                    : 'Tidak Aktif',
            ];
        });
    }


}
