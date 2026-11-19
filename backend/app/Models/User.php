<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'no_hp',
        'alamat',
        'foto_profile',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class);
    }

    public function getFotoAttribute(): ?string
    {
        if (!$this->foto_profile) {
            return null;
        }

        if (str_starts_with($this->foto_profile, 'http://') || str_starts_with($this->foto_profile, 'https://')) {
            return $this->foto_profile;
        }

        $path = ltrim($this->foto_profile, '/');
        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . $path);
    }
}