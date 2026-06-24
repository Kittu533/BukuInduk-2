<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class TahunAjaran extends Model
{
    protected static ?bool $hasStatusColumn = null;

    protected $table = 'tahun_ajaran';
    protected $primaryKey = 'id_tahun';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_tahun',
        'tahun',
        'status',
        'is_arsip',
    ];

    protected $casts = [
        'is_arsip' => 'boolean',
    ];

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function semester()
    {
        return $this->hasMany(Semester::class, 'id_tahun', 'id_tahun');
    }

    // ===========================
    // SCOPES
    // ===========================

    public function scopeAktif($query)
    {
        if (! static::hasStatusColumn()) {
            return $query->orderByDesc('id_tahun');
        }

        if (! static::query()->where('status', 'aktif')->exists()) {
            return $query->orderByDesc('id_tahun');
        }

        return $query->where('status', 'aktif');
    }

    public static function hasStatusColumn(): bool
    {
        if (static::$hasStatusColumn !== null) {
            return static::$hasStatusColumn;
        }

        try {
            static::$hasStatusColumn = Schema::hasColumn('tahun_ajaran', 'status');
        } catch (\Throwable) {
            static::$hasStatusColumn = false;
        }

        return static::$hasStatusColumn;
    }

    // ===========================
    // HELPER
    // ===========================

    public static function generateId(): string
    {
        $last = static::orderBy('id_tahun', 'desc')->first();

        if ($last) {
            $number = (int) str_replace('TA', '', $last->id_tahun) + 1;
        } else {
            $number = 1;
        }

        return 'TA' . str_pad($number, 2, '0', STR_PAD_LEFT);
    }
}
