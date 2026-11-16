<?php

namespace App\Observers;

use App\Models\Kategori;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class KategoriObserver
{
    private function catatLog(string $pesan): void
    {
        $userId = Auth::id() ?? 1;
        LogAktivitas::create([
            'user_id' => $userId,
            'aktivitas' => $pesan,
        ]);
    }

    public function created(Kategori $kategori): void
    {
        $this->catatLog("Menambahkan kategori alat baru: '{$kategori->nama_kategori}'");
    }

    public function updated(Kategori $kategori): void
    {
        $this->catatLog("Mengubah data kategori menjadi: '{$kategori->nama_kategori}'");
    }

    public function deleted(Kategori $kategori): void
    {
        $this->catatLog("Menghapus kategori alat: '{$kategori->nama_kategori}'");
    }
}
