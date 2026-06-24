<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            $table->string('tahun', 9)->change();
        });

        Schema::table('siswa', function (Blueprint $table) {
            if (!Schema::hasColumn('siswa', 'status_siswa')) {
                $table->string('status_siswa', 10)
                    ->default('aktif')
                    ->after('tahun_masuk');
            }

            if (!Schema::hasColumn('siswa', 'tanggal_do')) {
                $table->date('tanggal_do')
                    ->nullable()
                    ->after('status_siswa');
            }
        });

        DB::table('siswa')
            ->whereNull('status_siswa')
            ->update(['status_siswa' => 'aktif']);
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            if (Schema::hasColumn('siswa', 'tanggal_do')) {
                $table->dropColumn('tanggal_do');
            }

            if (Schema::hasColumn('siswa', 'status_siswa')) {
                $table->dropColumn('status_siswa');
            }
        });

        Schema::table('tahun_ajaran', function (Blueprint $table) {
            $table->string('tahun', 4)->change();
        });
    }
};
