<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiEkskul extends Model
{
    protected $table = 'nilai_ekskul';
    protected $primaryKey = 'id_nilai_ekskul';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_nilai_ekskul',
        'id_siswa',
        'id_kelas_aktif',
        'id_ekskul',
        'nilai',
    ];

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function ekstrakurikuler()
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'id_ekskul', 'id_ekskul');
    }

     public function kelasAktif()
    {
        return $this->belongsTo(KelasAktif::class,'id_kelas_aktif','id_kelas_aktif');
    }

    // ===========================
    // HELPER
    // ===========================

    public static function generateId(): string
    {
        $last = static::orderBy('id_nilai_ekskul', 'desc')->first();

        if ($last) {
            $number = (int) substr($last->id_nilai_ekskul, 2) + 1;
        } else {
            $number = 1;
        }

        return 'NE' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
