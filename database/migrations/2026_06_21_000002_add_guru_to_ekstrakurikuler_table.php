<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ekstrakurikuler') || Schema::hasColumn('ekstrakurikuler', 'id_guru')) {
            return;
        }

        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            $table->string('id_guru', 4)
                ->nullable()
                ->after('nama_ekskul');

            $table->index('id_guru', 'fk_ekskul_guru');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('ekstrakurikuler') || ! Schema::hasColumn('ekstrakurikuler', 'id_guru')) {
            return;
        }

        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            $table->dropIndex('fk_ekskul_guru');
            $table->dropColumn('id_guru');
        });
    }
};
