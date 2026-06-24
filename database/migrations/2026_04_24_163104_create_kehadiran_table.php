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
        Schema::create('kehadiran', function (Blueprint $table) {
            $table->string('id_kehadiran', 5)->primary(); // manual, bukan auto increment

            $table->string('id_siswa', 7);
            $table->string('id_jadwal', 5);

            $table->date('tanggal');
            $table->string('status', 10);

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
        Schema::dropIfExists('kehadiran');
    }
};
