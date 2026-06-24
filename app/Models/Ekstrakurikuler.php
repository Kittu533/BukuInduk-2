<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ekstrakurikuler extends Model
{
    protected $table = 'ekstrakurikuler';
    protected $primaryKey = 'id_ekskul';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_ekskul',
        'nama_ekskul',
        'id_guru',
    ];

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function nilaiEkskul()
    {
        return $this->hasMany(NilaiEkskul::class, 'id_ekskul', 'id_ekskul');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    // ===========================
    // HELPER
    // ===========================

    public static function generateId(): string
    {
        $last = static::orderBy('id_ekskul', 'desc')->first();

        if ($last) {
            preg_match('/(\d+)$/', $last->id_ekskul, $match);
            $number = isset($match[1]) ? (int) $match[1] + 1 : 1;
        } else {
            $number = 1;
        }

        return 'EK' . str_pad($number, 2, '0', STR_PAD_LEFT);
    }
}
