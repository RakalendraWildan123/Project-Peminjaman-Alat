@extends('layouts.app')

@section('title', 'Dashboard Petugas - AlatKu')
@section('header-title', 'Dashboard Operasional Petugas')

@section('content')
    <!-- 1. HERO GREETING BANNER -->
    <div class="relative overflow-hidden bg-gradient-to-r from-slate-950 via-slate-900 to-teal-950 rounded-3xl p-6 sm:p-8 text-white shadow-lg mb-8 border border-teal-900/40">
        <!-- Dekorasi Cahaya Latar -->
        <div class="absolute -right-10 -top-10 w-60 h-60 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-40 -bottom-10 w-48 h-48 bg-teal-500/15 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-semibold mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Petugas Operasional Aktif</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white mb-2">
                Halo, {{ auth()->user()->name }}! 👋
            </h2>
            <p class="text-sm sm:text-base text-slate-300 leading-relaxed mb-6">
                Selamat datang di panel verifikasi peminjaman. Pantau pengajuan baru yang masuk, periksa pengembalian fisik alat, atau cetak laporan transaksi.
            </p>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('petugas.peminjaman.index') }}"
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold text-sm px-5 py-2.5 rounded-xl shadow-md shadow-emerald-500/20 transition-all hover:scale-105">
                    <span>⚡</span>
                    <span>Persetujuan Pinjam ({{ $totalMenunggu }})</span>
                </a>
                <a href="{{ route('petugas.pengembalian.index') }}"
                   class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/15 text-white font-semibold text-sm px-5 py-2.5 rounded-xl border border-white/15 transition-all">
                    <span>🔄</span>
                    <span>Pantau Pengembalian</span>
                </a>
                <a href="{{ route('petugas.laporan.index') }}"
                   class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/15 text-white font-semibold text-sm px-5 py-2.5 rounded-xl border border-white/15 transition-all">
                    <span>🖨️</span>
                    <span>Cetak Laporan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Card 1: Menunggu Persetujuan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Menunggu Persetujuan</p>
                <p class="text-2xl font-black text-amber-500 mt-1">{{ $totalMenunggu }}</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Pengajuan butuh verifikasi</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl shadow-inner">
                ⏳
            </div>
        </div>

        <!-- Card 2: Alat Sedang Dipinjam -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Sedang Dipinjam</p>
                <p class="text-2xl font-black text-blue-600 mt-1">{{ $totalDipinjam }}</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Unit alat aktif di luar</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                📦
            </div>
        </div>

        <!-- Card 3: Pengembalian Bulan Ini -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pengembalian Bulan Ini</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $totalPengembalianBulanIni }}</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Selesai terverifikasi</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                ✅
            </div>
        </div>

        <!-- Card 4: Denda Terkumpul Bulan Ini -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Denda Terkumpul</p>
                <p class="text-2xl font-black text-rose-600 mt-1">Rp {{ number_format($totalDendaBulanIni) }}</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Periode bulan berjalan</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shadow-inner">
                💰
            </div>
        </div>
    </div>

    <!-- 3. TABEL PENGAJUAN TERBARU -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                <h3 class="font-bold text-slate-800 text-base">Pengajuan Terbaru Menunggu Persetujuan</h3>
            </div>
            <a href="{{ route('petugas.peminjaman.index') }}"
               class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                <span>Buka Panel Persetujuan</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <th class="py-3 px-4">Peminjam</th>
                        <th class="py-3 px-4">Detail Alat</th>
                        <th class="py-3 px-4">Rencana Kembali</th>
                        <th class="py-3 px-4 text-center">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($peminjamanTerbaru as $item)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900">{{ $item->user->name ?? 'User Dihapus' }}</div>
                            <div class="text-[10px] text-slate-400">ID: #{{ $item->id }} • {{ $item->tgl_pinjam }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($item->detailPinjam as $d)
                                    <span class="bg-slate-100 text-slate-700 font-semibold px-2.5 py-0.5 rounded-md border border-slate-200 text-[11px]">
                                        {{ $d->alat->nama_alat ?? 'Alat Dihapus' }} ({{ $d->jumlah }} unit)
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="py-3 px-4 text-slate-600 font-medium">
                            {{ $item->tgl_kembali_plan }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('petugas.peminjaman.index') }}"
                               class="inline-flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold px-3 py-1.5 rounded-lg shadow-sm transition">
                                <span>Verifikasi</span>
                                <span>&rarr;</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-slate-400">
                            Tidak ada pengajuan peminjaman baru yang menunggu verifikasi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
