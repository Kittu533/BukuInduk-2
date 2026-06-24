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

        if (Schema::hasTable('riwayat_kelas')) {
            DB::statement(<<<'SQL'
                INSERT INTO siswa_kelas (id_siswa_kelas, id_siswa, id_kelas_aktif, created_at, updated_at)
                SELECT id_riwayat_kelas, id_siswa, id_kelas_aktif, created_at, updated_at
                FROM riwayat_kelas
                WHERE id_riwayat_kelas NOT IN (
                    SELECT id_siswa_kelas FROM siswa_kelas
                )
            SQL);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_kelas');
    }
};
