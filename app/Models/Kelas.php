<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_kelas',
        'nama_kelas',
        'fase',
    ];

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function kelasAktif()
    {
        return $this->hasMany(KelasAktif::class, 'id_kelas', 'id_kelas');
    }

    // ===========================
    // HELPER
    // ===========================

    public static function generateId(): string
    {
        $last = static::orderBy('id_kelas', 'desc')->first();

        if ($last) {
            $number = (int) substr($last->id_kelas, 1) + 1;
        } else {
            $number = 1;
        }

        return 'K' . str_pad($number, 2, '0', STR_PAD_LEFT);
    }
}
