<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\KelolaAkunController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasAktifController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\EkskulController;
use App\Http\Controllers\JadwalMengajarController;
use App\Http\Controllers\KehadiranController;
use App\Http\Controllers\NilaiAkademikController;
use App\Http\Controllers\NilaiEkskulController;
use App\Http\Controllers\ProfilSekolahController;
use App\Http\Controllers\RekapNilaiController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordEmailController;
use App\Http\Controllers\BukuIndukController;
use App\Http\Controllers\KenaikanKelasController;

use App\Http\Controllers\Guru\DashboardController;
use App\Http\Controllers\Guru\GuruJadwalController;
use App\Http\Controllers\Guru\GuruNilaiController;
use App\Http\Controllers\Guru\GuruAbsensiController;
use App\Http\Controllers\Guru\GuruProfilController;

use App\Http\Controllers\WaliKelas\WalasDashboardController;
use App\Http\Controllers\WaliKelas\WalasSiswaController;
use App\Http\Controllers\WaliKelas\WalasKehadiranController;
use App\Http\Controllers\WaliKelas\WalasNilaiController;
use App\Http\Controllers\WaliKelas\WalasEkskulController;
use App\Http\Controllers\WaliKelas\WalasRekapController;
use App\Http\Controllers\WaliKelas\WalasProfileController;

use App\Http\Controllers\PembinaEkskul\PembinaDashboardController;
use App\Http\Controllers\PembinaEkskul\PembinaSiswaController;
use App\Http\Controllers\PembinaEkskul\PembinaNilaiController;
use App\Http\Controllers\PembinaEkskul\PembinaRiwayatController;

use App\Http\Controllers\OrangTua\OrtuDashboardController;
use App\Http\Controllers\OrangTua\OrtuNilaiController;
use App\Http\Controllers\OrangTua\OrtuRekapController;
use App\Http\Controllers\OrangTua\OrtuAbsensiController;
use App\Http\Controllers\OrangTua\OrtuJadwalController;
use App\Http\Controllers\OrangTua\OrtuProfilController;
use App\Http\Controllers\OrangTua\OrtuExportController;


/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('proses.login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Lupa Password
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'send'])->name('password.send');


