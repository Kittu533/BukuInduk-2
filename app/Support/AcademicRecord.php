<?php

namespace App\Support;

use App\Models\JadwalMengajar;
use App\Models\Kehadiran;
use App\Models\NilaiEkskul;
use App\Models\NilaiMapel;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\RiwayatKelas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;


class AcademicRecord
{
    public static function semesterSuffix(?string $namaSemester): string
    {
        return $namaSemester === 'Genap' ? 'GP' : 'GJ';
    }

    public static function kodeMapelSemester(?string $kodeMapel, ?string $namaSemester): string
    {
        if (blank($kodeMapel)) {
            return '-';
        }

        if (str_ends_with($kodeMapel, '-GJ') || str_ends_with($kodeMapel, '-GP')) {
            return $kodeMapel;
        }

        return $kodeMapel . '-' . static::semesterSuffix($namaSemester);
    }

    public static function riwayatSiswa(string $idSiswa): Collection
    {
        return RiwayatKelas::with([
            'kelasAktif.kelas',
            'kelasAktif.semester',
            'kelasAktif.tahunAjaran',
        ])
        ->where('id_siswa', $idSiswa)
        ->get()
        ->filter(function ($item) {

            return $item->kelasAktif
                && $item->kelasAktif->kelas
                && $item->kelasAktif->semester
                && $item->kelasAktif->tahunAjaran;
        })
        ->sortByDesc(function ($item) {

            $tahun =
                $item->kelasAktif->tahunAjaran->tahun ?? '';

            $semesterOrder =
                $item->kelasAktif->semester->nama_semester === 'Genap'
                ? '2'
                : '1';

            return $tahun . '-' .
                $semesterOrder . '-' .
                ($item->kelasAktif->kelas->nama_kelas ?? '');
        })
        ->values();
    }

    public static function riwayatTerpilih(
        Siswa $siswa,
        ?string $idKelasAktif = null
    ): ?RiwayatKelas
    {
        $riwayat = static::riwayatSiswa($siswa->id_siswa);

        if ($idKelasAktif) {

            $terpilih = $riwayat->firstWhere(
                'id_kelas_aktif',
                $idKelasAktif
            );

            if ($terpilih) {
                return $terpilih;
            }
        }

        $kelasAktifSekarang = SiswaKelas::where(
            'id_siswa',
            $siswa->id_siswa
        )->first();

        if ($kelasAktifSekarang) {

            $aktif = $riwayat->firstWhere(
                'id_kelas_aktif',
                $kelasAktifSekarang->id_kelas_aktif
            );

            if ($aktif) {
                return $aktif;
            }
        }

        return $riwayat->first();
    }

    public static function nilaiAkademik(string $idSiswa, ?string $idKelasAktif, array $excludedMapel = []): Collection
    {
        if (!$idKelasAktif) {
            return collect();
        }

        return NilaiMapel::with([
            'jadwalMengajar.mapel',
            'jadwalMengajar.kelasAktif.semester',
        ])
            ->where('id_siswa', $idSiswa)
            ->whereHas('jadwalMengajar', fn ($query) => $query->where('id_kelas_aktif', $idKelasAktif))
            ->get()
            ->map(function (NilaiMapel $nilai) {
                $jadwal = $nilai->jadwalMengajar;
                $mapel = $jadwal?->mapel;
                $semester = $jadwal?->kelasAktif?->semester?->nama_semester;

                return (object) [
                    'id_nilai' => $nilai->id_nilai,
                    'id_mapel' => $mapel?->id_mapel,
                    'kode_mapel_semester' => static::kodeMapelSemester($mapel?->id_mapel, $semester),
                    'nama_mapel' => $mapel?->nama_mapel,
                    'kategori_mapel' => $mapel?->kategori_mapel,
                    'tugas1' => $nilai->tugas1,
                    'tugas2' => $nilai->tugas2,
                    'tugas3' => $nilai->tugas3,
                    'tugas4' => $nilai->tugas4,
                    'tugas5' => $nilai->tugas5,
                    'uts' => $nilai->uts,
                    'uas' => $nilai->uas,
                    'nilai_akhir' => $nilai->nilai_akhir,
                ];
            })
            ->filter(fn ($nilai) => $nilai->nama_mapel && !in_array($nilai->nama_mapel, $excludedMapel, true))
            ->sortBy(fn ($nilai) => ($nilai->kategori_mapel ?? '') . '-' . ($nilai->id_mapel ?? ''))
            ->values();
    }

    public static function kelompokNilai(Collection $nilai): array
    {
        return [
            'wajib' => $nilai->filter(fn ($item) => ($item->kategori_mapel ?? null) === 'Wajib')->values(),
            'pilihan' => $nilai->filter(fn ($item) => ($item->kategori_mapel ?? null) !== 'Wajib')->values(),
        ];
    }

    public static function nilaiLengkap(object $nilai): bool
    {
        foreach (['tugas1', 'tugas2', 'tugas3', 'tugas4', 'tugas5', 'uts', 'uas'] as $field) {
            if (is_null($nilai->{$field})) {
                return false;
            }
        }

        return true;
    }

    public static function statusKelengkapanSiswa(string $idSiswa, ?string $idKelasAktif, array $excludedMapel = []): array
    {
        $nilai = static::nilaiAkademik($idSiswa, $idKelasAktif, $excludedMapel);
        $lengkap = $nilai->filter(fn ($item) => static::nilaiLengkap($item))->count();

        return [
            'nilai_masuk' => $lengkap,
            'total_mapel' => $nilai->count(),
            'status_lengkap' => $nilai->isNotEmpty() && $lengkap === $nilai->count() ? 'Lengkap' : 'Belum Lengkap',
        ];
    }

    public static function nilaiEkskul(string $idSiswa, ?string $idKelasAktif): Collection
    {
        $query = NilaiEkskul::with('ekstrakurikuler')
            ->where('id_siswa', $idSiswa);

        static $hasKelasAktifColumn;
        $hasKelasAktifColumn ??= Schema::hasColumn('nilai_ekskul', 'id_kelas_aktif');

        if ($hasKelasAktifColumn && $idKelasAktif) {
            $query->where('id_kelas_aktif', $idKelasAktif);
        }

        return $query->get();
    }

    public static function kehadiran(string $idSiswa, ?string $idKelasAktif): object
    {
        if (!$idKelasAktif) {
            return (object) ['sakit' => 0, 'izin' => 0, 'alpa' => 0];
        }

        $jadwalIds = JadwalMengajar::where('id_kelas_aktif', $idKelasAktif)->pluck('id_jadwal');

        return (object) [
            'sakit' => Kehadiran::where('id_siswa', $idSiswa)->whereIn('id_jadwal', $jadwalIds)->whereIn('status', ['sakit', 'Sakit'])->count(),
            'izin' => Kehadiran::where('id_siswa', $idSiswa)->whereIn('id_jadwal', $jadwalIds)->whereIn('status', ['izin', 'Izin'])->count(),
            'alpa' => Kehadiran::where('id_siswa', $idSiswa)->whereIn('id_jadwal', $jadwalIds)->whereIn('status', ['alpa', 'Alpa'])->count(),
        ];
    }
}
