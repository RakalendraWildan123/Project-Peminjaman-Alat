<?php

namespace App\Observers;

use App\Models\User;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    private function catatLog(string $pesan, ?int $userId = null): void
    {
        $userId = $userId ?? Auth::id() ?? 1;
        LogAktivitas::create([
            'user_id' => $userId,
            'aktivitas' => $pesan,
        ]);
    }

    public function created(User $user): void
    {
        $operator = Auth::check() ? Auth::user()->name : 'Registrasi Mandiri';
        $this->catatLog("Pengguna baru ditambahkan: '{$user->name}' (Role: {$user->role}) oleh {$operator}", $user->id);
    }

    public function updated(User $user): void
    {
        $changes = array_diff(array_keys($user->getChanges()), ['updated_at', 'remember_token']);
        if (!empty($changes)) {
            $changedCols = implode(', ', $changes);
            $this->catatLog("Memperbarui data pengguna '{$user->name}' (kolom diubah: {$changedCols})", $user->id);
        }
    }

    public function deleted(User $user): void
    {
        $this->catatLog("Menghapus akun pengguna: '{$user->name}' ({$user->email})");
    }
}
