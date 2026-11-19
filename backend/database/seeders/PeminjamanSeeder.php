<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rian = User::where('email', 'rian@gmail.com')->first();
        $siti = User::where('email', 'siti@gmail.com')->first();
        $eka = User::where('email', 'eka@gmail.com')->first();

        $peminjamList = User::where('role', 'peminjam')->get();
        if ($peminjamList->isEmpty()) {
            return;
        }

        $idRian = $rian ? $rian->id : $peminjamList->first()->id;
        $idSiti = $siti ? $siti->id : $peminjamList->first()->id;
        $idEka = $eka ? $eka->id : $peminjamList->first()->id;

        $peminjaman = [
            [
                'user_id' => $idRian, // Rian (Peminjam)
                'tgl_pinjam' => '2026-06-01',
                'tgl_kembali_plan' => '2026-06-04',
                'status' => 'dikembalikan',
            ],
            [
                'user_id' => $idSiti, // Siti (Peminjam)
                'tgl_pinjam' => '2026-06-02',
                'tgl_kembali_plan' => '2026-06-05',
                'status' => 'dikembalikan',
            ],
            [
                'user_id' => $idEka, // Eka (Peminjam)
                'tgl_pinjam' => '2026-06-03',
                'tgl_kembali_plan' => '2026-06-06',
                'status' => 'telat',
            ],
            [
                'user_id' => $idRian,
                'tgl_pinjam' => '2026-06-08',
                'tgl_kembali_plan' => '2026-06-11',
                'status' => 'diajukan',
            ],
            [
                'user_id' => $idSiti,
                'tgl_pinjam' => '2026-06-09',
                'tgl_kembali_plan' => '2026-06-12',
                'status' => 'diajukan',
            ],
        ];
        
        foreach ($peminjaman as $index => $pinjam) {
            Peminjaman::firstOrCreate([
                'user_id' => $pinjam['user_id'],
                'tgl_pinjam' => $pinjam['tgl_pinjam'],
                'tgl_kembali_plan' => $pinjam['tgl_kembali_plan'],
            ], $pinjam);
        }
    }
}
