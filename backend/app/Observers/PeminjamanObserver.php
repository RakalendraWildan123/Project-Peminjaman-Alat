<?php

namespace App\Observers;

use App\Models\Peminjaman;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class PeminjamanObserver
{
    private function catatLog(string $pesan, ?int $userId = null): void
    {
        $userId = $userId ?? Auth::id() ?? 1;
        LogAktivitas::create([
            'user_id' => $userId,
            'aktivitas' => $pesan,
        ]);
    }

    public function created(Peminjaman $peminjaman): void
    {
        $namaPeminjam = $peminjaman->user?->name ?? 'User';
        $this->catatLog("Peminjam ({$namaPeminjam}) mengajukan permohonan peminjaman baru (ID: #{$peminjaman->id})", $peminjaman->user_id);
    }

    public function updated(Peminjaman $peminjaman): void
    {
        if ($peminjaman->wasChanged('status')) {
            $namaPeminjam = $peminjaman->user?->name ?? 'User';
            $this->catatLog("Status peminjaman ID #{$peminjaman->id} ({$namaPeminjam}) diubah menjadi: '{$peminjaman->status}'");
        } elseif ($peminjaman->wasChanged('permintaan_kembali') && $peminjaman->permintaan_kembali) {
            $namaPeminjam = $peminjaman->user?->name ?? 'User';
            $this->catatLog("Peminjam ({$namaPeminjam}) mengajukan pengembalian untuk peminjaman ID #{$peminjaman->id}", $peminjaman->user_id);
        } else {
            $changes = array_diff(array_keys($peminjaman->getChanges()), ['updated_at']);
            if (!empty($changes)) {
                $this->catatLog("Memperbarui detail data peminjaman ID #{$peminjaman->id}");
            }
        }
    }

    public function deleted(Peminjaman $peminjaman): void
    {
        $this->catatLog("Menghapus/membatalkan data peminjaman ID #{$peminjaman->id}");
    }
}
