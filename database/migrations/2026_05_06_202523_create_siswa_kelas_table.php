<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('siswa_kelas', function (Blueprint $table) {

            $table->string('id_siswa_kelas', 7)->primary();

            $table->string('id_siswa', 7);

            $table->string('id_kelas_aktif', 4);

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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_kelas');
    }
};
