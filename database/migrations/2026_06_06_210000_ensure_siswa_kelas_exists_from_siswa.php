<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('siswa_kelas')) {
            Schema::create('siswa_kelas', function (Blueprint $table) {
                $table->string('id_siswa_kelas', 10)->primary();
                $table->string('id_siswa', 7);
                $table->string('id_kelas_aktif', 6);
                $table->timestamps();

                $table->foreign('id_siswa')
                    ->references('id_siswa')
                    ->on('siswa')
                    ->onDelete('cascade');

                $table->foreign('id_kelas_aktif')
                    ->references('id_kelas_aktif')
                    ->on('kelas_aktif')
                    ->onDelete('cascade');
            });
        }

        if (! Schema::hasTable('siswa') || ! Schema::hasColumn('siswa', 'id_kelas_aktif')) {
            return;
        }

        $rows = DB::table('siswa')
            ->leftJoin('siswa_kelas', function ($join) {
                $join->on('siswa.id_siswa', '=', 'siswa_kelas.id_siswa')
                    ->on('siswa.id_kelas_aktif', '=', 'siswa_kelas.id_kelas_aktif');
            })
            ->whereNotNull('siswa.id_kelas_aktif')
            ->whereNull('siswa_kelas.id_siswa_kelas')
            ->select(
                'siswa.id_siswa',
                'siswa.id_kelas_aktif',
                'siswa.created_at',
                'siswa.updated_at'
            )
            ->orderBy('siswa.id_siswa')
            ->get();

        if ($rows->isEmpty()) {
            return;
        }

        $lastNumber = DB::table('siswa_kelas')
            ->pluck('id_siswa_kelas')
            ->map(fn ($id) => (int) preg_replace('/\D+/', '', (string) $id))
            ->max() ?? 0;

        $now = now();
        $payload = [];

        foreach ($rows as $row) {
            $lastNumber++;

            $payload[] = [
                'id_siswa_kelas' => 'SK' . str_pad((string) $lastNumber, 8, '0', STR_PAD_LEFT),
                'id_siswa' => $row->id_siswa,
                'id_kelas_aktif' => $row->id_kelas_aktif,
                'created_at' => $row->created_at ?? $now,
                'updated_at' => $row->updated_at ?? $now,
            ];
        }

        DB::table('siswa_kelas')->insert($payload);
    }

    public function down(): void
    {
        // Repair migration: intentionally does not remove restored rows.
    }
};