/*
|--------------------------------------------------------------------------
| ADMIN — Dilindungi middleware 'admin'
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('admin')->group(function () {

    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard']);

    // Kelola Akun
    Route::get('kelola-akun', [KelolaAkunController::class, 'index']);
    Route::get('kelola-akun/create', [KelolaAkunController::class, 'create']);
    Route::post('kelola-akun', [KelolaAkunController::class, 'store']);
    Route::get('kelola-akun/{id}/edit', [KelolaAkunController::class, 'edit']);
    Route::put('kelola-akun/{id}', [KelolaAkunController::class, 'update']);
    Route::delete('kelola-akun/{id}', [KelolaAkunController::class, 'destroy']);

    
    // Data Siswa
    Route::get('/siswa', [SiswaController::class, 'index']);
    Route::get('/siswa/create', [SiswaController::class, 'create']); // <-- TAMBAH INI
    Route::post('/siswa', [SiswaController::class, 'store']);        // <-- TAMBAH INI
    Route::get('/siswa/{id}', [SiswaController::class, 'show']);
    Route::get('/siswa/detail/{id}', [SiswaController::class, 'detail']);
    Route::get('/siswa/{id}/edit', [SiswaController::class, 'edit']);
    Route::put('/siswa/{id}', [SiswaController::class, 'update']);

    // Alumni  
    Route::get('/alumni', [AlumniController::class, 'index']);
    Route::get('/alumni/detail/{id}',[AlumniController::class, 'detail'])->name('admin.alumni.detail');

    // Kelas Aktif
    Route::get('/kelasaktif', [KelasAktifController::class, 'index']);
    Route::get('/kelasaktif/create', [KelasAktifController::class, 'create']);
    Route::post('/kelasaktif', [KelasAktifController::class, 'store']);
    Route::post('/kelasaktif/generate', [KelasAktifController::class, 'generate']);
    Route::get('/kelasaktif/{id}/edit', [KelasAktifController::class, 'edit']);
    Route::put('/kelasaktif/{id}', [KelasAktifController::class, 'update']);
    Route::delete('/kelasaktif/{id}', [KelasAktifController::class, 'destroy']);

    // Tahun Ajaran
    Route::get('/tahun-ajaran', [TahunAjaranController::class, 'index']);
    Route::post('/tahun-ajaran/auto', [TahunAjaranController::class, 'autoTahun']);
    Route::delete('/tahun-ajaran/{id}', [TahunAjaranController::class, 'destroy']);
    Route::post('/tahun-ajaran/aktif/{id}', [TahunAjaranController::class, 'setAktif']);

    // Semester
    Route::get('/semester', [SemesterController::class, 'index']);
    Route::post('/semester/auto', [SemesterController::class, 'autoSemester']);
    Route::post('/semester/proses-baru', [SemesterController::class, 'prosesSemesterBaru']);
    Route::get('/semester/{id}/edit', [SemesterController::class, 'edit']);
    Route::put('/semester/{id}', [SemesterController::class, 'update']);
    Route::delete('/semester/{id}', [SemesterController::class, 'destroy']);
    Route::post('/semester/aktif/{id}', [SemesterController::class, 'setAktif']);

    // Guru
    Route::get('/guru', [GuruController::class, 'index']);
    Route::get('/guru/create', [GuruController::class, 'create']);
    Route::post('/guru', [GuruController::class, 'store']);
    Route::get('/guru/{id}', [GuruController::class, 'detail']);
    Route::get('/guru/{id}/edit', [GuruController::class, 'edit']);
    Route::put('/guru/{id}', [GuruController::class, 'update']);
    Route::delete('/guru/{id}', [GuruController::class, 'destroy']);

    // Mata Pelajaran
    Route::get('/mapel', [MapelController::class, 'index']);
    Route::get('/mapel/create', [MapelController::class, 'create']);
    Route::post('/mapel', [MapelController::class, 'store']);
    Route::get('/mapel/{id}/edit', [MapelController::class, 'edit']);
    Route::put('/mapel/{id}', [MapelController::class, 'update']);
    Route::delete('/mapel/{id}', [MapelController::class, 'destroy']);

    // Ekstrakurikuler
    Route::get('/ekskul', [EkskulController::class, 'index']);
    Route::get('/ekskul/create', [EkskulController::class, 'create']);
    Route::post('/ekskul', [EkskulController::class, 'store']);
    Route::get('/ekskul/{id}/edit', [EkskulController::class, 'edit']);
    Route::put('/ekskul/{id}', [EkskulController::class, 'update']);
    Route::delete('/ekskul/{id}', [EkskulController::class, 'destroy']);

    // Jadwal Mengajar
    Route::get('/jadwal-mengajar', [JadwalMengajarController::class, 'index']);
    Route::get('/jadwal-mengajar/create', [JadwalMengajarController::class, 'create']);
    Route::post('/jadwal-mengajar', [JadwalMengajarController::class, 'store']);
    Route::get('/jadwal-mengajar/{id}', [JadwalMengajarController::class, 'detail']);
    Route::get('/jadwal-mengajar/{id}/edit', [JadwalMengajarController::class, 'edit']);
    Route::put('/jadwal-mengajar/{id}', [JadwalMengajarController::class, 'update']);
    Route::delete('/jadwal-mengajar/{id}', [JadwalMengajarController::class, 'destroy']);

    // Kehadiran — TANPA edit/delete
    Route::get('/kehadiran', [KehadiranController::class, 'index']);
    Route::get('/kehadiran/create/{id}', [KehadiranController::class, 'create']);
    Route::post('/kehadiran/store', [KehadiranController::class, 'store']);
    Route::get('/kehadiran/{id}', [KehadiranController::class, 'detail']);
    Route::get('/kehadiran/detail/{id}', [KehadiranController::class, 'detailSiswa']);

    // Nilai Akademik
    Route::get('/nilai_akademik', [NilaiAkademikController::class, 'index']);
    Route::get('/nilai_akademik/{id}', [NilaiAkademikController::class, 'detail']);
    Route::get('/nilai_akademik/detail/{id}', [NilaiAkademikController::class, 'detailSiswa']);
    Route::get('/nilai_akademik/{id}/edit', [NilaiAkademikController::class, 'edit']);
    Route::put('/nilai_akademik/{id}', [NilaiAkademikController::class, 'update']);

    // Nilai Ekskul
    Route::get('/nilai_ekskul', [NilaiEkskulController::class, 'index']);
    Route::get('/nilai_ekskul/kelas/{id}', [NilaiEkskulController::class, 'detail']);
    Route::get('/nilai_ekskul/detail/{id}', [NilaiEkskulController::class, 'detailSiswa']);
    Route::get('/nilai_ekskul/create/{id}', [NilaiEkskulController::class, 'create']);
    Route::post('/nilai_ekskul/store', [NilaiEkskulController::class, 'store']);
    Route::delete('/nilai_ekskul/delete/{id}', [NilaiEkskulController::class, 'destroy']);
    Route::get('/nilai_ekskul/edit_siswa/{id}', [NilaiEkskulController::class, 'editSiswa']);
    Route::post('/nilai_ekskul/update_siswa/{id}', [NilaiEkskulController::class, 'updateSiswa']);

    // Profil Sekolah (BARU)
    Route::get('/profil-sekolah', [ProfilSekolahController::class, 'index']);
    Route::get('/profil-sekolah/edit', [ProfilSekolahController::class, 'edit']);
    Route::put('/profil-sekolah', [ProfilSekolahController::class, 'update']);
    Route::get('/profil-sekolah/download-cover', [ProfilSekolahController::class, 'downloadCover']);

    // Rekap Nilai / Rapor (BARU)
    Route::get('/rekap', [RekapNilaiController::class, 'index']);
    Route::get('/rekap/{id}', [RekapNilaiController::class, 'detail']);
    Route::get('/rekap/siswa/{id}', [RekapNilaiController::class, 'rekapSiswa']);
    Route::get('/rekap/download/{id}', [RekapNilaiController::class, 'downloadPdf']);

    // Kenaikan/Kelulusan (BARU)
    Route::get('/kenaikan', [KenaikanKelasController::class, 'index']);
    Route::get('/kenaikan/kelas/{id}', [KenaikanKelasController::class,'detail']);
    Route::get('/kenaikan/siswa/{id}', [KenaikanKelasController::class,'show']);
    Route::put('/kenaikan/siswa/{id}', [KenaikanKelasController::class,'update']);
    Route::post('/kenaikan/proses-tahun-baru', [KenaikanKelasController::class, 'prosesTahunBaru']);

    // Export (BARU)
    Route::get('/export/siswa/{id}', [ExportController::class, 'exportSiswa']);
    Route::get('/export/nilai/{id}', [ExportController::class, 'exportNilaiSiswa']);
    Route::get('/export/rekap-hasil-belajar/{id}', [ExportController::class, 'exportRekapHasilBelajar'])->name('export.rekap.hasil.belajar');

    // BUKU INDUK & HASIL BELAJAR (Template resmi)
    Route::get('/export/buku-induk/{id_siswa}', [BukuIndukController::class, 'downloadBukuInduk']);
    Route::get('/export/hasil-belajar/{id_siswa}', [BukuIndukController::class, 'downloadHasilBelajar']);

    // Reset password via email (untuk user yang sudah login)
    Route::post('/profil/reset-email', [ResetPasswordEmailController::class, 'reset']);
});

// Route siswa detail (diluar prefix admin karena URL pattern berbeda)
Route::middleware('admin')->group(function () {
    Route::get('/siswa/{id}', [SiswaController::class, 'show']);
    Route::get('/bukuinduk/{id}', [SiswaController::class, 'detail']);
});


/*
|--------------------------------------------------------------------------
| GURU MAPEL — Dilindungi middleware 'guru'
|--------------------------------------------------------------------------
*/
Route::prefix('guru')->middleware('guru')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/remidi/{id}', [DashboardController::class, 'detailRemidi']);

    // Jadwal
    Route::get('/jadwal', [GuruJadwalController::class, 'index'])->name('guru.jadwal_mengajar.index_jadwal');
    Route::get('/jadwal/{id}', [GuruJadwalController::class, 'show'])->name('guru.jadwal_mengajar.detail_jadwal');

    // Nilai
    Route::get('/nilai', [GuruNilaiController::class, 'index'])->name('guru.nilai.index');
    Route::get('/nilai/{id}', [GuruNilaiController::class, 'siswa'])->name('guru.nilai.siswa');
    Route::get('/nilai/{id}/{id_siswa}/input', [GuruNilaiController::class, 'input'])->name('guru.nilai.input');
    Route::post('/nilai/simpan', [GuruNilaiController::class, 'simpan'])->name('guru.nilai.simpan');
    Route::get('/nilai/{id}/{id_siswa}/detail', [GuruNilaiController::class, 'detailNilai'])->name('guru.nilai.detail');
    Route::get('/nilai/riwayat/{id}/{id_siswa}', [GuruNilaiController::class, 'riwayatNilai'])->name('guru.nilai.riwayat');

    // Absensi — hanya input, TIDAK bisa edit/delete
    Route::get('/absensi', [GuruAbsensiController::class, 'index']);
    Route::get('/absensi/{id}', [GuruAbsensiController::class, 'siswa']);
    Route::get('/absensi/{id}/{id_siswa}/input', [GuruAbsensiController::class, 'input']);
    Route::post('/absensi/simpan', [GuruAbsensiController::class, 'simpan']);
    Route::get('/absensi/{id}/{id_siswa}/detail', [GuruAbsensiController::class, 'detailAbsensi']);

    // Profil + Ganti Password
    Route::get('/profil', [GuruProfilController::class, 'index']);
    Route::get('/profil/password', [GuruProfilController::class, 'formPassword']);
    Route::post('/profil/password', [GuruProfilController::class, 'updatePassword']);
    Route::post('/profil/reset-email', [ResetPasswordEmailController::class, 'reset']);
});


