<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilSekolah extends Model
{
    protected $table = 'profil_sekolah';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_sekolah',
        'alamat',
        'telepon',
        'email',
        'website',
        'kepala_sekolah',
        'nip_kepala_sekolah',
        'akreditasi',
        'npsn',
        'logo',
    ];
}
