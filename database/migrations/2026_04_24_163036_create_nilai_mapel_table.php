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
            Schema::create('nilai_mapel', function (Blueprint $table) {
            $table->string('id_nilai', 5)->primary(); // manual, bukan auto increment

            $table->string('id_siswa', 7);
            $table->string('id_jadwal', 5);

            $table->integer('tugas1')->nullable();
            $table->integer('tugas2')->nullable();
            $table->integer('tugas3')->nullable();
            $table->integer('tugas4')->nullable();
            $table->integer('tugas5')->nullable();
            $table->integer('uts')->nullable();
            $table->integer('uas')->nullable();
            $table->integer('nilai_akhir')->nullable();

            $table->timestamps();

            $table->foreign('id_siswa')
                  ->references('id_siswa')
                  ->on('siswa')
                  ->onDelete('cascade');

            $table->foreign('id_jadwal')
                  ->references('id_jadwal')
                  ->on('jadwal_mengajar')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_mapel');
    }
};
