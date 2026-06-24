<?php

namespace App\Console\Commands;

use App\Models\Users;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * HashPasswordCommand — Hash semua password plaintext di tabel users
 *
 * Usage: php artisan users:hash-password
 *
 * Mendeteksi password yang belum di-hash (tidak diawali $2y$)
 * lalu meng-hash-nya menggunakan bcrypt.
 *
 * @legacy Tool migrasi password plaintext → bcrypt (30 Mei 2026)
 */
class HashPasswordCommand extends Command
{
    protected $signature = 'users:hash-password';
    protected $description = 'Hash semua password plaintext di tabel users';

    public function handle()
    {
        $users = Users::all();

        $countHashed = 0;
        $countSkipped = 0;

        foreach ($users as $user) {
            // Cek apakah password sudah ter-hash (bcrypt diawali $2y$)
            if (str_starts_with($user->password, '$2y$') || str_starts_with($user->password, '$2a$')) {
                $this->line("⏭  Skip {$user->username} — sudah ter-hash");
                $countSkipped++;
                continue;
            }

            // Hash password lama
            $oldPassword = $user->password;
            $user->password = Hash::make($oldPassword);
            $user->save();

            $this->info("✓ Hashed: {$user->username} (password lama: {$oldPassword})");
            $countHashed++;
        }

        $this->newLine();
        $this->info("Selesai!");
        $this->table(
            ['Status', 'Jumlah'],
            [
                ['Berhasil di-hash', $countHashed],
                ['Sudah ter-hash (skip)', $countSkipped],
                ['Total user', $users->count()],
            ]
        );

        return self::SUCCESS;
    }
}
