<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Kategori;
use App\Models\User;
use App\Observers\AlatObserver;
use App\Observers\PeminjamanObserver;
use App\Observers\PengembalianObserver;
use App\Observers\KategoriObserver;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftarkan Observers untuk mencatat Log Aktivitas otomatis
        Alat::observe(AlatObserver::class);
        Peminjaman::observe(PeminjamanObserver::class);
        Pengembalian::observe(PengembalianObserver::class);
        Kategori::observe(KategoriObserver::class);
        User::observe(UserObserver::class);
    }
}
