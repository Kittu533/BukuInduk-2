<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaKelas extends Model
{
    protected $table = 'siswa_kelas';
    protected $primaryKey = 'id_siswa_kelas';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_siswa_kelas',
        'id_siswa',
        'id_kelas_aktif',
    ];

    protected static function booted(): void
    {
        static::created(function (SiswaKelas $siswaKelas) {
            NilaiMapel::ensureDraftsForSiswaDiKelas($siswaKelas->id_siswa, $siswaKelas->id_kelas_aktif);
        });

        static::updated(function (SiswaKelas $siswaKelas) {
            NilaiMapel::ensureDraftsForSiswaDiKelas($siswaKelas->id_siswa, $siswaKelas->id_kelas_aktif);
        });
    }

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function kelasAktif()
    {
        return $this->belongsTo(KelasAktif::class, 'id_kelas_aktif', 'id_kelas_aktif');
    }
}
