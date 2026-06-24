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
        Schema::create('semester', function (Blueprint $table) {
            $table->string('id_semester', 4)->primary(); // manual, bukan auto increment

            $table->string('id_tahun', 4);
            $table->string('nama_semester', 8);

            $table->timestamps();

            $table->foreign('id_tahun')
                ->references('id_tahun')
                ->on('tahun_ajaran')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semester');
    }
};
