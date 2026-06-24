<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('semester', function (Blueprint $table) {
            if (!Schema::hasColumn('semester', 'status')) {
                $table->string('status', 8)
                    ->default('tidak')
                    ->after('nama_semester');
            }
        });

        DB::table('semester')
            ->whereNull('status')
            ->update(['status' => 'tidak']);
    }

    public function down(): void
    {
        Schema::table('semester', function (Blueprint $table) {
            if (Schema::hasColumn('semester', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
