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
        Schema::create('jadwal_mengajar', function (Blueprint $table) {
            $table->string('id_jadwal', 5)->primary();
            $table->string('id_guru', 4);
            $table->string('id_mapel', 8);
            $table->string('id_kelas_aktif', 4);
            //foreign key
            $table->foreign('id_guru')->references('id_guru')->on('guru')->onDelete('cascade');
            $table->foreign('id_mapel')->references('id_mapel')->on('mata_pelajaran')->onDelete('cascade');
            $table->foreign('id_kelas_aktif')->references('id_kelas_aktif')->on('kelas_aktif')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_mengajar');
    }
};
