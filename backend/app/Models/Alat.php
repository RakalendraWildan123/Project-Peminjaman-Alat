<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alat extends Model
{
    protected $table = 'alat';

    protected $fillable = [
        'kategori_id',
        'nama_alat',
        'stok',
        'status_kondisi',
        'deskripsi',
        'gambar',
    ];

    protected function casts(): array
    {
        return [
            'stok' => 'integer',
        ];
    }

    public function getGambarUrlAttribute(): ?string
    {
        if (!$this->gambar) {
            return null;
        }

        if (str_starts_with($this->gambar, 'http://') || str_starts_with($this->gambar, 'https://')) {
            return $this->gambar;
        }

        $path = ltrim($this->gambar, '/');
        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        if (str_starts_with($path, 'alat/')) {
            return asset('storage/' . $path);
        }

        return asset('storage/alat/' . $path);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function detailPinjam(): HasMany
    {
        return $this->hasMany(DetailPinjam::class);
    }

    public function detailPinjams(): HasMany
    {
        return $this->detailPinjam();
    }
    public function scopeTersedia($query)
    {
        return $query->where('stok', '>', 0)->where('status_kondisi', 'Baik');
    }
}