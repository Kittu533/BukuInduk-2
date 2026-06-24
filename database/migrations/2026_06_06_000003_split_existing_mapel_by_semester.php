<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('mata_pelajaran', function (Blueprint $table) {
            if (!Schema::hasColumn('mata_pelajaran', 'semester_mapel')) {
                $table->string('semester_mapel', 6)
                    ->nullable()
                    ->after('kategori_mapel');
            }
        });

        $this->dropIdMapelForeignIfExists();

        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->string('id_mapel', 20)->change();
        });

        Schema::table('jadwal_mengajar', function (Blueprint $table) {
            $table->string('id_mapel', 20)->change();
        });

        $mapelLama = DB::table('mata_pelajaran')
            ->whereNull('semester_mapel')
            ->orWhere('semester_mapel', '')
            ->get();

        foreach ($mapelLama as $mapel) {
            $ganjilId = $this->buildSemesterCode($mapel->id_mapel, 'Ganjil');
            $genapId = $this->buildSemesterCode($mapel->id_mapel, 'Genap');

            DB::table('mata_pelajaran')->updateOrInsert(
                ['id_mapel' => $ganjilId],
                [
                    'nama_mapel' => $mapel->nama_mapel,
                    'kategori_mapel' => $mapel->kategori_mapel,
                    'semester_mapel' => 'Ganjil',
                    'created_at' => $mapel->created_at,
                    'updated_at' => now(),
                ]
            );

            DB::table('mata_pelajaran')->updateOrInsert(
                ['id_mapel' => $genapId],
                [
                    'nama_mapel' => $mapel->nama_mapel,
                    'kategori_mapel' => $mapel->kategori_mapel,
                    'semester_mapel' => 'Genap',
                    'created_at' => $mapel->created_at,
                    'updated_at' => now(),
                ]
            );
        }

        $jadwalLama = DB::table('jadwal_mengajar')
            ->join('kelas_aktif', 'jadwal_mengajar.id_kelas_aktif', '=', 'kelas_aktif.id_kelas_aktif')
            ->join('semester', 'kelas_aktif.id_semester', '=', 'semester.id_semester')
            ->select('jadwal_mengajar.id_jadwal', 'jadwal_mengajar.id_mapel', 'semester.nama_semester')
            ->get();

        foreach ($jadwalLama as $jadwal) {
            if ($this->isSemesterCode($jadwal->id_mapel)) {
                DB::table('mata_pelajaran')
                    ->where('id_mapel', $jadwal->id_mapel)
                    ->update([
                        'semester_mapel' => str_ends_with($jadwal->id_mapel, '-GJ') ? 'Ganjil' : 'Genap',
                        'updated_at' => now(),
                    ]);
                continue;
            }

            $mapelBaru = $this->buildSemesterCode($jadwal->id_mapel, $jadwal->nama_semester);

            DB::table('jadwal_mengajar')
                ->where('id_jadwal', $jadwal->id_jadwal)
                ->update(['id_mapel' => $mapelBaru]);
        }

        DB::table('mata_pelajaran')
            ->where(function ($query) {
                $query->whereNull('semester_mapel')
                    ->orWhere('semester_mapel', '');
            })
            ->delete();

        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->string('semester_mapel', 6)->nullable(false)->change();
        });

        Schema::table('jadwal_mengajar', function (Blueprint $table) {
            $table->foreign('id_mapel')
                ->references('id_mapel')
                ->on('mata_pelajaran')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        $this->dropIdMapelForeignIfExists();

        Schema::table('mata_pelajaran', function (Blueprint $table) {
            if (Schema::hasColumn('mata_pelajaran', 'semester_mapel')) {
                $table->dropColumn('semester_mapel');
            }
        });

        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->string('id_mapel', 5)->change();
        });

        Schema::table('jadwal_mengajar', function (Blueprint $table) {
            $table->string('id_mapel', 5)->change();
            $table->foreign('id_mapel')
                ->references('id_mapel')
                ->on('mata_pelajaran')
                ->onDelete('cascade');
        });
    }

    private function buildSemesterCode(string $baseCode, string $semester): string
    {
        if ($this->isSemesterCode($baseCode)) {
            return $baseCode;
        }

        return $baseCode . ($semester === 'Genap' ? '-GP' : '-GJ');
    }

    private function isSemesterCode(string $idMapel): bool
    {
        return str_ends_with($idMapel, '-GJ') || str_ends_with($idMapel, '-GP');
    }

    private function dropIdMapelForeignIfExists(): void
    {
        $schema = DB::getDatabaseName();

        $constraint = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $schema)
            ->where('TABLE_NAME', 'jadwal_mengajar')
            ->where('COLUMN_NAME', 'id_mapel')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        if ($constraint) {
            DB::statement("ALTER TABLE jadwal_mengajar DROP FOREIGN KEY `$constraint`");
        }
    }
};
