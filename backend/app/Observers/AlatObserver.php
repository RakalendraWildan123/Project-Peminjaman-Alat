<?php

namespace App\Observers;

use App\Models\Alat;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class AlatObserver
{
    private function catatLog(string $pesan): void
    {
        $userId = Auth::id() ?? 1;
        LogAktivitas::create([
            'user_id' => $userId,
            'aktivitas' => $pesan,
        ]);
    }

    public function created(Alat $alat): void
    {
        $this->catatLog("Menambahkan alat baru: '{$alat->nama_alat}' (Stok: {$alat->stok})");
    }

    public function updated(Alat $alat): void
    {
        $perubahan = array_diff(array_keys($alat->getChanges()), ['updated_at']);
        if (!empty($perubahan)) {
            $kolom = implode(', ', $perubahan);
            $this->catatLog("Memperbarui data alat '{$alat->nama_alat}' (kolom diubah: {$kolom})");
        }
    }

    public function deleted(Alat $alat): void
    {
        $this->catatLog("Menghapus data alat: '{$alat->nama_alat}' (ID: #{$alat->id})");
    }
}
