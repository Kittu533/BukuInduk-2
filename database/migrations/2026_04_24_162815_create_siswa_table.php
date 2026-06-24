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
        Schema::create('siswa', function (Blueprint $table) {
            $table->string('id_siswa', 7)->primary();

            $table->integer('nis')->unique()->nullable();
            $table->bigInteger('nisn')->unique()->nullable();
            $table->string('nama_lengkap', 200);
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin', 20)->nullable();
            $table->string('agama', 20)->nullable();
            $table->integer('anak_ke')->nullable();
            $table->string('status_keluarga', 25)->nullable();
            $table->string('alamat', 200)->nullable();
            $table->string('no_telp', 20)->nullable();

            $table->string('id_kelas_aktif', 4);

            $table->date('tahun_masuk')->nullable();
            $table->string('asal_sekolah', 50)->nullable();

            $table->string('nama_ayah', 150)->nullable();
            $table->string('nama_ibu', 150)->nullable();
            $table->string('alamat_ortu', 150)->nullable();
            $table->string('no_telp_ortu', 20)->nullable();
            $table->string('pekerjaan_ayah', 50)->nullable();
            $table->string('pekerjaan_ibu', 50)->nullable();

            $table->string('nama_wali', 100)->nullable();
            $table->string('alamat_wali', 150)->nullable();
            $table->string('no_telp_wali', 20)->nullable();
            $table->string('pekerjaan_wali', 50)->nullable();

            $table->timestamps();

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
        Schema::dropIfExists('siswa');
    }
};
