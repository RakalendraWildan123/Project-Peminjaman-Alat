<?php

namespace Database\Seeders;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Database\Seeder;

class LogAktivitasSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $petugas = User::where('role', 'petugas')->first() ?? $admin;
        $peminjam = User::where('role', 'peminjam')->first() ?? $admin;

        if (!$admin) {
            return;
        }

        $logs = [
            [
                'user_id' => $admin->id,
                'aktivitas' => 'Melakukan import data master alat baru sebanyak 5 entitas.',
            ],
            [
                'user_id' => $petugas ? $petugas->id : $admin->id,
                'aktivitas' => 'Menyetujui permohonan peminjaman ID #4.',
            ],
            [
                'user_id' => $peminjam ? $peminjam->id : $admin->id,
                'aktivitas' => 'Mengajukan peminjaman alat baru untuk kebutuhan praktik kelompok.',
            ],
            [
                'user_id' => $petugas ? $petugas->id : $admin->id,
                'aktivitas' => 'Memproses pengembalian alat telat untuk peminjaman ID #3 dan mengenakan denda.',
            ],
            [
                'user_id' => $admin->id,
                'aktivitas' => 'Mengubah konfigurasi hak akses aplikasi.',
            ],
        ];

        foreach ($logs as $log) {
            LogAktivitas::firstOrCreate([
                'user_id' => $log['user_id'],
                'aktivitas' => $log['aktivitas'],
            ], $log);
        }
    }
}