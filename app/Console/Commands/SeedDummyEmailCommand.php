<?php

namespace App\Console\Commands;

use App\Models\Users;
use Illuminate\Console\Command;

/**
 * SeedDummyEmailCommand — Isi email dummy untuk user yang masih kosong
 *
 * Usage: php artisan users:seed-email
 *
 * Format email: {username}@bukuinduk.test
 *
 * @legacy Tool untuk testing fitur email (30 Mei 2026)
 */
class SeedDummyEmailCommand extends Command
{
    protected $signature = 'users:seed-email {--domain=bukuinduk.test : Domain untuk email dummy}';
    protected $description = 'Isi email dummy untuk user yang emailnya masih kosong';

    public function handle()
    {
        $domain = $this->option('domain');
        $users = Users::whereNull('email')->orWhere('email', '')->get();

        if ($users->isEmpty()) {
            $this->info('Semua user sudah punya email.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($users as $user) {
            $email = $user->username . '@' . $domain;
            $user->update(['email' => $email]);
            $this->line("✓ {$user->username} → {$email}");
            $count++;
        }

        $this->newLine();
        $this->info("Selesai! {$count} user di-update.");

        return self::SUCCESS;
    }
}
