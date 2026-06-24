<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kenaikan_kelas', function (Blueprint $table) {
            $table->id();
            $table->string('id_siswa', 7);
            $table->string('id_tahun', 4);
            $table->enum('status', ['naik', 'tidak_naik', 'lulus'])->default('naik');
            $table->string('catatan', 200)->nullable();
            $table->timestamps();

            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');
            $table->foreign('id_tahun')->references('id_tahun')->on('tahun_ajaran')->onDelete('cascade');

            $table->unique(['id_siswa', 'id_tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kenaikan_kelas');
    }
};
