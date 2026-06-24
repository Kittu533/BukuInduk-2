<?php

namespace App\Console\Commands;

use App\Models\KelasAktif;
use App\Models\NilaiMapel;
use App\Models\Siswa;
use Illuminate\Console\Command;

class BackfillNilaiDraftCommand extends Command
{
    protected $signature = 'nilai:backfill-drafts {--active-only : Hanya semester aktif}';
    protected $description = 'Membuat draft nilai kosong untuk siswa-mapel yang belum punya record.';

    public function handle(): int
    {
        Siswa::syncAutoDropOut();

        $kelasAktif = KelasAktif::query()
            ->when($this->option('active-only'), function ($query) {
                $query->whereHas('semester', fn ($semester) => $semester->where('status', 'aktif'));
            })
            ->pluck('id_kelas_aktif');

        foreach ($kelasAktif as $idKelasAktif) {
            NilaiMapel::ensureDraftsForKelasAktif($idKelasAktif);
            $this->line("Draft nilai disinkronkan untuk kelas aktif {$idKelasAktif}");
        }

        $this->info('Selesai backfill draft nilai.');

        return self::SUCCESS;
    }
};
