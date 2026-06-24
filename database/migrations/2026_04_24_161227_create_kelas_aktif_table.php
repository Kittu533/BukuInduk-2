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
        Schema::create('kelas_aktif', function (Blueprint $table) {
            $table->string('id_kelas_aktif', 4)->primary(); // manual, bukan auto increment

            $table->string('id_kelas', 4);
            $table->string('id_tahun', 4);
            $table->string('id_semester', 4);
            $table->string('id_guru', 4);

            $table->timestamps();

            $table->foreign('id_kelas')
                ->references('id_kelas')
                ->on('kelas')
                ->onDelete('cascade');

            $table->foreign('id_tahun')
                ->references('id_tahun')
                ->on('tahun_ajaran')
                ->onDelete('cascade');

            $table->foreign('id_semester')
                ->references('id_semester')
                ->on('semester')
                ->onDelete('cascade');

            $table->foreign('id_guru')
                ->references('id_guru')
                ->on('guru')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas_aktif');
    }
};