/*
|--------------------------------------------------------------------------
| WALI KELAS — Dilindungi middleware 'wali'
|--------------------------------------------------------------------------
*/
Route::prefix('wali')->middleware('wali')->group(function () {

    // Dashboard
    Route::get('/dashboard', [WalasDashboardController::class, 'index']);

    // Data Siswa
    Route::get('/siswa', [WalasSiswaController::class, 'index']);
    Route::get('/siswa/{id}', [WalasSiswaController::class, 'detail']);

    // Nilai
    Route::get('/nilai', [WalasNilaiController::class, 'index']);
    Route::get('/nilai/{id}', [WalasNilaiController::class, 'detailSiswa']);

    // Kehadiran
    Route::get('/kehadiran', [WalasKehadiranController::class, 'index']);
    Route::get('/kehadiran/{id}', [WalasKehadiranController::class, 'detail']);

    // Ekstrakurikuler
    Route::get('/ekskul', [WalasEkskulController::class, 'index']);
    Route::get('/ekskul/{id}', [WalasEkskulController::class, 'detail']);
    Route::get('/ekskul/create/{id}', [WalasEkskulController::class, 'create']);
    Route::post('/ekskul/store', [WalasEkskulController::class, 'store']);

    // Rekap
    Route::get('/rekap', [WalasRekapController::class, 'index']);
    Route::get('/rekap/{id}', [WalasRekapController::class, 'rekap']);
    Route::get('/rekap/download/{id}', [BukuIndukController::class, 'downloadHasilBelajar']);

    // Profil + Ganti Password
    Route::get('/profil', [WalasProfileController::class, 'index']);
    Route::get('/profil/password', [WalasProfileController::class, 'formPassword']);
    Route::post('/profil/password', [WalasProfileController::class, 'updatePassword']);
    Route::post('/profil/reset-email', [ResetPasswordEmailController::class, 'reset']);
});

