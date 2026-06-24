<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('semester', function (Blueprint $table) {
            if (!Schema::hasColumn('semester', 'batas_edit_nilai')) {
                $table->date('batas_edit_nilai')->nullable()->after('nama_semester');
            }
        });
    }

    public function down(): void
    {
        Schema::table('semester', function (Blueprint $table) {
            if (Schema::hasColumn('semester', 'batas_edit_nilai')) {
                $table->dropColumn('batas_edit_nilai');
            }
        });
    }
};
