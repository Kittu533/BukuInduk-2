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
        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->string('id_mapel', 8)->primary();
            $table->string('nama_mapel', 50);

            $table->enum('kategori_mapel', [
                'Wajib',
                'Pilihan',
                'Muatan Lokal'
            ]);
            $table->string('semester_mapel', 6);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mata_pelajaran');
    }
};
