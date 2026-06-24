<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'guru';
    protected $primaryKey = 'id_guru';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_guru',
        'nama_guru',
        'nip',
        'jenis_kelamin',
        'jabatan',
        'tugas_mengajar',
    ];

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function kelasAktif()
    {
        return $this->hasMany(KelasAktif::class, 'id_guru', 'id_guru');
    }

    public function jadwalMengajar()
    {
        return $this->hasMany(JadwalMengajar::class, 'id_guru', 'id_guru');
    }

    public function user()
    {
        return $this->hasOne(Users::class, 'id_guru', 'id_guru');
    }

    // ===========================
    // HELPER
    // ===========================

    public static function generateId(): string
    {
        $last = static::orderBy('id_guru', 'desc')->first();

        if ($last) {
            $number = (int) substr($last->id_guru, 1) + 1;
        } else {
            $number = 1;
        }

        return 'G' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
