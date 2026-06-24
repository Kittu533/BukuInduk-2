<?php

namespace App\Services;

use App\Models\Alumni;
use App\Models\KelasAktif;
use App\Models\KenaikanKelas;
use App\Models\RiwayatKelas;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunAjaran;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class AcademicYearRolloverService
{
    public function process(
        TahunAjaran $currentYear,
        TahunAjaran $targetYear,
        Semester $currentSemester,
        Semester $targetSemester,
        ?Carbon $tanggalLulus = null
    ): array
    {
        $tanggalLulus ??= now();

        $currentClasses = KelasAktif::with(['kelas', 'siswaKelas.siswa'])
            ->where('id_tahun', $currentYear->id_tahun)
            ->whereHas('semester', function ($query) use ($currentSemester) {
                $query->where('nama_semester', $currentSemester->nama_semester);
            })
            ->get();

        if ($currentClasses->isEmpty()) {
            throw new InvalidArgumentException(
                'Tidak ada kelas aktif pada tahun ajaran ' . $currentYear->tahun .
                ' semester ' . $currentSemester->nama_semester . '. Proses dibatalkan.'
            );
        }

        $this->ensureGradeXIntakeExists($currentClasses, $currentYear, $currentSemester);

        $targetClasses = KelasAktif::with('kelas')
            ->where('id_tahun', $targetYear->id_tahun)
            ->whereHas('semester', function ($query) use ($targetSemester) {
                $query->where('nama_semester', $targetSemester->nama_semester);
            })
            ->get()
            ->keyBy(fn (KelasAktif $kelasAktif) => $kelasAktif->kelas?->nama_kelas);

        $missingTargets = $this->missingTargetClasses($currentClasses, $targetClasses);

        if ($missingTargets !== []) {
            throw new InvalidArgumentException(
                'Kelas tujuan tahun ajaran baru belum lengkap: ' . implode(', ', $missingTargets)
            );
        }

        return DB::transaction(function () use (
            $currentYear,
            $targetYear,
            $currentSemester,
            $targetSemester,
            $tanggalLulus,
            $currentClasses,
            $targetClasses
        ) {
            $summary = [
                'graduated' => 0,
                'promoted' => 0,
            ];

            foreach ($currentClasses as $kelasAktif) {
                $namaKelas = $kelasAktif->kelas?->nama_kelas ?? '';
                $grade = $this->gradeFromClassName($namaKelas);

                foreach ($kelasAktif->siswaKelas as $siswaKelas) {
                    $siswa = $siswaKelas->siswa;

                    if (! $siswa) {
                        continue;
                    }

                    $status = strtolower((string) $siswa->status_siswa);

                    if ($grade === 'XII') {
                        $this->graduate($siswa, $currentYear, $tanggalLulus);
                        $summary['graduated']++;
                        continue;
                    }

                    if ($status !== Siswa::STATUS_AKTIF) {
                        continue;
                    }

                    $targetName = $this->targetClassName($namaKelas);
                    $targetClass = $targetClasses->get($targetName);

                    if (! $targetClass) {
                        continue;
                    }

                    $this->promote($siswa, $targetClass, $currentYear);
                    $summary['promoted']++;
                }
            }

            $this->archiveAndActivateYears($currentYear, $targetYear);
            $this->activateTargetSemester($currentSemester, $targetSemester, $targetYear);

            return $summary;
        });
    }

    private function missingTargetClasses($currentClasses, $targetClasses): array
    {
        return $currentClasses
            ->map(fn (KelasAktif $kelasAktif) => $kelasAktif->kelas?->nama_kelas)
            ->filter()
            ->filter(fn (string $namaKelas) => in_array($this->gradeFromClassName($namaKelas), ['X', 'XI'], true))
            ->map(fn (string $namaKelas) => $this->targetClassName($namaKelas))
            ->filter(fn (?string $targetName) => $targetName && ! $targetClasses->has($targetName))
            ->unique()
            ->values()
            ->all();
    }

    private function ensureGradeXIntakeExists($currentClasses, TahunAjaran $currentYear, Semester $currentSemester): void
    {
        $gradeXClasses = $currentClasses
            ->filter(function (KelasAktif $kelasAktif) {
                return $this->gradeFromClassName($kelasAktif->kelas?->nama_kelas ?? '') === 'X';
            });

        if ($gradeXClasses->isEmpty()) {
            return;
        }

        $kelasXKosong = $gradeXClasses
            ->filter(function (KelasAktif $kelasAktif) {
                $activeStudents = $kelasAktif->siswaKelas
                    ->filter(fn ($siswaKelas) => strtolower((string) $siswaKelas->siswa?->status_siswa) === Siswa::STATUS_AKTIF)
                    ->count();

                return $activeStudents === 0;
            })
            ->map(fn (KelasAktif $kelasAktif) => $kelasAktif->kelas?->nama_kelas)
            ->filter()
            ->values()
            ->all();

        if ($kelasXKosong === []) {
            return;
        }

        throw new InvalidArgumentException(
            'Kelas X pada tahun ajaran ' . $currentYear->tahun .
            ' semester ' . $currentSemester->nama_semester .
            ' belum lengkap. Isi dulu siswa baru/pindahan untuk kelas: ' . implode(', ', $kelasXKosong) .
            ' sebelum memproses tahun ajaran baru berikutnya.'
        );
    }

    private function promote(Siswa $siswa, KelasAktif $targetClass, TahunAjaran $currentYear): void
    {
        $assignment = SiswaKelas::where('id_siswa', $siswa->id_siswa)->first();

        if ($assignment) {
            $assignment->update(['id_kelas_aktif' => $targetClass->id_kelas_aktif]);
        } else {
            SiswaKelas::create([
                'id_siswa_kelas' => $this->nextSiswaKelasId(),
                'id_siswa' => $siswa->id_siswa,
                'id_kelas_aktif' => $targetClass->id_kelas_aktif,
            ]);
        }

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

        $this->upsertKenaikan($siswa->id_siswa, $currentYear->id_tahun, 'naik');
    }

    private function graduate(Siswa $siswa, TahunAjaran $currentYear, Carbon $tanggalLulus): void
    {
        $siswa->forceFill([
            'status_siswa' => Siswa::STATUS_LULUS,
        ])->save();

        SiswaKelas::where('id_siswa', $siswa->id_siswa)->delete();

        Alumni::updateOrCreate(
            ['id_siswa' => $siswa->id_siswa],
            [
                'id_tahun_lulus' => $currentYear->id_tahun,
                'tanggal_lulus' => $tanggalLulus->toDateString(),
                'status_akhir' => Siswa::STATUS_LULUS,
            ]
        );

        $this->upsertKenaikan($siswa->id_siswa, $currentYear->id_tahun, 'lulus');
    }

    private function upsertKenaikan(string $idSiswa, string $idTahun, string $status): void
    {
        $catatan = 'Diproses otomatis saat rollover tahun ajaran pada ' . now()->format('Y-m-d H:i:s');

        $existing = KenaikanKelas::where('id_siswa', $idSiswa)
            ->where('id_tahun', $idTahun)
            ->first();

        if ($existing) {
            $existing->update([
                'status' => $status,
                'catatan' => $catatan,
            ]);
            return;
        }

        $payload = [
            'id_siswa' => $idSiswa,
            'id_tahun' => $idTahun,
            'status' => $status,
            'catatan' => $catatan,
        ];

        if (Schema::hasColumn('kenaikan_kelas', 'id_kenaikan')) {
            $payload['id_kenaikan'] = $this->nextPrefixedId(
                KenaikanKelas::pluck('id_kenaikan')->all(),
                'KK',
                6
            );
        }

        KenaikanKelas::create($payload);
    }

    private function archiveAndActivateYears(TahunAjaran $currentYear, TahunAjaran $targetYear): void
    {
        TahunAjaran::where('id_tahun', $currentYear->id_tahun)
            ->update([
                'status' => 'tidak',
                'is_arsip' => true,
            ]);

        TahunAjaran::where('id_tahun', $targetYear->id_tahun)
            ->update([
                'status' => 'aktif',
                'is_arsip' => false,
            ]);
    }

    private function activateTargetSemester(
        Semester $currentSemester,
        Semester $targetSemester,
        TahunAjaran $targetYear
    ): void {
        if (Semester::hasStatusColumn()) {
            Semester::query()->update(['status' => 'tidak']);

            Semester::where('id_semester', $targetSemester->id_semester)
                ->update(['status' => 'aktif']);
        }

        if (Semester::hasIdTahunColumn()) {
            Semester::where('id_semester', $targetSemester->id_semester)
                ->update(['id_tahun' => $targetYear->id_tahun]);

            if ($currentSemester->id_semester !== $targetSemester->id_semester) {
                Semester::where('id_semester', $currentSemester->id_semester)
                    ->where('id_tahun', $targetYear->id_tahun)
                    ->update(['id_tahun' => $targetYear->id_tahun]);
            }
        }
    }

    private function targetClassName(string $namaKelas): ?string
    {
        $parts = preg_split('/\s+/', trim($namaKelas), 2);
        $grade = $parts[0] ?? '';
        $suffix = $parts[1] ?? '';

        if ($grade === 'X') {
            $suffix = preg_replace('/^E/', 'F', $suffix);

            return trim('XI ' . $suffix);
        }

        if ($grade === 'XI') {
            return trim('XII ' . $suffix);
        }

        return null;
    }

    private function gradeFromClassName(string $namaKelas): string
    {
        return preg_split('/\s+/', trim($namaKelas), 2)[0] ?? '';
    }

    private function nextSiswaKelasId(): string
    {
        return $this->nextPrefixedId(SiswaKelas::pluck('id_siswa_kelas')->all(), 'SK', 8);
    }

    private function nextRiwayatKelasId(): string
    {
        return $this->nextPrefixedId(RiwayatKelas::pluck('id_riwayat_kelas')->all(), 'R', 6);
    }

    private function nextPrefixedId(array $ids, string $prefix, int $padding): string
    {
        $max = collect($ids)
            ->map(fn ($id) => (int) preg_replace('/\D+/', '', (string) $id))
            ->max() ?? 0;

        return $prefix . str_pad((string) ($max + 1), $padding, '0', STR_PAD_LEFT);
    }
}
