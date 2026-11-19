<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Illuminate\Database\Seeder;

class PengembalianSeeder extends Seeder
{
    public function run(): void
    {
        $petugas = User::where('role', 'petugas')->first() ?? User::where('role', 'admin')->first();
        $peminjamanList = Peminjaman::orderBy('id')->get();

        if ($peminjamanList->isEmpty() || !$petugas) {
            return;
        }

        $p1 = $peminjamanList[0]->id;
        $p2 = $peminjamanList[1]->id ?? $p1;
        $p3 = $peminjamanList[2]->id ?? $p1;

        $pengembalian = [
            [
                'peminjaman_id' => $p1,
                'tgl_kembali' => '2026-06-04',
                'kondisi_kembali' => 'Lengkap dan Berfungsi Baik',
                'denda' => 0,
                'petugas_id' => $petugas->id, // Arif (Petugas)
            ],
            [
                'peminjaman_id' => $p2,
                'tgl_kembali' => '2026-06-05',
                'kondisi_kembali' => 'Lengkap dan Berfungsi Baik',
                'denda' => 0,
                'petugas_id' => $petugas->id,
            ],
            [
                'peminjaman_id' => $p3,
                'tgl_kembali' => '2026-06-09', // Telat 3 hari dari tgl 6
                'kondisi_kembali' => 'Lengkap, Casing Sedikit Tergores',
                'denda' => 30000, // Asumsi denda per hari 10rb
                'petugas_id' => $petugas->id,
            ],
        ];

        foreach ($pengembalian as $kembali) {
            Pengembalian::firstOrCreate([
                'peminjaman_id' => $kembali['peminjaman_id'],
            ], $kembali);
        }
    }
}