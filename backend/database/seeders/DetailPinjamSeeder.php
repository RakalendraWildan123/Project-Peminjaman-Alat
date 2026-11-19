<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Peminjaman;
use Illuminate\Database\Seeder;

class DetailPinjamSeeder extends Seeder
{
    public function run(): void
    {
        $peminjamanList = Peminjaman::orderBy('id')->get();
        $alatList = Alat::orderBy('id')->get();

        if ($peminjamanList->isEmpty() || $alatList->isEmpty()) {
            return;
        }

        $p1 = $peminjamanList[0]->id;
        $p2 = $peminjamanList[1]->id ?? $p1;
        $p3 = $peminjamanList[2]->id ?? $p1;
        $p4 = $peminjamanList[3]->id ?? $p1;
        $p5 = $peminjamanList[4]->id ?? $p1;

        $a1 = $alatList[0]->id;
        $a2 = $alatList[1]->id ?? $a1;
        $a3 = $alatList[2]->id ?? $a1;
        $a4 = $alatList[3]->id ?? $a1;
        $a5 = $alatList[4]->id ?? $a1;

        $details = [
            ['peminjaman_id' => $p1, 'alat_id' => $a1, 'jumlah' => 2], // Pinjam 2 Mikrotik
            ['peminjaman_id' => $p2, 'alat_id' => $a2, 'jumlah' => 1], // Pinjam 1 Kamera
            ['peminjaman_id' => $p3, 'alat_id' => $a3, 'jumlah' => 1], // Pinjam 1 Mini PC
            ['peminjaman_id' => $p4, 'alat_id' => $a4, 'jumlah' => 2], // Pinjam 2 Tang Crimping
            ['peminjaman_id' => $p5, 'alat_id' => $a5, 'jumlah' => 3], // Pinjam 3 Adapter
        ];

        foreach ($details as $detail) {
            DetailPinjam::firstOrCreate([
                'peminjaman_id' => $detail['peminjaman_id'],
                'alat_id' => $detail['alat_id'],
            ], $detail);
        }
    }
}