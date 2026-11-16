<?php

namespace App\Observers;

use App\Models\Pengembalian;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class PengembalianObserver
{
    private function catatLog(string $pesan): void
    {
        $userId = Auth::id() ?? 1;
        LogAktivitas::create([
            'user_id' => $userId,
            'aktivitas' => $pesan,
        ]);
    }

    public function created(Pengembalian $pengembalian): void
    {
        $peminjamName = $pengembalian->peminjaman?->user?->name ?? 'User';
        $dendaFormatted = $pengembalian->denda > 0 ? "Rp " . number_format($pengembalian->denda) : "Rp 0";
        $this->catatLog("Memproses pengembalian alat untuk peminjaman ID #{$pengembalian->peminjaman_id} ({$peminjamName}) - Kondisi: {$pengembalian->kondisi_kembali}, Denda: {$dendaFormatted}");
    }

    public function updated(Pengembalian $pengembalian): void
    {
        $perubahan = array_diff(array_keys($pengembalian->getChanges()), ['updated_at']);
        if (!empty($perubahan)) {
            $kolom = implode(', ', $perubahan);
            $this->catatLog("Memperbarui data pengembalian ID #{$pengembalian->id} (Peminjaman ID: #{$pengembalian->peminjaman_id}, kolom diubah: {$kolom})");
        }
    }

    public function deleted(Pengembalian $pengembalian): void
    {
        $this->catatLog("Menghapus data riwayat pengembalian ID #{$pengembalian->id} (Peminjaman ID: #{$pengembalian->peminjaman_id})");
    }
}
