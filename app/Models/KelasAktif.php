<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelasAktif extends Model
{
    protected $table = 'kelas_aktif';
    protected $primaryKey = 'id_kelas_aktif';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_kelas_aktif',
        'id_kelas',
        'id_tahun',
        'id_semester',
        'id_guru',
    ];

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun', 'id_tahun');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester', 'id_semester');
    }

    public function siswaKelas()
    {
        return $this->hasMany(SiswaKelas::class, 'id_kelas_aktif', 'id_kelas_aktif');
    }

    public function jadwalMengajar()
    {
        return $this->hasMany(JadwalMengajar::class, 'id_kelas_aktif', 'id_kelas_aktif');
    }

    public function riwayatKelas()
    {
        return $this->hasMany(RiwayatKelas::class, 'id_kelas_aktif','id_kelas_aktif');
    }

    // ===========================
    // HELPER
    // ===========================

    public static function generateId(): string
    {
        $last = static::orderBy('id_kelas_aktif', 'desc')->first();

        if ($last) {
            $number = (int) str_replace('KA', '', $last->id_kelas_aktif) + 1;
        } else {
            $number = 1;
        }

        return 'KA' . str_pad($number, 2, '0', STR_PAD_LEFT);
    }
}
