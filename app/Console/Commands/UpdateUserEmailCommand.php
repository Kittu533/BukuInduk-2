<?php

namespace App\Console\Commands;

use App\Models\Users;
use Illuminate\Console\Command;

/**
 * UpdateUserEmailCommand — Update email user tertentu
 *
 * Usage: php artisan users:update-email {username} {email}
 *
 * Untuk test fitur reset password dengan email asli.
 *
 * @legacy Tool untuk testing fitur email (30 Mei 2026)
 */
class UpdateUserEmailCommand extends Command
{
    protected $signature = 'users:update-email {username} {email}';
    protected $description = 'Update email user berdasarkan username';

    public function handle()
    {
        $username = $this->argument('username');
        $email = $this->argument('email');

        $user = Users::where('username', $username)->first();

        if (!$user) {
            $this->error("User dengan username '{$username}' tidak ditemukan.");
            return self::FAILURE;
        }

        $oldEmail = $user->email;
        $user->update(['email' => $email]);

        $this->info("✓ Email {$username} di-update");
        $this->line("  Lama: {$oldEmail}");
        $this->line("  Baru: {$email}");

        return self::SUCCESS;
    }
}
