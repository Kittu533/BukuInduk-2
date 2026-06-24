<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    protected $table = 'alumni';

    protected $fillable = [
        'id_siswa',
        'id_tahun_lulus',
        'tanggal_lulus',
        'status_akhir',
        'catatan',
    ];

    protected $casts = [
        'tanggal_lulus' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function tahunLulus()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_lulus', 'id_tahun');
    }
}
