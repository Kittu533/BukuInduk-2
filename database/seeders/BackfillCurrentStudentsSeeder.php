<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BackfillCurrentStudentsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $now = Carbon::now();

            $activeYear = DB::table('tahun_ajaran')
                ->where('status', 'aktif')
                ->first();

            $activeSemester = Semester::aktif()->first();

            if (! $activeYear || ! $activeSemester) {
                throw new RuntimeException('Konteks akademik aktif tidak ditemukan.');
            }

            $targetClasses = DB::table('kelas_aktif')
                ->join('kelas', 'kelas.id_kelas', '=', 'kelas_aktif.id_kelas')
                ->join('semester', 'semester.id_semester', '=', 'kelas_aktif.id_semester')
                ->where('kelas_aktif.id_tahun', $activeYear->id_tahun)
                ->where('semester.nama_semester', $activeSemester->nama_semester)
                ->orderBy('kelas.nama_kelas')
                ->get([
                    'kelas_aktif.id_kelas_aktif',
                    'kelas_aktif.id_kelas',
                    'kelas.nama_kelas',
                ]);

            $kelasX = $targetClasses
                ->filter(fn ($item) => str_starts_with($item->nama_kelas, 'X '))
                ->values();

            $kelasXI = $targetClasses
                ->filter(fn ($item) => str_starts_with($item->nama_kelas, 'XI '))
                ->values();

            if ($kelasX->count() !== 11 || $kelasXI->count() !== 11) {
                throw new RuntimeException('Kelas aktif X/XI pada tahun aktif tidak lengkap.');
            }

            $nextSiswaKelas = $this->nextNumericId('siswa_kelas', 'id_siswa_kelas', 'SA');
            $nextRiwayat = $this->nextNumericId('riwayat_kelas', 'id_riwayat_kelas', 'R');
            $nextNis = ((int) DB::table('siswa')->max('nis')) + 1;
            $nextNisn = ((int) DB::table('siswa')->max('nisn')) + 1;

            $results = [
                $this->fillCohort(
                    newPrefix: 'S26',
                    sourcePrefix: 'S24',
                    targetClasses: $kelasXI,
                    tahunMasuk: '2026-07-01',
                    nameSuffix: 'S26',
                    now: $now,
                    nextSiswaKelas: $nextSiswaKelas,
                    nextRiwayat: $nextRiwayat,
                    nextNis: $nextNis,
                    nextNisn: $nextNisn,
                ),
                $this->fillCohort(
                    newPrefix: 'S27',
                    sourcePrefix: 'S25',
                    targetClasses: $kelasX,
                    tahunMasuk: '2027-07-01',
                    nameSuffix: 'S27',
                    now: $now,
                    nextSiswaKelas: $nextSiswaKelas,
                    nextRiwayat: $nextRiwayat,
                    nextNis: $nextNis,
                    nextNisn: $nextNisn,
                ),
            ];

            $this->command?->info('Backfill siswa aktif selesai.');
            foreach ($results as $result) {
                $status = $result['skipped'] ? 'skip' : 'insert';
                $this->command?->line(
                    sprintf('%s: cohort %s, total %d', $status, $result['cohort'], $result['created'])
                );
            }
        });
    }

    private function fillCohort(
        string $newPrefix,
        string $sourcePrefix,
        Collection $targetClasses,
        string $tahunMasuk,
        string $nameSuffix,
        Carbon $now,
        int &$nextSiswaKelas,
        int &$nextRiwayat,
        int &$nextNis,
        int &$nextNisn,
    ): array {
        $existing = DB::table('siswa')
            ->where('id_siswa', 'like', $newPrefix . '%')
            ->count();

        if ($existing > 0) {
            if ($existing !== 121) {
                throw new RuntimeException("Cohort {$newPrefix} sudah terisi sebagian ({$existing} siswa).");
            }

            return [
                'cohort' => $newPrefix,
                'created' => 0,
                'skipped' => true,
            ];
        }

        $sourceStudents = DB::table('siswa')
            ->where('id_siswa', 'like', $sourcePrefix . '%')
            ->orderBy('id_siswa')
            ->get();

        if ($sourceStudents->count() !== 121) {
            throw new RuntimeException("Sumber cohort {$sourcePrefix} tidak berjumlah 121 siswa.");
        }

        $created = 0;

        foreach ($sourceStudents as $index => $student) {
            $classIndex = intdiv($index, 11);
            $targetClass = $targetClasses->get($classIndex);

            if (! $targetClass) {
                throw new RuntimeException("Distribusi kelas untuk cohort {$newPrefix} gagal pada indeks {$index}.");
            }

            $suffix = substr((string) $student->id_siswa, 3);
            $newId = $newPrefix . $suffix;

            DB::table('siswa')->insert([
                'id_siswa' => $newId,
                'nis' => $nextNis++,
                'nisn' => $nextNisn++,
                'nama_lengkap' => mb_substr($student->nama_lengkap . ' ' . $nameSuffix, 0, 200),
                'tempat_lahir' => $student->tempat_lahir,
                'tanggal_lahir' => $student->tanggal_lahir,
                'jenis_kelamin' => $student->jenis_kelamin,
                'agama' => $student->agama,
                'anak_ke' => $student->anak_ke,
                'status_keluarga' => $student->status_keluarga,
                'alamat' => $student->alamat,
                'no_telp' => $student->no_telp,
                'id_kelas' => $targetClass->id_kelas,
                'tahun_masuk' => $tahunMasuk,
                'status_masuk' => $student->status_masuk ?: 'baru',
                'status_siswa' => 'aktif',
                'tanggal_do' => null,
                'asal_sekolah' => $student->asal_sekolah,
                'nama_ayah' => $student->nama_ayah,
                'nama_ibu' => $student->nama_ibu,
                'alamat_ortu' => $student->alamat_ortu,
                'no_telp_ortu' => $student->no_telp_ortu,
                'pekerjaan_ayah' => $student->pekerjaan_ayah,
                'pekerjaan_ibu' => $student->pekerjaan_ibu,
                'nama_wali' => $student->nama_wali,
                'alamat_wali' => $student->alamat_wali,
                'no_telp_wali' => $student->no_telp_wali,
                'pekerjaan_wali' => $student->pekerjaan_wali,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('siswa_kelas')->insert([
                'id_siswa_kelas' => 'SA' . str_pad((string) $nextSiswaKelas++, 6, '0', STR_PAD_LEFT),
                'id_siswa' => $newId,
                'id_kelas_aktif' => $targetClass->id_kelas_aktif,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('riwayat_kelas')->insert([
                'id_riwayat_kelas' => 'R' . str_pad((string) $nextRiwayat++, 6, '0', STR_PAD_LEFT),
                'id_siswa' => $newId,
                'id_kelas_aktif' => $targetClass->id_kelas_aktif,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $created++;
        }

        return [
            'cohort' => $newPrefix,
            'created' => $created,
            'skipped' => false,
        ];
    }

    private function nextNumericId(string $table, string $column, string $prefix): int
    {
        $last = DB::table($table)
            ->where($column, 'like', $prefix . '%')
            ->orderBy($column, 'desc')
            ->value($column);

        if (! $last) {
            return 1;
        }

        return ((int) preg_replace('/\D+/', '', (string) $last)) + 1;
    }
}
