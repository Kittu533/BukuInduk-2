<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kehadiran extends Model
{
    protected $table = 'kehadiran';
    protected $primaryKey = 'id_kehadiran';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_kehadiran',
        'id_siswa',
        'id_jadwal',
        'tanggal',
        'status',
    ];

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function jadwalMengajar()
    {
        return $this->belongsTo(JadwalMengajar::class, 'id_jadwal', 'id_jadwal');
    }

    // ===========================
    // HELPER
    // ===========================

    public static function generateId($idSiswa): string
    {
        $siswa = Siswa::findOrFail($idSiswa);

        $siswaKelas = SiswaKelas::with('kelasAktif.kelas')
            ->where('id_siswa', $idSiswa)
            ->firstOrFail();

        $namaKelas = $siswaKelas->kelasAktif->kelas->nama_kelas;

        if (str_starts_with($namaKelas, 'XII')) {
            $tingkat = 'XII';
        } elseif (str_starts_with($namaKelas, 'XI')) {
            $tingkat = 'XI';
        } else {
            $tingkat = 'X';
        }

        $tahunMasuk = substr($siswa->id_siswa, 1, 2);
        // S253112 => 25
        // S233001 => 23

        $prefix = 'KH' . $tingkat . $tahunMasuk;

        $last = self::where('id_kehadiran', 'like', $prefix . '%')
            ->orderBy('id_kehadiran', 'desc')
            ->first();

        if ($last) {
            $nomor = (int) substr($last->id_kehadiran, strlen($prefix)) + 1;
        } else {
            $nomor = 1001;
        }

        return $prefix . $nomor;
    }
}
