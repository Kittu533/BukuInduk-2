<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\JadwalMengajar;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\KelasAktif;
use App\Models\MataPelajaran;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrtuAbsensiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_orangtua_absensi_shows_all_statuses_for_latest_active_class_only(): void
    {
        $kelasLama = $this->makeKelasAktif('KA01', 'TA01', '2025/2026', 'SM01', 'Ganjil', 'X IPA 1');
        $kelasAktif = $this->makeKelasAktif('KA02', 'TA01', '2025/2026', 'SM02', 'Genap', 'X IPA 2');

        $siswa = Siswa::create([
            'id_siswa' => 'S000001',
            'nama_lengkap' => 'Budi',
            'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
            'tahun_masuk' => '2025-07-01',
            'status_siswa' => Siswa::STATUS_AKTIF,
        ]);

        SiswaKelas::create([
            'id_siswa_kelas' => 'SK00001',
            'id_siswa' => $siswa->id_siswa,
            'id_kelas_aktif' => $kelasLama->id_kelas_aktif,
        ]);

        SiswaKelas::create([
            'id_siswa_kelas' => 'SK00002',
            'id_siswa' => $siswa->id_siswa,
            'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
        ]);

        $jadwalAktif = $this->makeJadwal('J01', 'MP01', 'Matematika', $kelasAktif->id_kelas_aktif);
        $this->makeJadwal('J02', 'MP02', 'Biologi Lama', $kelasLama->id_kelas_aktif);

        Kehadiran::create([
            'id_kehadiran' => 'KH0001',
            'id_siswa' => $siswa->id_siswa,
            'id_jadwal' => $jadwalAktif->id_jadwal,
            'tanggal' => '2026-06-20',
            'status' => 'hadir',
        ]);

        Kehadiran::create([
            'id_kehadiran' => 'KH0002',
            'id_siswa' => $siswa->id_siswa,
            'id_jadwal' => $jadwalAktif->id_jadwal,
            'tanggal' => '2026-06-21',
            'status' => 'Sakit',
        ]);

        Kehadiran::create([
            'id_kehadiran' => 'KH0003',
            'id_siswa' => $siswa->id_siswa,
            'id_jadwal' => 'J02',
            'tanggal' => '2026-05-10',
            'status' => 'izin',
        ]);

        $response = $this->withSession([
            'role' => 'orangtua',
            'id_siswa' => $siswa->id_siswa,
        ])->get('/orangtua/absensi');

        $response->assertOk();
        $response->assertSee('Matematika');
        $response->assertSee('Hadir');
        $response->assertSee('Sakit');
        $response->assertDontSee('Biologi Lama');
        $response->assertDontSee('Izin');
    }

    private function makeKelasAktif(
        string $idKelasAktif,
        string $idTahun,
        string $tahun,
        string $idSemester,
        string $namaSemester,
        string $namaKelas
    ): KelasAktif {
        TahunAjaran::firstOrCreate([
            'id_tahun' => $idTahun,
        ], [
            'tahun' => $tahun,
            'status' => 'aktif',
        ]);

        Semester::firstOrCreate([
            'id_semester' => $idSemester,
        ], [
            'id_tahun' => $idTahun,
            'nama_semester' => $namaSemester,
            'status' => 'aktif',
        ]);

        $idKelas = 'K' . substr($idKelasAktif, 2);

        Kelas::firstOrCreate([
            'id_kelas' => $idKelas,
        ], [
            'nama_kelas' => $namaKelas,
        ]);

        Guru::firstOrCreate([
            'id_guru' => 'G001',
        ], [
            'nama_guru' => 'Guru Uji',
        ]);

        return KelasAktif::create([
            'id_kelas_aktif' => $idKelasAktif,
            'id_kelas' => $idKelas,
            'id_tahun' => $idTahun,
            'id_semester' => $idSemester,
            'id_guru' => 'G001',
        ]);
    }

    private function makeJadwal(
        string $idJadwal,
        string $idMapel,
        string $namaMapel,
        string $idKelasAktif
    ): JadwalMengajar {
        MataPelajaran::create([
            'id_mapel' => $idMapel,
            'nama_mapel' => $namaMapel,
            'kategori_mapel' => 'Wajib',
            'semester_mapel' => 'Genap',
        ]);

        return JadwalMengajar::create([
            'id_jadwal' => $idJadwal,
            'id_guru' => 'G001',
            'id_mapel' => $idMapel,
            'id_kelas_aktif' => $idKelasAktif,
        ]);
    }
}
