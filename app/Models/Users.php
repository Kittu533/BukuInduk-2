<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Users extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id_users';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_users',
        'name',
        'username',
        'email',
        'password',
        'role',
        'id_guru',
        'id_siswa',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ===========================
    // RELATIONSHIPS
    // ===========================

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    // ===========================
    // HELPER
    // ===========================

    public static function generateId(?string $role = null): string
    {
        $prefix = match ($role) {
            'guru' => 'G',
            'orangtua' => 'O',
            default => 'A',
        };

        $max = static::query()
            ->where('id_users', 'like', $prefix . '%')
            ->pluck('id_users')
            ->map(fn ($id) => (int) preg_replace('/\D+/', '', (string) $id))
            ->max() ?? 0;

        return $prefix . str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }
}