/*
|--------------------------------------------------------------------------
| GURU PEMBINA EKSKUL — Dilindungi middleware 'pembina'
|--------------------------------------------------------------------------
*/
Route::prefix('pembina')->middleware('pembina')->group(function () {

    // Data Siswa Ekskul
    Route::get('/siswa', [PembinaSiswaController::class, 'index']);
    Route::get('/siswa/{idEkskul}', [PembinaSiswaController::class, 'detail']);

    // Nilai Ekskul
    Route::get('/nilai', [PembinaNilaiController::class, 'index']);
    Route::get('/nilai/{idEkskul}', [PembinaNilaiController::class, 'kelas']);
    Route::get('/nilai/{idEkskul}/{idKelasAktif}', [PembinaNilaiController::class, 'detail']);
    Route::post('/nilai/update/{id}', [PembinaNilaiController::class, 'updateNilai']);

    Route::get('/riwayat-nilai', [PembinaRiwayatController::class, 'index']);
    Route::get('/riwayat/{idEkskul}', [PembinaRiwayatController::class, 'detail']);

    // Profil
    Route::get('/profil', [PembinaProfilController::class, 'index']);

});


/*
|--------------------------------------------------------------------------
| ORANG TUA — Dilindungi middleware 'orangtua'
|--------------------------------------------------------------------------
*/
Route::prefix('orangtua')->middleware('orangtua')->group(function () {

    Route::get('/dashboard', [OrtuDashboardController::class, 'index']);
    Route::get('/nilai', [OrtuNilaiController::class, 'index']);
    Route::get('/rekap', [OrtuRekapController::class, 'index'])->name('ortu.rekap');
    Route::get('/absensi', [OrtuAbsensiController::class, 'index']);
    Route::get('/jadwal', [OrtuJadwalController::class, 'index']);
    Route::get('/profil', [OrtuProfilController::class, 'index']);

    // Ganti Password (BARU)
    Route::get('/profil/password', [OrtuProfilController::class, 'formPassword']);
    Route::post('/profil/password', [OrtuProfilController::class, 'updatePassword']);
    Route::post('/profil/reset-email', [ResetPasswordEmailController::class, 'reset']);

    // Download Nilai (BARU)
    Route::get('/download-nilai', function () {
    $id_siswa = session('id_siswa');
    if (!$id_siswa) {
        return redirect('/login');
    }
    return app(OrtuExportController::class)
        ->exportNilaiOrtu($id_siswa);
    });

    // Download Hasil Belajar 3 Tahun (Template B)
    Route::get('/download-rapor', function () {
        $id_siswa = session('id_siswa');
        if (!$id_siswa) return redirect('/login');
        return app(BukuIndukController::class)->downloadHasilBelajar($id_siswa);
    });
});
