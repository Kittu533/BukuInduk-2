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
         Schema::create('nilai_ekskul', function (Blueprint $table) {
            $table->string('id_nilai_ekskul', 5)->primary(); // manual, bukan auto increment

            $table->string('id_siswa', 7);
            $table->string('id_ekskul', 4);

            $table->integer('nilai');

            $table->timestamps();

            $table->foreign('id_siswa')
                ->references('id_siswa')
                ->on('siswa')
                ->onDelete('cascade');

            $table->foreign('id_ekskul')
                ->references('id_ekskul')
                ->on('ekstrakurikuler')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_ekskul');
    }
};
