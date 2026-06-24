<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_mengajar', function (Blueprint $table) {
            $table->dropForeign(['id_mapel']);
        });

        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->string('id_mapel', 20)->change();
        });

        Schema::table('jadwal_mengajar', function (Blueprint $table) {
            $table->string('id_mapel', 20)->change();
            $table->foreign('id_mapel')
                ->references('id_mapel')
                ->on('mata_pelajaran')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_mengajar', function (Blueprint $table) {
            $table->dropForeign(['id_mapel']);
        });

        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->string('id_mapel', 8)->change();
        });

        Schema::table('jadwal_mengajar', function (Blueprint $table) {
            $table->string('id_mapel', 8)->change();
            $table->foreign('id_mapel')
                ->references('id_mapel')
                ->on('mata_pelajaran')
                ->onDelete('cascade');
        });
    }
};
