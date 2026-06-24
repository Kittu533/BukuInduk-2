<?php

namespace App\Services;

use App\Models\KelasAktif;
use App\Models\RiwayatKelas;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SemesterProgressionService
{
    public function process(TahunAjaran $tahunAjaran, Semester $currentSemester, Semester $targetSemester): array
    {
        $currentClasses = KelasAktif::with(['siswaKelas.siswa'])
            ->where('id_tahun', $tahunAjaran->id_tahun)
            ->whereHas('semester', function ($query) use ($currentSemester) {
                $query->where('nama_semester', $currentSemester->nama_semester);
            })
            ->get();

        if ($currentClasses->isEmpty()) {
            throw new InvalidArgumentException(
                'Tidak ada kelas aktif pada tahun ajaran ' . $tahunAjaran->tahun .
                ' semester ' . $currentSemester->nama_semester . '. Proses semester dibatalkan.'
            );
        }

        return DB::transaction(function () use ($tahunAjaran, $currentSemester, $targetSemester, $currentClasses) {
            $targetClasses = $this->ensureTargetClasses($tahunAjaran, $targetSemester, $currentClasses);
            $moved = 0;

            foreach ($currentClasses as $currentClass) {
                $targetClass = $targetClasses->get($currentClass->id_kelas);

                if (! $targetClass) {
                    continue;
                }

                foreach ($currentClass->siswaKelas as $assignment) {
                    $siswa = $assignment->siswa;

                    if (! $siswa || strtolower((string) $siswa->status_siswa) !== Siswa::STATUS_AKTIF) {
                        continue;
                    }

                    $assignment->update([
                        'id_kelas_aktif' => $targetClass->id_kelas_aktif,
                    ]);

                    $payload = [
                        'id_kelas' => $targetClass->id_kelas,
                    ];

                    if (Siswa::hasIdKelasAktifColumn()) {
                        $payload['id_kelas_aktif'] = $targetClass->id_kelas_aktif;
                    }

                    $siswa->forceFill($payload)->save();

                    RiwayatKelas::firstOrCreate(
                        [
                            'id_siswa' => $siswa->id_siswa,
                            'id_kelas_aktif' => $targetClass->id_kelas_aktif,
                        ],
                        [
                            'id_riwayat_kelas' => $this->nextRiwayatKelasId(),
                        ]
                    );

                    $moved++;
                }
            }

            $this->activateSemester($targetSemester, $tahunAjaran);

            return [
                'moved' => $moved,
                'generated_classes' => $targetClasses->count(),
                'from_semester' => $currentSemester->nama_semester,
                'to_semester' => $targetSemester->nama_semester,
            ];
        });
    }

    private function ensureTargetClasses(TahunAjaran $tahunAjaran, Semester $targetSemester, $currentClasses)
    {
        $existingTargets = KelasAktif::where('id_tahun', $tahunAjaran->id_tahun)
            ->whereHas('semester', function ($query) use ($targetSemester) {
                $query->where('nama_semester', $targetSemester->nama_semester);
            })
            ->get()
            ->keyBy('id_kelas');

        foreach ($currentClasses as $currentClass) {
            if ($existingTargets->has($currentClass->id_kelas)) {
                continue;
            }

            $newClass = KelasAktif::create([
                'id_kelas_aktif' => $this->nextKelasAktifId(),
                'id_kelas' => $currentClass->id_kelas,
                'id_tahun' => $tahunAjaran->id_tahun,
                'id_semester' => $targetSemester->id_semester,
                'id_guru' => $currentClass->id_guru,
            ]);

            $existingTargets->put($newClass->id_kelas, $newClass);
        }

        return $existingTargets;
    }

    private function activateSemester(Semester $targetSemester, TahunAjaran $tahunAjaran): void
    {
        if (Semester::hasStatusColumn()) {
            Semester::query()->update(['status' => 'tidak']);

            Semester::where('id_semester', $targetSemester->id_semester)
                ->update(['status' => 'aktif']);
        }

        if (Semester::hasIdTahunColumn()) {
            Semester::where('id_semester', $targetSemester->id_semester)
                ->update(['id_tahun' => $tahunAjaran->id_tahun]);
        }
    }

    private function nextKelasAktifId(): string
    {
        $max = KelasAktif::pluck('id_kelas_aktif')
            ->map(fn ($id) => (int) preg_replace('/\D+/', '', (string) $id))
            ->max() ?? 0;

        return 'KA' . str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }

    private function nextRiwayatKelasId(): string
    {
        $max = RiwayatKelas::pluck('id_riwayat_kelas')
            ->map(fn ($id) => (int) preg_replace('/\D+/', '', (string) $id))
            ->max() ?? 0;

        return 'R' . str_pad((string) ($max + 1), 6, '0', STR_PAD_LEFT);
    }
}
