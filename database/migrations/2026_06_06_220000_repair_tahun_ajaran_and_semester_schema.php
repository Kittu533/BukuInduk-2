<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tahun_ajaran') && ! Schema::hasColumn('tahun_ajaran', 'status')) {
            Schema::table('tahun_ajaran', function (Blueprint $table) {
                $table->string('status', 8)
                    ->default('tidak')
                    ->after('tahun');
            });
        }

        if (Schema::hasTable('semester') && ! Schema::hasColumn('semester', 'id_tahun')) {
            Schema::table('semester', function (Blueprint $table) {
                $table->string('id_tahun', 4)
                    ->nullable()
                    ->after('id_semester');
            });
        }

        if (Schema::hasTable('semester') && ! Schema::hasColumn('semester', 'status')) {
            Schema::table('semester', function (Blueprint $table) {
                $table->string('status', 8)
                    ->default('tidak')
                    ->after('nama_semester');
            });
        }

        $this->backfillTahunAjaranStatus();
        $this->backfillSemesterColumns();
    }

    public function down(): void
    {
        // Repair migration: intentionally non-destructive.
    }

    private function backfillTahunAjaranStatus(): void
    {
        if (! Schema::hasTable('tahun_ajaran') || ! Schema::hasColumn('tahun_ajaran', 'status')) {
            return;
        }

        DB::table('tahun_ajaran')
            ->whereNull('status')
            ->update(['status' => 'tidak']);

        $hasActive = DB::table('tahun_ajaran')
            ->where('status', 'aktif')
            ->exists();

        if ($hasActive) {
            return;
        }

        $latestId = DB::table('tahun_ajaran')
            ->orderByDesc('id_tahun')
            ->value('id_tahun');

        if ($latestId) {
            DB::table('tahun_ajaran')
                ->where('id_tahun', $latestId)
                ->update(['status' => 'aktif']);
        }
    }

    private function backfillSemesterColumns(): void
    {
        if (! Schema::hasTable('semester')) {
            return;
        }

        if (Schema::hasColumn('semester', 'status')) {
            DB::table('semester')
                ->whereNull('status')
                ->update(['status' => 'tidak']);
        }

        if (Schema::hasColumn('semester', 'id_tahun')) {
            $kelasAktifTahun = collect();

            if (Schema::hasTable('kelas_aktif')) {
                $kelasAktifTahun = DB::table('kelas_aktif')
                    ->select('id_semester', DB::raw('MIN(id_tahun) as id_tahun'))
                    ->groupBy('id_semester')
                    ->pluck('id_tahun', 'id_semester');
            }

            $defaultTahun = null;

            if (Schema::hasTable('tahun_ajaran')) {
                $defaultTahun = DB::table('tahun_ajaran')
                    ->when(
                        Schema::hasColumn('tahun_ajaran', 'status'),
                        fn ($query) => $query->orderByRaw("status = 'aktif' desc")
                    )
                    ->orderByDesc('id_tahun')
                    ->value('id_tahun');
            }

            DB::table('semester')
                ->orderBy('id_semester')
                ->get(['id_semester', 'id_tahun'])
                ->each(function ($semester) use ($kelasAktifTahun, $defaultTahun) {
                    if (! empty($semester->id_tahun)) {
                        return;
                    }

                    $idTahun = $kelasAktifTahun[$semester->id_semester] ?? $defaultTahun;

                    if (! $idTahun) {
                        return;
                    }

                    DB::table('semester')
                        ->where('id_semester', $semester->id_semester)
                        ->update(['id_tahun' => $idTahun]);
                });
        }

        if (! Schema::hasColumn('semester', 'status')) {
            return;
        }

        $hasActive = DB::table('semester')
            ->where('status', 'aktif')
            ->exists();

        if ($hasActive) {
            return;
        }

        $latestId = DB::table('semester')
            ->orderByDesc('id_semester')
            ->value('id_semester');

        if ($latestId) {
            DB::table('semester')
                ->where('id_semester', $latestId)
                ->update(['status' => 'aktif']);
        }
    }
};
