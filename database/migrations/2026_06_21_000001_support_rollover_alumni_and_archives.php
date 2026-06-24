<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tahun_ajaran') && ! Schema::hasColumn('tahun_ajaran', 'is_arsip')) {
            Schema::table('tahun_ajaran', function (Blueprint $table) {
                $table->boolean('is_arsip')
                    ->default(false)
                    ->after('status');
            });
        }

        if (Schema::hasTable('siswa')) {
            Schema::table('siswa', function (Blueprint $table) {
                if (! Schema::hasColumn('siswa', 'id_kelas')) {
                    $table->string('id_kelas', 6)
                        ->nullable()
                        ->after('no_telp');
                }

                if (! Schema::hasColumn('siswa', 'status_masuk')) {
                    $table->string('status_masuk', 10)
                        ->default('baru')
                        ->after('tahun_masuk');
                }
            });

            if (Schema::hasColumn('siswa', 'status_siswa')) {
                DB::table('siswa')
                    ->where('status_siswa', 'Lulus')
                    ->update(['status_siswa' => 'lulus']);
            }
        }

        if (! Schema::hasTable('riwayat_kelas')) {
            Schema::create('riwayat_kelas', function (Blueprint $table) {
                $table->string('id_riwayat_kelas', 10)->primary();
                $table->string('id_siswa', 7);
                $table->string('id_kelas_aktif', 6);
                $table->timestamps();

                $table->foreign('id_siswa')
                    ->references('id_siswa')
                    ->on('siswa')
                    ->onDelete('cascade');

                $table->foreign('id_kelas_aktif')
                    ->references('id_kelas_aktif')
                    ->on('kelas_aktif')
                    ->onDelete('cascade');
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'email')) {
                    $table->string('email', 100)
                        ->nullable()
                        ->unique()
                        ->after('username');
                }

                if (! Schema::hasColumn('users', 'id_guru')) {
                    $table->string('id_guru', 4)
                        ->nullable()
                        ->after('updated_at');
                }

                if (! Schema::hasColumn('users', 'id_siswa')) {
                    $table->string('id_siswa', 7)
                        ->nullable()
                        ->after('id_guru');
                }
            });
        }

        if (Schema::hasTable('alumni')) {
            Schema::table('alumni', function (Blueprint $table) {
                if (! Schema::hasColumn('alumni', 'id_siswa')) {
                    $table->string('id_siswa', 7)
                        ->unique()
                        ->after('id');
                }

                if (! Schema::hasColumn('alumni', 'id_tahun_lulus')) {
                    $table->string('id_tahun_lulus', 4)
                        ->nullable()
                        ->after('id_siswa');
                }

                if (! Schema::hasColumn('alumni', 'tanggal_lulus')) {
                    $table->date('tanggal_lulus')
                        ->nullable()
                        ->after('id_tahun_lulus');
                }

                if (! Schema::hasColumn('alumni', 'status_akhir')) {
                    $table->string('status_akhir', 10)
                        ->default('lulus')
                        ->after('tanggal_lulus');
                }

                if (! Schema::hasColumn('alumni', 'catatan')) {
                    $table->string('catatan', 200)
                        ->nullable()
                        ->after('status_akhir');
                }
            });
        }

        if (Schema::hasTable('nilai_ekskul') && ! Schema::hasColumn('nilai_ekskul', 'id_kelas_aktif')) {
            Schema::table('nilai_ekskul', function (Blueprint $table) {
                $table->string('id_kelas_aktif', 4)
                    ->nullable()
                    ->after('id_siswa');
            });
        }
    }

    public function down(): void
    {
        // Compatibility migration: intentionally non-destructive.
    }
};
