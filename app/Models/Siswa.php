<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class Siswa extends Model
{
    public const STATUS_AKTIF = 'aktif';
    public const STATUS_DO = 'do';
    public const STATUS_LULUS = 'lulus';

    protected static ?bool $hasStatusSiswaColumn = null;
    protected static ?bool $hasIdKelasAktifColumn = null;

    protected $table = 'siswa';
    protected $primaryKey = 'id_siswa';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_siswa',
        'nis',
        'nisn',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'anak_ke',
        'status_keluarga',
        'alamat',
        'no_telp',
        'id_kelas',
        'id_kelas_aktif',
        'tahun_masuk',
        'status_masuk',
        'status_siswa',
        'tanggal_do',
        'asal_sekolah',
        'nama_ayah',
        'nama_ibu',
        'alamat_ortu',
        'no_telp_ortu',
        'pekerjaan_ayah',
        'pekerjaan_ibu',
        'nama_wali',
        'alamat_wali',
        'no_telp_wali',
        'pekerjaan_wali',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tahun_masuk' => 'date',
        'tanggal_do' => 'date',
    ];

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function siswaKelas()
    {
        return $this->hasMany(SiswaKelas::class, 'id_siswa', 'id_siswa');
    }

    public function nilaiMapel()
    {
        return $this->hasMany(NilaiMapel::class, 'id_siswa', 'id_siswa');
    }

    public function nilaiEkskul()
    {
        return $this->hasMany(NilaiEkskul::class, 'id_siswa', 'id_siswa');
    }

    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'id_siswa', 'id_siswa');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function scopeAktifAkademik($query)
    {
        if (!static::hasStatusSiswaColumn()) {
            return $query;
        }

        return $query->where('status_siswa', self::STATUS_AKTIF);
    }

    // ===========================
    // HELPER — Filter Mapel Agama
    // ===========================

    public function getMapelExcluded(): array
    {
        return match ($this->agama) {
            'Islam' => ['Pendidikan Agama Kristen', 'Pendidikan Agama Katolik'],
            'Kristen' => ['Pendidikan Agama dan Budi Pekerti', 'Pendidikan Agama Katolik'],
            'Katolik' => ['Pendidikan Agama dan Budi Pekerti', 'Pendidikan Agama Kristen'],
            default => [],
        };
    }

    public function shouldAutoDropOut(?Carbon $reference = null): bool
    {
        if (!$this->tahun_masuk) {
            return false;
        }

        if (static::hasStatusSiswaColumn() && $this->status_siswa !== self::STATUS_AKTIF) {
            return false;
        }

        $reference ??= now();

        return $this->tahun_masuk->copy()->addYears(5)->startOfDay()->lte($reference->copy()->startOfDay());
    }

    public function markAsDropOut(?Carbon $reference = null): bool
    {
        if (!static::hasStatusSiswaColumn()) {
            return false;
        }

        if (!$this->shouldAutoDropOut($reference)) {
            return false;
        }

        $reference ??= now();

        $this->forceFill([
            'status_siswa' => self::STATUS_DO,
            'tanggal_do' => $reference->toDateString(),
        ])->save();

        return true;
    }

    public static function syncAutoDropOut(?Carbon $reference = null): int
    {
        if (!static::hasStatusSiswaColumn()) {
            return 0;
        }

        $reference ??= now();

        $updated = 0;

        static::query()
            ->where('status_siswa', self::STATUS_AKTIF)
            ->whereNotNull('tahun_masuk')
            ->get()
            ->each(function (Siswa $siswa) use (&$updated, $reference) {
                if ($siswa->markAsDropOut($reference)) {
                    $updated++;
                }
            });

        return $updated;
    }

    public static function hasStatusSiswaColumn(): bool
    {
        if (static::$hasStatusSiswaColumn !== null) {
            return static::$hasStatusSiswaColumn;
        }

        try {
            static::$hasStatusSiswaColumn = Schema::hasColumn('siswa', 'status_siswa');
        } catch (\Throwable) {
            static::$hasStatusSiswaColumn = false;
        }

        return static::$hasStatusSiswaColumn;
    }

    public static function hasIdKelasAktifColumn(): bool
    {
        if (static::$hasIdKelasAktifColumn !== null) {
            return static::$hasIdKelasAktifColumn;
        }

        try {
            static::$hasIdKelasAktifColumn = Schema::hasColumn('siswa', 'id_kelas_aktif');
        } catch (\Throwable) {
            static::$hasIdKelasAktifColumn = false;
        }

        return static::$hasIdKelasAktifColumn;
    }
}
