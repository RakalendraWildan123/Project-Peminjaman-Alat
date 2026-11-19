<?php

namespace Tests\Feature\Api;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected function createAdmin(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'Admin Testing',
            'email' => 'admin_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Admin No. 1',
        ], $attributes));
    }

    protected function createPetugas(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'Petugas Testing',
            'email' => 'petugas_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'petugas',
            'no_hp' => '082345678901',
            'alamat' => 'Jl. Petugas No. 2',
        ], $attributes));
    }

    protected function createPeminjam(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'Peminjam Testing',
            'email' => 'peminjam_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'peminjam',
            'no_hp' => '083456789012',
            'alamat' => 'Jl. Peminjam No. 3',
        ], $attributes));
    }

    protected function actingAsAdmin(array $attributes = []): User
    {
        $admin = $this->createAdmin($attributes);
        Sanctum::actingAs($admin, ['*']);
        return $admin;
    }

    protected function actingAsPetugas(array $attributes = []): User
    {
        $petugas = $this->createPetugas($attributes);
        Sanctum::actingAs($petugas, ['*']);
        return $petugas;
    }

    protected function actingAsPeminjam(array $attributes = []): User
    {
        $peminjam = $this->createPeminjam($attributes);
        Sanctum::actingAs($peminjam, ['*']);
        return $peminjam;
    }

    protected function createKategori(array $attributes = []): Kategori
    {
        return Kategori::create(array_merge([
            'nama_kategori' => 'Kategori ' . uniqid(),
        ], $attributes));
    }

    protected function createAlat(array $attributes = []): Alat
    {
        $kategoriId = $attributes['kategori_id'] ?? $this->createKategori()->id;

        return Alat::create(array_merge([
            'kategori_id' => $kategoriId,
            'nama_alat' => 'Alat Test ' . uniqid(),
            'stok' => 10,
            'status_kondisi' => 'Baik',
            'deskripsi' => 'Deskripsi alat uji coba',
            'gambar' => null,
        ], $attributes));
    }

    protected function createPeminjaman(array $attributes = [], array $items = []): Peminjaman
    {
        $userId = $attributes['user_id'] ?? $this->createPeminjam()->id;

        $peminjaman = Peminjaman::create(array_merge([
            'user_id' => $userId,
            'tgl_pinjam' => now()->toDateString(),
            'tgl_kembali_plan' => now()->addDays(3)->toDateString(),
            'status' => 'diajukan',
        ], $attributes));

        if (empty($items)) {
            $alat = $this->createAlat();
            DetailPinjam::create([
                'peminjaman_id' => $peminjaman->id,
                'alat_id' => $alat->id,
                'jumlah' => 1,
            ]);
        } else {
            foreach ($items as $item) {
                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $item['alat_id'],
                    'jumlah' => $item['jumlah'] ?? 1,
                ]);
            }
        }

        return $peminjaman->fresh(['detailPinjam.alat', 'user']);
    }

    protected function createPengembalian(array $attributes = []): Pengembalian
    {
        $peminjamanId = $attributes['peminjaman_id'] ?? null;
        if (!$peminjamanId) {
            $peminjaman = $this->createPeminjaman(['status' => 'dipinjam']);
            $peminjamanId = $peminjaman->id;
        }

        $petugasId = $attributes['petugas_id'] ?? $this->createPetugas()->id;

        return Pengembalian::create(array_merge([
            'peminjaman_id' => $peminjamanId,
            'tgl_kembali' => now()->toDateString(),
            'kondisi_kembali' => 'Lengkap dan Baik',
            'denda' => 0,
            'petugas_id' => $petugasId,
        ], $attributes));
    }
}
