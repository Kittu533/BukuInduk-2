<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalMengajar extends Model
{
    protected $table = 'jadwal_mengajar';
    protected $primaryKey = 'id_jadwal';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_jadwal',
        'id_guru',
        'id_mapel',
        'id_kelas_aktif',
    ];

    protected static function booted(): void
    {
        static::created(function (JadwalMengajar $jadwal) {
            NilaiMapel::ensureDraftsForJadwal($jadwal->id_jadwal);
        });

        static::updated(function (JadwalMengajar $jadwal) {
            NilaiMapel::ensureDraftsForJadwal($jadwal->id_jadwal);
        });
    }

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'id_mapel', 'id_mapel');
    }

    public function kelasAktif()
    {
        return $this->belongsTo(KelasAktif::class, 'id_kelas_aktif', 'id_kelas_aktif');
    }

    public function nilaiMapel()
    {
        return $this->hasMany(NilaiMapel::class, 'id_jadwal', 'id_jadwal');
    }

    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'id_jadwal', 'id_jadwal');
    }
}
