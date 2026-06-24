<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KenaikanKelas extends Model
{
    protected $table = 'kenaikan_kelas';

    protected $fillable = [
        'id_kenaikan',
        'id_siswa',
        'id_tahun',
        'status',
        'catatan',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun', 'id_tahun');
    }

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'naik' => 'Naik Kelas',
            'tidak_naik' => 'Tidak Naik',
            'lulus' => 'Lulus',
            default => '-',
        };
    }
}
