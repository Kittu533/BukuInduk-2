<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Paginator::useBootstrapFive();
        
        View::composer('*', function ($view) {

            $id_guru = session('id_guru');

            $kelasWaliGlobal = null;

            if ($id_guru) {

                $kelasWaliGlobal = DB::table('kelas_aktif')
                    ->join('kelas', 'kelas_aktif.id_kelas', '=', 'kelas.id_kelas')
                    ->where('kelas_aktif.id_guru', $id_guru)
                    ->orderByDesc('kelas_aktif.id_kelas_aktif')
                    ->select('kelas.nama_kelas')
                    ->first();
            }

            $view->with('kelasWaliGlobal', $kelasWaliGlobal);
        });

        // ============================================
        // VIEW COMPOSER UNTUK ORANG TUA — kirim data siswa
        // ============================================
        View::composer(['layouts.sidebar-ortu', 'layouts.navbar-ortu', 'layouts.app-ortu'], function ($view) {

            $id_siswa = session('id_siswa');
            $siswa = null;

            if ($id_siswa) {
                $siswa = \App\Models\Siswa::find($id_siswa);
            }

            $view->with('siswa', $siswa);
        });
    }
}
