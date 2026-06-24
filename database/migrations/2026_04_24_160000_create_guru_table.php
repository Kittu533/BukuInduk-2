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
        Schema::create('guru', function (Blueprint $table) {
            $table->string('id_guru', 4)->primary();
            $table->string('nama_guru', 150)->nullable();
            $table->string('nip', 25)->nullable();
            $table->string('jenis_kelamin', 25)->nullable();
            $table->string('jabatan', 100)->nullable();
            $table->string('tugas_mengajar', 100)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru');
    }
};
