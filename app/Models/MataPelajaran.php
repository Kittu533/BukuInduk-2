<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';
    protected $primaryKey = 'id_mapel';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_mapel',
        'nama_mapel',
        'kategori_mapel',
        'semester_mapel',
    ];

    protected static ?bool $hasSemesterMapelColumn = null;

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function jadwalMengajar()
    {
        return $this->hasMany(JadwalMengajar::class, 'id_mapel', 'id_mapel');
    }

    public function getKodeDasarAttribute(): string
    {
        return preg_replace('/-(GJ|GP)$/', '', $this->id_mapel) ?? $this->id_mapel;
    }

    public function getLabelSemesterAttribute(): string
    {
        return ($this->attributes['semester_mapel'] ?? null)
            ?: (str_ends_with($this->id_mapel, '-GP') ? 'Genap' : 'Ganjil');
    }

    public static function buildSemesterCode(string $baseCode, string $semester): string
    {
        $normalized = strtoupper(trim($baseCode));
        $normalized = preg_replace('/-(GJ|GP)$/', '', $normalized) ?? $normalized;

        return $normalized . ($semester === 'Genap' ? '-GP' : '-GJ');
    }

    public static function hasSemesterMapelColumn(): bool
    {
        if (self::$hasSemesterMapelColumn !== null) {
            return self::$hasSemesterMapelColumn;
        }

        return self::$hasSemesterMapelColumn = Schema::hasColumn('mata_pelajaran', 'semester_mapel');
    }
}
