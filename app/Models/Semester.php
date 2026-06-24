<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Semester extends Model
{
    protected static ?bool $hasStatusColumn = null;
    protected static ?bool $hasIdTahunColumn = null;
    protected static ?bool $hasBatasEditNilaiColumn = null;

    protected $table = 'semester';
    protected $primaryKey = 'id_semester';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_semester',
        'id_tahun',
        'nama_semester',
        'status',
        'batas_edit_nilai',
    ];

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun', 'id_tahun');
    }

    public function kelasAktif()
    {
        return $this->hasMany(KelasAktif::class, 'id_semester', 'id_semester');
    }

    // ===========================
    // SCOPES
    // ===========================

    public function scopeAktif($query)
    {
        if (! static::hasStatusColumn()) {
            $namaSemesterAktif = static::detectActiveSemesterNameWithoutStatus();

            if ($namaSemesterAktif) {
                return $query
                    ->where('nama_semester', $namaSemesterAktif)
                    ->orderBy('id_semester');
            }

            return $query->orderByDesc('id_semester');
        }

        if (! static::query()->where('status', 'aktif')->exists()) {
            return $query->orderByDesc('id_semester');
        }

        return $query->where('status', 'aktif');
    }

    private static function detectActiveSemesterNameWithoutStatus(): ?string
    {
        $tahunAktif = TahunAjaran::aktif()->first();

        if (! $tahunAktif) {
            return null;
        }

        $namaDariAssignment = DB::table('siswa_kelas')
            ->join('kelas_aktif', 'kelas_aktif.id_kelas_aktif', '=', 'siswa_kelas.id_kelas_aktif')
            ->join('semester', 'semester.id_semester', '=', 'kelas_aktif.id_semester')
            ->where('kelas_aktif.id_tahun', $tahunAktif->id_tahun)
            ->selectRaw('semester.nama_semester, count(*) as total')
            ->groupBy('semester.nama_semester')
            ->orderByDesc('total')
            ->value('semester.nama_semester');

        if ($namaDariAssignment) {
            return $namaDariAssignment;
        }

        $namaDariKelas = DB::table('kelas_aktif')
            ->join('semester', 'semester.id_semester', '=', 'kelas_aktif.id_semester')
            ->where('kelas_aktif.id_tahun', $tahunAktif->id_tahun)
            ->selectRaw('semester.nama_semester, count(*) as total')
            ->groupBy('semester.nama_semester')
            ->orderByDesc('total')
            ->value('semester.nama_semester');

        return $namaDariKelas ?: null;
    }

    public static function hasStatusColumn(): bool
    {
        if (static::$hasStatusColumn !== null) {
            return static::$hasStatusColumn;
        }

        try {
            static::$hasStatusColumn = Schema::hasColumn('semester', 'status');
        } catch (\Throwable) {
            static::$hasStatusColumn = false;
        }

        return static::$hasStatusColumn;
    }

    public static function hasIdTahunColumn(): bool
    {
        if (static::$hasIdTahunColumn !== null) {
            return static::$hasIdTahunColumn;
        }

        try {
            static::$hasIdTahunColumn = Schema::hasColumn('semester', 'id_tahun');
        } catch (\Throwable) {
            static::$hasIdTahunColumn = false;
        }

        return static::$hasIdTahunColumn;
    }

    public static function hasBatasEditNilaiColumn(): bool
    {
        if (static::$hasBatasEditNilaiColumn !== null) {
            return static::$hasBatasEditNilaiColumn;
        }

        try {
            static::$hasBatasEditNilaiColumn = Schema::hasColumn('semester', 'batas_edit_nilai');
        } catch (\Throwable) {
            static::$hasBatasEditNilaiColumn = false;
        }

        return static::$hasBatasEditNilaiColumn;
    }

    // ===========================
    // HELPER
    // ===========================

    public static function generateId(): string
    {
        $last = static::orderBy('id_semester', 'desc')->first();

        if ($last) {
            $number = (int) str_replace('SM', '', $last->id_semester) + 1;
        } else {
            $number = 1;
        }

        return 'SM' . str_pad($number, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Cek apakah masih dalam batas waktu edit nilai
     */
    public function masihBisaEditNilai(): bool
    {
        if ($this->status !== 'aktif') {
            return false;
        }

        if (empty($this->batas_edit_nilai)) {
            return true;
        }

        return now()->lte($this->batas_edit_nilai);
    }
}
