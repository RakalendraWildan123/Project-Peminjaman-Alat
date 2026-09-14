<?php

namespace App\Observers;

use App\Models\Pengembalian;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;


class PengembalianObserver
{
    private function catatLog(string $pesan): void
    {
        if(Auth::check()) {
            LOgAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => $pesan,
            ]);
        }
    }

    public function created(Pengembalian $pengembalian): void
    {
        $this->catatLog("Memproses pengembalian alat untuk peminjaman ID: #{$pengembalian->peminajamn_id}");
    }


    public function updated(Pengembalian $pengembalian): void
    {
        $perubahan = array_diff(array_keys($pengembalian->getChanges()), ['updated_at']);

        if (!empty($perubahan)) {
            $kolom = implode(', ', $perubahan);
            $this->catatLog("Merevisi data pengembalian (ID Kembali: #{$pengembalian->peminajamn_id}, Peminjaman ID: #{$pengembalian->peminjaman_id}, KOlom diubah: {$kolom})");
        }
    }

    
    public function deleted(Pengembalian $pengembalian): void
    {
        $this->catatLog("Membatalkan/Menghapus riwayat pengembalian (peminjaman ID: #{$pengembalian->peminajamn_id})");
    }

    /**
     * Handle the Pengembalian "restored" event.
     */
    public function restored(Pengembalian $pengembalian): void
    {
        //
    }

    /**
     * Handle the Pengembalian "force deleted" event.
     */
    public function forceDeleted(Pengembalian $pengembalian): void
    {
        //
    }
}
