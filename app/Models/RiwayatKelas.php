<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatKelas extends Model
{
    protected $table = 'riwayat_kelas';
    protected $primaryKey = 'id_riwayat_kelas';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_riwayat_kelas',
        'id_siswa',
        'id_kelas_aktif',
    ];

    protected static function booted(): void
    {
        static::created(function (RiwayatKelas $riwayatKelas) {
            //
        });

        static::updated(function (RiwayatKelas $riwayatKelas) {
            //
        });
    }

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function siswa()
    {
        return $this->belongsTo(
            Siswa::class,
            'id_siswa',
            'id_siswa'
        );
    }

    public function kelasAktif()
    {
        return $this->belongsTo(
            KelasAktif::class,
            'id_kelas_aktif',
            'id_kelas_aktif'
        );
    }

    public static function generateId(): string
    {
        $max = self::query()
            ->pluck('id_riwayat_kelas')
            ->map(fn ($id) => (int) preg_replace('/\D+/', '', (string) $id))
            ->max() ?? 0;

        return 'R' . str_pad(
            (string) ($max + 1),
            6,
            '0',
            STR_PAD_LEFT
        );
    }
}
