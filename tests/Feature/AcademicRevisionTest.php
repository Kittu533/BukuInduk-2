<?php

namespace Tests\Feature;

use App\Models\JadwalMengajar;
use App\Models\Kelas;
use App\Models\KelasAktif;
use App\Models\MataPelajaran;
use App\Models\NilaiMapel;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\TahunAjaran;
use App\Models\Guru;
use App\Models\RiwayatKelas;
use App\Models\Users;
use App\Services\AcademicYearRolloverService;
use App\Support\AcademicRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AcademicRevisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_auto_drop_out_marks_students_after_five_years(): void
    {
        Carbon::setTestNow('2026-06-06');

        $kelasAktif = $this->makeKelasAktif('KA01', 'TA01', '2025/2026', 'SM01', 'Ganjil');

        $siswa = Siswa::create([
            'id_siswa' => 'S000001',
            'nama_lengkap' => 'Siswa Lama',
            'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
            'tahun_masuk' => '2021-06-05',
            'status_siswa' => Siswa::STATUS_AKTIF,
        ]);

        Siswa::syncAutoDropOut();

        $siswa->refresh();

        $this->assertSame(Siswa::STATUS_DO, $siswa->status_siswa);
        $this->assertSame('2026-06-06', $siswa->tanggal_do?->toDateString());
    }

    public function test_academic_record_is_scoped_to_selected_kelas_aktif(): void
    {
        $mapel = MataPelajaran::create([
            'id_mapel' => 'MAT01-GJ',
            'nama_mapel' => 'Matematika',
            'kategori_mapel' => 'Wajib',
            'semester_mapel' => 'Ganjil',
        ]);

        $kelasAktifGanjil = $this->makeKelasAktif('KA01', 'TA01', '2025/2026', 'SM01', 'Ganjil');
        $kelasAktifGenap = $this->makeKelasAktif('KA02', 'TA01', '2025/2026', 'SM02', 'Genap');

        $siswa = Siswa::create([
            'id_siswa' => 'S000001',
            'nama_lengkap' => 'Siswa Uji',
            'agama' => 'Islam',
            'id_kelas_aktif' => $kelasAktifGenap->id_kelas_aktif,
            'tahun_masuk' => '2024-07-01',
            'status_siswa' => Siswa::STATUS_AKTIF,
        ]);

        SiswaKelas::create([
            'id_siswa_kelas' => 'SK00001',
            'id_siswa' => $siswa->id_siswa,
            'id_kelas_aktif' => $kelasAktifGanjil->id_kelas_aktif,
        ]);

        SiswaKelas::create([
            'id_siswa_kelas' => 'SK00002',
            'id_siswa' => $siswa->id_siswa,
            'id_kelas_aktif' => $kelasAktifGenap->id_kelas_aktif,
        ]);

        JadwalMengajar::create([
            'id_jadwal' => 'J01',
            'id_guru' => 'G001',
            'id_mapel' => $mapel->id_mapel,
            'id_kelas_aktif' => $kelasAktifGanjil->id_kelas_aktif,
        ]);

        $mapelGenap = MataPelajaran::create([
            'id_mapel' => 'MAT01-GP',
            'nama_mapel' => 'Matematika',
            'kategori_mapel' => 'Wajib',
            'semester_mapel' => 'Genap',
        ]);

        JadwalMengajar::create([
            'id_jadwal' => 'J02',
            'id_guru' => 'G001',
            'id_mapel' => $mapelGenap->id_mapel,
            'id_kelas_aktif' => $kelasAktifGenap->id_kelas_aktif,
        ]);

        NilaiMapel::where('id_siswa', $siswa->id_siswa)
            ->where('id_jadwal', 'J01')
            ->first()
            ?->update([
                'tugas1' => 70,
                'tugas2' => 70,
                'tugas3' => 70,
                'tugas4' => 70,
                'tugas5' => 70,
                'uts' => 70,
                'uas' => 70,
                'nilai_akhir' => 70,
            ]);

        NilaiMapel::where('id_siswa', $siswa->id_siswa)
            ->where('id_jadwal', 'J02')
            ->first()
            ?->update([
                'tugas1' => 90,
                'tugas2' => 90,
                'tugas3' => 90,
                'tugas4' => 90,
                'tugas5' => 90,
                'uts' => 90,
                'uas' => 90,
                'nilai_akhir' => 90,
            ]);

        $ganjil = AcademicRecord::nilaiAkademik($siswa->id_siswa, $kelasAktifGanjil->id_kelas_aktif, []);
        $genap = AcademicRecord::nilaiAkademik($siswa->id_siswa, $kelasAktifGenap->id_kelas_aktif, []);

        $this->assertCount(1, $ganjil);
        $this->assertCount(1, $genap);
        $this->assertSame(70, $ganjil->first()->nilai_akhir);
        $this->assertSame(90, $genap->first()->nilai_akhir);
        $this->assertSame('MAT01-GJ', $ganjil->first()->kode_mapel_semester);
        $this->assertSame('MAT01-GP', $genap->first()->kode_mapel_semester);
    }

    public function test_jadwal_creation_creates_empty_draft_scores_for_all_students(): void
    {
        $mapel = MataPelajaran::create([
            'id_mapel' => 'BIO01-GJ',
            'nama_mapel' => 'Biologi',
            'kategori_mapel' => 'Wajib',
            'semester_mapel' => 'Ganjil',
        ]);

        $kelasAktif = $this->makeKelasAktif('KA01', 'TA01', '2025/2026', 'SM01', 'Ganjil');

        $siswa = Siswa::create([
            'id_siswa' => 'S000001',
            'nama_lengkap' => 'Siswa Draft',
            'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
            'tahun_masuk' => '2024-07-01',
            'status_siswa' => Siswa::STATUS_AKTIF,
        ]);

        SiswaKelas::create([
            'id_siswa_kelas' => 'SK00001',
            'id_siswa' => $siswa->id_siswa,
            'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
        ]);

        JadwalMengajar::create([
            'id_jadwal' => 'J01',
            'id_guru' => 'G001',
            'id_mapel' => $mapel->id_mapel,
            'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
        ]);

        $draft = NilaiMapel::where('id_siswa', $siswa->id_siswa)
            ->where('id_jadwal', 'J01')
            ->first();

        $this->assertNotNull($draft);
        $this->assertNull($draft->tugas1);
        $this->assertSame('Belum Lengkap', AcademicRecord::statusKelengkapanSiswa($siswa->id_siswa, $kelasAktif->id_kelas_aktif)['status_lengkap']);
    }

    public function test_admin_cannot_update_scores_for_locked_semester(): void
    {
        Carbon::setTestNow('2026-06-06 10:00:00');

        $mapel = MataPelajaran::create([
            'id_mapel' => 'FIS01-GJ',
            'nama_mapel' => 'Fisika',
            'kategori_mapel' => 'Wajib',
            'semester_mapel' => 'Ganjil',
        ]);

        $kelasAktif = $this->makeKelasAktif('KA01', 'TA01', '2025/2026', 'SM01', 'Ganjil');
        $kelasAktif->semester->update([
            'status' => 'aktif',
            'batas_edit_nilai' => '2026-06-01 23:59:00',
        ]);

        $siswa = Siswa::create([
            'id_siswa' => 'S000001',
            'nama_lengkap' => 'Siswa Locked',
            'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
            'tahun_masuk' => '2024-07-01',
            'status_siswa' => Siswa::STATUS_AKTIF,
        ]);

        SiswaKelas::create([
            'id_siswa_kelas' => 'SK00001',
            'id_siswa' => $siswa->id_siswa,
            'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
        ]);

        JadwalMengajar::create([
            'id_jadwal' => 'J01',
            'id_guru' => 'G001',
            'id_mapel' => $mapel->id_mapel,
            'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
        ]);

        $nilai = NilaiMapel::where('id_siswa', $siswa->id_siswa)
            ->where('id_jadwal', 'J01')
            ->firstOrFail();

        $nilai->update([
            'tugas1' => 70,
            'tugas2' => 70,
            'tugas3' => 70,
            'tugas4' => 70,
            'tugas5' => 70,
            'uts' => 70,
            'uas' => 70,
            'nilai_akhir' => 70,
        ]);

        $this->withSession(['role' => 'admin'])
            ->put('/admin/nilai_akademik/' . $siswa->id_siswa, [
                'kelas_aktif' => $kelasAktif->id_kelas_aktif,
                'id_nilai' => [$nilai->id_nilai],
                'tugas1' => [100],
                'tugas2' => [100],
                'tugas3' => [100],
                'tugas4' => [100],
                'tugas5' => [100],
                'uts' => [100],
                'uas' => [100],
            ])
            ->assertRedirect('/admin/nilai_akademik/detail/' . $siswa->id_siswa . '?kelas_aktif=' . $kelasAktif->id_kelas_aktif)
            ->assertSessionHas('error');

        $nilai->refresh();

        $this->assertSame(70, $nilai->tugas1);
        $this->assertSame(70, $nilai->nilai_akhir);
    }

    public function test_rollover_graduates_xii_promotes_x_and_xi_and_is_idempotent(): void
    {
        Carbon::setTestNow('2026-06-21');

        $currentYear = TahunAjaran::create([
            'id_tahun' => 'TA10',
            'tahun' => '2025/2026',
            'status' => 'aktif',
        ]);

        $targetYear = TahunAjaran::create([
            'id_tahun' => 'TA11',
            'tahun' => '2026/2027',
            'status' => 'tidak',
        ]);

        $semesterAktif = Semester::create([
            'id_semester' => 'SM20',
            'id_tahun' => $currentYear->id_tahun,
            'nama_semester' => 'Genap',
            'status' => 'aktif',
        ]);

        $semesterTujuan = Semester::create([
            'id_semester' => 'SM10',
            'id_tahun' => $targetYear->id_tahun,
            'nama_semester' => 'Ganjil',
            'status' => 'tidak',
        ]);

        $currentX = $this->makeNamedKelasAktif('KA11', $currentYear->id_tahun, $semesterAktif->id_semester, 'K101', 'X E01');
        $currentXi = $this->makeNamedKelasAktif('KA12', $currentYear->id_tahun, $semesterAktif->id_semester, 'K111', 'XI F01');
        $currentXii = $this->makeNamedKelasAktif('KA13', $currentYear->id_tahun, $semesterAktif->id_semester, 'K121', 'XII F01');
        $targetX = $this->makeNamedKelasAktif('KA21', $targetYear->id_tahun, $semesterTujuan->id_semester, 'K101', 'X E01');
        $targetXi = $this->makeNamedKelasAktif('KA22', $targetYear->id_tahun, $semesterTujuan->id_semester, 'K111', 'XI F01');
        $targetXii = $this->makeNamedKelasAktif('KA23', $targetYear->id_tahun, $semesterTujuan->id_semester, 'K121', 'XII F01');

        $siswaX = $this->makeSiswaInKelas('S100001', $currentX);
        $siswaXi = $this->makeSiswaInKelas('S100002', $currentXi);
        $siswaXii = $this->makeSiswaInKelas('S100003', $currentXii, 'Lulus');

        $mapel = MataPelajaran::create([
            'id_mapel' => 'SEJ01-GJ',
            'nama_mapel' => 'Sejarah',
            'kategori_mapel' => 'Wajib',
            'semester_mapel' => 'Ganjil',
        ]);

        JadwalMengajar::create([
            'id_jadwal' => 'JR01',
            'id_guru' => 'G001',
            'id_mapel' => $mapel->id_mapel,
            'id_kelas_aktif' => $currentX->id_kelas_aktif,
        ]);

        NilaiMapel::where('id_siswa', $siswaX->id_siswa)
            ->where('id_jadwal', 'JR01')
            ->firstOrFail()
            ->update(['nilai_akhir' => 88]);

        $service = new AcademicYearRolloverService();

        $summary = $service->process($currentYear, $targetYear, $semesterAktif, $semesterTujuan);
        $service->process($currentYear, $targetYear, $semesterAktif, $semesterTujuan);

        $this->assertSame(1, $summary['graduated']);
        $this->assertSame(2, $summary['promoted']);

        $this->assertDatabaseHas('siswa', [
            'id_siswa' => $siswaXii->id_siswa,
            'status_siswa' => Siswa::STATUS_LULUS,
        ]);
        $this->assertDatabaseHas('alumni', [
            'id_siswa' => $siswaXii->id_siswa,
            'id_tahun_lulus' => $currentYear->id_tahun,
            'status_akhir' => Siswa::STATUS_LULUS,
        ]);
        $this->assertDatabaseMissing('siswa_kelas', [
            'id_siswa' => $siswaXii->id_siswa,
        ]);

        $this->assertDatabaseHas('siswa_kelas', [
            'id_siswa' => $siswaX->id_siswa,
            'id_kelas_aktif' => $targetXi->id_kelas_aktif,
        ]);
        $this->assertDatabaseHas('siswa_kelas', [
            'id_siswa' => $siswaXi->id_siswa,
            'id_kelas_aktif' => $targetXii->id_kelas_aktif,
        ]);
        $this->assertDatabaseMissing('siswa_kelas', [
            'id_kelas_aktif' => $targetX->id_kelas_aktif,
        ]);

        $this->assertSame(1, DB::table('alumni')->where('id_siswa', $siswaXii->id_siswa)->count());
        $this->assertSame(1, RiwayatKelas::where('id_siswa', $siswaX->id_siswa)->where('id_kelas_aktif', $targetXi->id_kelas_aktif)->count());
        $this->assertSame(88, AcademicRecord::nilaiAkademik($siswaX->id_siswa, $currentX->id_kelas_aktif)->first()->nilai_akhir);
        $this->assertDatabaseHas('semester', [
            'id_semester' => $semesterTujuan->id_semester,
            'status' => 'aktif',
        ]);
        $this->assertDatabaseHas('semester', [
            'id_semester' => $semesterAktif->id_semester,
            'status' => 'tidak',
        ]);
    }

    public function test_archived_tahun_ajaran_cannot_be_activated(): void
    {
        TahunAjaran::create([
            'id_tahun' => 'TA01',
            'tahun' => '2025/2026',
            'status' => 'tidak',
            'is_arsip' => true,
        ]);

        TahunAjaran::create([
            'id_tahun' => 'TA02',
            'tahun' => '2026/2027',
            'status' => 'aktif',
            'is_arsip' => false,
        ]);

        $this->withSession(['role' => 'admin'])
            ->post('/admin/tahun-ajaran/aktif/TA01')
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('tahun_ajaran', [
            'id_tahun' => 'TA01',
            'status' => 'tidak',
        ]);
        $this->assertDatabaseHas('tahun_ajaran', [
            'id_tahun' => 'TA02',
            'status' => 'aktif',
        ]);
    }

    public function test_rollover_is_cancelled_when_current_active_semester_has_no_classes(): void
    {
        TahunAjaran::create([
            'id_tahun' => 'TA10',
            'tahun' => '2025/2026',
            'status' => 'aktif',
        ]);

        TahunAjaran::create([
            'id_tahun' => 'TA11',
            'tahun' => '2026/2027',
            'status' => 'tidak',
        ]);

        Semester::create([
            'id_semester' => 'SM01',
            'id_tahun' => 'TA11',
            'nama_semester' => 'Ganjil',
            'status' => 'tidak',
        ]);

        Semester::create([
            'id_semester' => 'SM02',
            'id_tahun' => 'TA10',
            'nama_semester' => 'Genap',
            'status' => 'aktif',
        ]);

        $this->withSession(['role' => 'admin'])
            ->post('/admin/kenaikan/proses-tahun-baru')
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('tahun_ajaran', [
            'id_tahun' => 'TA10',
            'status' => 'aktif',
        ]);
        $this->assertDatabaseHas('tahun_ajaran', [
            'id_tahun' => 'TA11',
            'status' => 'tidak',
        ]);
    }

    public function test_alumni_detail_uses_selected_kelas_aktif_for_historical_scores(): void
    {
        $kelasGanjil = $this->makeKelasAktif('KA01', 'TA01', '2025/2026', 'SM01', 'Ganjil');
        $kelasGenap = $this->makeKelasAktif('KA02', 'TA01', '2025/2026', 'SM02', 'Genap');

        $siswa = Siswa::create([
            'id_siswa' => 'S000010',
            'nama_lengkap' => 'Alumni Nilai',
            'agama' => 'Islam',
            'id_kelas_aktif' => $kelasGenap->id_kelas_aktif,
            'tahun_masuk' => '2023-07-01',
            'status_siswa' => Siswa::STATUS_LULUS,
        ]);

        RiwayatKelas::create(['id_riwayat_kelas' => 'R100001', 'id_siswa' => $siswa->id_siswa, 'id_kelas_aktif' => $kelasGanjil->id_kelas_aktif]);
        RiwayatKelas::create(['id_riwayat_kelas' => 'R100002', 'id_siswa' => $siswa->id_siswa, 'id_kelas_aktif' => $kelasGenap->id_kelas_aktif]);

        $this->makeMapelScore($siswa, $kelasGanjil, 'J101', 'MAT10-GJ', 70);
        $this->makeMapelScore($siswa, $kelasGenap, 'J102', 'MAT10-GP', 90);

        $response = $this->withSession(['role' => 'admin'])
            ->get('/admin/alumni/detail/' . $siswa->id_siswa . '?id_kelas_aktif=' . $kelasGanjil->id_kelas_aktif);

        $response->assertOk();
        $this->assertSame(70, $response->viewData('nilai')->first()->nilai_akhir);
    }

    public function test_orangtua_login_is_rejected_when_linked_student_has_graduated(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true]),
        ]);

        $kelasAktif = $this->makeKelasAktif('KA01', 'TA01', '2025/2026', 'SM01', 'Ganjil');

        $siswa = Siswa::create([
            'id_siswa' => 'S000020',
            'nama_lengkap' => 'Siswa Alumni',
            'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
            'tahun_masuk' => '2023-07-01',
            'status_siswa' => Siswa::STATUS_LULUS,
        ]);

        Users::create([
            'id_users' => 'A001',
            'name' => 'Orang Tua Alumni',
            'username' => 'ortu-alumni',
            'email' => 'ortu@example.test',
            'password' => Hash::make('secret123'),
            'role' => 'orangtua',
            'id_siswa' => $siswa->id_siswa,
        ]);

        $this->post('/login', [
            'username' => 'ortu-alumni',
            'password' => 'secret123',
            'g-recaptcha-response' => 'ok',
        ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertFalse(session()->has('id_siswa'));
    }

    public function test_admin_can_change_user_password_and_blank_password_keeps_existing_hash(): void
    {
        $user = Users::create([
            'id_users' => 'A001',
            'name' => 'Admin Uji',
            'username' => 'admin-uji',
            'email' => 'admin@example.test',
            'password' => Hash::make('old-secret'),
            'role' => 'admin',
        ]);

        $oldHash = $user->password;

        $this->withSession(['role' => 'admin'])
            ->put('/admin/kelola-akun/' . $user->id_users, [
                'name' => 'Admin Uji',
                'username' => 'admin-uji',
                'email' => 'admin@example.test',
                'role' => 'admin',
                'password' => '',
            ])
            ->assertRedirect('admin/kelola-akun');

        $this->assertSame($oldHash, $user->fresh()->password);

        $this->withSession(['role' => 'admin'])
            ->put('/admin/kelola-akun/' . $user->id_users, [
                'name' => 'Admin Uji',
                'username' => 'admin-uji',
                'email' => 'admin@example.test',
                'role' => 'admin',
                'password' => 'new-secret',
            ])
            ->assertRedirect('admin/kelola-akun');

        $this->assertTrue(Hash::check('new-secret', $user->fresh()->password));
    }

    public function test_admin_can_update_siswa_with_non_fixed_length_nisn(): void
    {
        $kelasAktif = $this->makeKelasAktif('KA31', 'TA31', '2025/2026', 'SM31', 'Ganjil');
        $siswa = $this->makeSiswaInKelas('S200001', $kelasAktif);

        $this->withSession(['role' => 'admin'])
            ->put('/admin/siswa/' . $siswa->id_siswa, [
                'nis' => 200001,
                'nisn' => 123456,
                'nama_lengkap' => 'Siswa Update',
                'jenis_kelamin' => 'L',
                'id_kelas' => $kelasAktif->id_kelas,
                'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
                'status_masuk' => 'baru',
                'status_siswa' => Siswa::STATUS_AKTIF,
            ])
            ->assertRedirect('admin/siswa/detail/' . $siswa->id_siswa)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('siswa', [
            'id_siswa' => $siswa->id_siswa,
            'nisn' => 123456,
        ]);
    }

    public function test_admin_can_move_siswa_without_duplicate_riwayat_kelas_id(): void
    {
        $kelasAwal = $this->makeKelasAktif('KA41', 'TA41', '2025/2026', 'SM41', 'Ganjil');
        $kelasTujuan = $this->makeKelasAktif('KA42', 'TA41', '2025/2026', 'SM41', 'Ganjil');

        $siswa = Siswa::create([
            'id_siswa' => 'S410001',
            'nis' => 410001,
            'nama_lengkap' => 'Siswa Pindah',
            'jenis_kelamin' => 'L',
            'id_kelas' => $kelasAwal->id_kelas,
            'id_kelas_aktif' => $kelasAwal->id_kelas_aktif,
            'status_masuk' => 'baru',
            'status_siswa' => Siswa::STATUS_AKTIF,
            'tahun_masuk' => '2025-07-01',
        ]);

        SiswaKelas::create([
            'id_siswa_kelas' => 'SK410001',
            'id_siswa' => $siswa->id_siswa,
            'id_kelas_aktif' => $kelasAwal->id_kelas_aktif,
        ]);

        RiwayatKelas::create([
            'id_riwayat_kelas' => 'R26P121',
            'id_siswa' => $siswa->id_siswa,
            'id_kelas_aktif' => $kelasAwal->id_kelas_aktif,
        ]);

        $siswaLain = Siswa::create([
            'id_siswa' => 'S410002',
            'nis' => 410002,
            'nama_lengkap' => 'Siswa Lain',
            'jenis_kelamin' => 'P',
            'id_kelas' => $kelasAwal->id_kelas,
            'id_kelas_aktif' => $kelasAwal->id_kelas_aktif,
            'status_masuk' => 'baru',
            'status_siswa' => Siswa::STATUS_AKTIF,
            'tahun_masuk' => '2025-07-01',
        ]);

        RiwayatKelas::create([
            'id_riwayat_kelas' => 'R230001',
            'id_siswa' => $siswaLain->id_siswa,
            'id_kelas_aktif' => $kelasAwal->id_kelas_aktif,
        ]);

        $this->withSession(['role' => 'admin'])
            ->put('/admin/siswa/' . $siswa->id_siswa, [
                'nis' => $siswa->nis,
                'nama_lengkap' => 'Siswa Pindah',
                'jenis_kelamin' => 'L',
                'id_kelas' => $kelasTujuan->id_kelas,
                'id_kelas_aktif' => $kelasTujuan->id_kelas_aktif,
                'status_masuk' => 'baru',
                'status_siswa' => Siswa::STATUS_AKTIF,
            ])
            ->assertRedirect('admin/siswa/detail/' . $siswa->id_siswa)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('riwayat_kelas', [
            'id_siswa' => $siswa->id_siswa,
            'id_kelas_aktif' => $kelasTujuan->id_kelas_aktif,
            'id_riwayat_kelas' => 'R230002',
        ]);
    }

    public function test_kelola_akun_create_exposes_class_keys_that_match_student_filter_data(): void
    {
        TahunAjaran::create([
            'id_tahun' => 'TA50',
            'tahun' => '2025/2026',
            'status' => 'aktif',
        ]);

        Semester::create([
            'id_semester' => 'SM50',
            'id_tahun' => 'TA50',
            'nama_semester' => 'Ganjil',
            'status' => 'aktif',
        ]);

        Kelas::create([
            'id_kelas' => 'K5001',
            'nama_kelas' => 'X F01',
            'fase' => 'F',
        ]);

        Guru::create([
            'id_guru' => 'G500',
            'nama_guru' => 'Guru Filter',
        ]);

        KelasAktif::create([
            'id_kelas_aktif' => 'KA101',
            'id_kelas' => 'K5001',
            'id_tahun' => 'TA50',
            'id_semester' => 'SM50',
            'id_guru' => 'G500',
        ]);

        KelasAktif::create([
            'id_kelas_aktif' => 'KA331',
            'id_kelas' => 'K5001',
            'id_tahun' => 'TA50',
            'id_semester' => 'SM50',
            'id_guru' => 'G500',
        ]);

        Siswa::create([
            'id_siswa' => 'S500001',
            'nis' => 500001,
            'nama_lengkap' => 'Siswa Filter',
            'jenis_kelamin' => 'L',
            'id_kelas' => 'K5001',
            'id_kelas_aktif' => 'KA331',
            'status_masuk' => 'baru',
            'status_siswa' => Siswa::STATUS_AKTIF,
            'tahun_masuk' => '2025-07-01',
        ]);

        SiswaKelas::create([
            'id_siswa_kelas' => 'SK50001',
            'id_siswa' => 'S500001',
            'id_kelas_aktif' => 'KA331',
        ]);

        $response = $this->withSession(['role' => 'admin'])
            ->get('/admin/kelola-akun/create');

        $response->assertOk();

        $kelasKeys = collect($response->viewData('kelas'))
            ->map(fn ($kelas) => (string) ($kelas->id_kelas ?? $kelas->id_kelas_aktif))
            ->values();

        $siswaKeys = collect($response->viewData('siswa'))
            ->map(fn ($siswa) => (string) ($siswa->id_kelas ?? $siswa->id_kelas_aktif))
            ->values();

        $this->assertTrue(
            $siswaKeys->every(fn ($key) => $kelasKeys->contains($key)),
            'Ada siswa aktif yang key kelas filternya tidak tersedia di dropdown kelas.'
        );
    }

    private function makeKelasAktif(
        string $idKelasAktif,
        string $idTahun,
        string $tahun,
        string $idSemester,
        string $namaSemester
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
            'nama_kelas' => 'X-' . substr($idKelasAktif, -1),
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

    private function makeNamedKelasAktif(
        string $idKelasAktif,
        string $idTahun,
        string $idSemester,
        string $idKelas,
        string $namaKelas
    ): KelasAktif {
        Kelas::firstOrCreate([
            'id_kelas' => $idKelas,
        ], [
            'nama_kelas' => $namaKelas,
            'fase' => str_starts_with($namaKelas, 'X ') ? 'E' : 'F',
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

    private function makeSiswaInKelas(string $idSiswa, KelasAktif $kelasAktif, string $status = Siswa::STATUS_AKTIF): Siswa
    {
        $siswa = Siswa::create([
            'id_siswa' => $idSiswa,
            'nama_lengkap' => 'Siswa ' . $idSiswa,
            'id_kelas' => $kelasAktif->id_kelas,
            'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
            'tahun_masuk' => '2025-07-01',
            'status_siswa' => $status,
        ]);

        SiswaKelas::create([
            'id_siswa_kelas' => 'SK' . substr($idSiswa, -5),
            'id_siswa' => $idSiswa,
            'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
        ]);

        RiwayatKelas::create([
            'id_riwayat_kelas' => 'R' . substr($idSiswa, -6),
            'id_siswa' => $idSiswa,
            'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
        ]);

        return $siswa;
    }

    private function makeMapelScore(Siswa $siswa, KelasAktif $kelasAktif, string $idJadwal, string $idMapel, int $nilai): void
    {
        MataPelajaran::create([
            'id_mapel' => $idMapel,
            'nama_mapel' => 'Matematika',
            'kategori_mapel' => 'Wajib',
            'semester_mapel' => $kelasAktif->semester->nama_semester,
        ]);

        JadwalMengajar::create([
            'id_jadwal' => $idJadwal,
            'id_guru' => 'G001',
            'id_mapel' => $idMapel,
            'id_kelas_aktif' => $kelasAktif->id_kelas_aktif,
        ]);

        NilaiMapel::firstOrCreate(
            [
                'id_siswa' => $siswa->id_siswa,
                'id_jadwal' => $idJadwal,
            ],
            [
                'id_nilai' => NilaiMapel::generateId(),
            ]
        )->update(['nilai_akhir' => $nilai]);
    }
}
