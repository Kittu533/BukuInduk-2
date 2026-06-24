<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiMapel extends Model
{
    protected $table = 'nilai_mapel';
    protected $primaryKey = 'id_nilai';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_nilai',
        'id_siswa',
        'id_jadwal',
        'tugas1',
        'tugas2',
        'tugas3',
        'tugas4',
        'tugas5',
        'uts',
        'uas',
        'nilai_akhir',
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
    // BUSINESS LOGIC
    // ===========================

    /**
     * Hitung nilai akhir:
     * Rata-rata tugas (40%) + UTS (30%) + UAS (30%)
     */
    public function hitungNilaiAkhir(): int
    {
        $rataTugas = ($this->tugas1 + $this->tugas2 + $this->tugas3 + $this->tugas4 + $this->tugas5) / 5;

        return (int) round(
            ($rataTugas * 0.4) + ($this->uts * 0.3) + ($this->uas * 0.3)
        );
    }

    /**
     * Hitung dan simpan nilai akhir
     */
    public function simpanNilaiAkhir(): void
    {
        $this->nilai_akhir = $this->hitungNilaiAkhir();
        $this->save();
    }

    public static function ensureDraftsForJadwal(string $idJadwal): void
    {
        $jadwal = JadwalMengajar::with('kelasAktif.siswaKelas')->find($idJadwal);

        if (!$jadwal || !$jadwal->kelasAktif) {
            return;
        }

        foreach ($jadwal->kelasAktif->siswaKelas as $siswaKelas) {
            static::firstOrCreate(
                [
                    'id_siswa' => $siswaKelas->id_siswa,
                    'id_jadwal' => $idJadwal,
                ],
                [
                    'id_nilai' => static::generateId(),
                ]
            );
        }
    }

    public static function ensureDraftsForKelasAktif(string $idKelasAktif): void
    {
        $jadwalIds = JadwalMengajar::where('id_kelas_aktif', $idKelasAktif)->pluck('id_jadwal');

        foreach ($jadwalIds as $idJadwal) {
            static::ensureDraftsForJadwal($idJadwal);
        }
    }

    public static function ensureDraftsForSiswaDiKelas(string $idSiswa, string $idKelasAktif): void
    {
        $jadwalIds = JadwalMengajar::where('id_kelas_aktif', $idKelasAktif)->pluck('id_jadwal');

        foreach ($jadwalIds as $idJadwal) {
            static::firstOrCreate(
                [
                    'id_siswa' => $idSiswa,
                    'id_jadwal' => $idJadwal,
                ],
                [
                    'id_nilai' => static::generateId(),
                ]
            );
        }
    }

    // ===========================
    // HELPER
    // ===========================

    public static function generateId(): string
    {
        $last = static::orderBy('id_nilai', 'desc')->first();

        if ($last) {
            $number = (int) substr($last->id_nilai, 2) + 1;
        } else {
            $number = 1;
        }

        return 'NM' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
