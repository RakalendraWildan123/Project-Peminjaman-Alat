@extends('layouts.peminjam')

@section('title', 'Dashboard Peminjam - AlatKu')
@section('header-title', 'Dashboard Peminjam')

@section('content')
    <!-- 1. HERO GREETING BANNER -->
    <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-lg mb-8 border border-indigo-900/40">
        <!-- Dekorasi Cahaya Latar -->
        <div class="absolute -right-10 -top-10 w-60 h-60 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-40 -bottom-10 w-48 h-48 bg-indigo-500/15 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-500/20 text-orange-400 border border-orange-500/30 text-xs font-semibold mb-3">
                <span>✨</span>
                <span>Selamat Datang di Portal Mandiri</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white mb-2">
                Halo, {{ auth()->user()->name }}! 👋
            </h2>
            <p class="text-sm sm:text-base text-slate-300 leading-relaxed mb-6">
                Pantau pengajuan alat Anda, ajukan pengembalian sebelum jatuh tempo, atau cari perlengkapan baru di katalog alat yang tersedia.
            </p>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('peminjam.katalog') }}"
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-semibold text-sm px-5 py-2.5 rounded-xl shadow-md shadow-orange-500/20 transition-all hover:scale-105">
                    <span>📦</span>
                    <span>Jelajahi Katalog Alat</span>
                </a>
                <a href="{{ route('peminjam.riwayat') }}"
                   class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/15 text-white font-semibold text-sm px-5 py-2.5 rounded-xl border border-white/15 transition-all">
                    <span>🧾</span>
                    <span>Lihat Status Pesanan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Card 1: Sedang Dipinjam -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Sedang Dipinjam</p>
                <p class="text-2xl font-black text-blue-600 mt-1">{{ $totalDipinjam }}</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Alat di tangan Anda</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                📦
            </div>
        </div>

        <!-- Card 2: Menunggu Verifikasi -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Menunggu Verifikasi</p>
                <p class="text-2xl font-black text-amber-500 mt-1">{{ $totalDiajukan }}</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Diproses oleh petugas</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl shadow-inner">
                ⏳
            </div>
        </div>

        <!-- Card 3: Peminjaman Selesai -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Selesai Dikembalikan</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $totalSelesai }}</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Riwayat sukses</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                ✅
            </div>
        </div>

        <!-- Card 4: Total Alat Siap Pinjam -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Katalog Tersedia</p>
                <p class="text-2xl font-black text-indigo-600 mt-1">{{ $totalAlatTersedia }}</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Item siap dipinjam</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-inner">
                🛠️
            </div>
        </div>
    </div>

    <!-- 3. BAGIAN PINJAMAN AKTIF (JIKA ADA) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mb-8">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                <h3 class="font-bold text-slate-800 text-base">Alat yang Sedang Dipinjam</h3>
            </div>
            <span class="text-xs text-slate-500 font-medium">
                {{ $pinjamanAktif->count() }} peminjaman aktif
            </span>
        </div>

        @if($pinjamanAktif->count() > 0)
            <div class="divide-y divide-slate-100">
                @foreach($pinjamanAktif as $pinjam)
                <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-50/60 transition">
                    <div class="space-y-1.5 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach($pinjam->detailPinjam as $det)
                                <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 font-semibold text-xs px-3 py-1 rounded-lg border border-blue-100">
                                    <span>🔧</span>
                                    <span>{{ $det->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                    <span class="bg-blue-200/60 text-blue-800 text-[10px] px-1.5 py-0.5 rounded-full font-bold">
                                        {{ $det->jumlah }} unit
                                    </span>
                                </span>
                            @endforeach
                        </div>
                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 pt-1">
                            <span>📅 Tgl Pinjam: <strong class="text-slate-700">{{ $pinjam->tgl_pinjam }}</strong></span>
                            <span>⏰ Batas Kembali: <strong class="text-slate-800 font-semibold">{{ $pinjam->tgl_kembali_plan }}</strong></span>
                        </div>
                    </div>

                    <div class="shrink-0 flex items-center gap-2">
                        @if($pinjam->permintaan_kembali)
                            <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200 text-xs font-semibold px-3 py-2 rounded-xl">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                Menunggu Verifikasi Petugas
                            </span>
                        @else
                            <form action="{{ route('peminjam.pengembalian.ajukan', $pinjam->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="button" onclick="confirmAjukanKembali(this)"
                                    class="inline-flex items-center gap-1.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs px-4 py-2 rounded-xl shadow-sm transition cursor-pointer">
                                    <span>📦</span>
                                    <span>Ajukan Pengembalian</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center">
                <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center text-2xl">
                    🎉
                </div>
                <h4 class="font-bold text-slate-700 text-sm">Tidak Ada Alat yang Sedang Dipinjam</h4>
                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1 mb-4">
                    Semua pinjaman Anda telah terselesaikan. Anda dapat meminjam alat baru kapan saja melalui katalog kami.
                </p>
                <a href="{{ route('peminjam.katalog') }}"
                   class="inline-flex items-center gap-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold px-4 py-2 rounded-xl transition shadow-sm">
                    Buka Katalog Alat &rarr;
                </a>
            </div>
        @endif
    </div>

    <!-- 4. DUA KOLOM: RIWAYAT TERBARU & REKOMENDASI ALAT -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Kolom Kiri: Riwayat Pengajuan Terkini (2 Kolom) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 text-base">Riwayat & Pengajuan Terkini</h3>
                    <a href="{{ route('peminjam.riwayat') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700 transition">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Alat Dipinjam</th>
                                <th class="py-3 px-4">Rencana Kembali</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($riwayatTerbaru as $item)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3 px-4 whitespace-nowrap text-slate-500">
                                    {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}
                                </td>
                                <td class="py-3 px-4 font-medium text-slate-900">
                                    <ul class="space-y-0.5">
                                        @foreach($item->detailPinjam as $det)
                                            <li>{{ $det->alat->nama_alat ?? 'Alat Dihapus' }} ({{ $det->jumlah }})</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-slate-600">
                                    {{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->format('d M Y') }}
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if($item->status == 'diajukan')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            Diajukan
                                        </span>
                                    @elseif($item->status == 'dipinjam')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                            Dipinjam
                                        </span>
                                    @elseif($item->status == 'selesai')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400">
                                    Belum ada catatan riwayat peminjaman.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50/30 text-right">
                <a href="{{ route('peminjam.riwayat') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                    Kelola Semua Pesanan &rarr;
                </a>
            </div>
        </div>

        <!-- Kolom Kanan: Rekomendasi Alat Siap Pinjam (1 Kolom) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 text-base">Alat Tersedia</h3>
                    <a href="{{ route('peminjam.katalog') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-700 transition">
                        Semua &rarr;
                    </a>
                </div>

                <div class="p-4 space-y-3">
                    @forelse($rekomendasiAlat as $alat)
                    <div class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 border border-slate-100 transition">
                        <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center overflow-hidden shrink-0">
                            @if($alat->gambar)
                                <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-xl">🛠️</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-slate-800 truncate">{{ $alat->nama_alat }}</h4>
                            <p class="text-[10px] text-slate-400 truncate">{{ $alat->kategori->nama_kategori ?? 'Umum' }}</p>
                            <span class="inline-block mt-1 text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                                Stok: {{ $alat->stok }}
                            </span>
                        </div>
                        <a href="{{ route('peminjam.alat.show', $alat->id) }}"
                           class="text-xs font-semibold text-orange-600 hover:text-white hover:bg-orange-500 border border-orange-200 hover:border-orange-500 px-2.5 py-1.5 rounded-lg transition shrink-0">
                            Pinjam
                        </a>
                    </div>
                    @empty
                    <p class="text-center text-xs text-slate-400 py-6">Katalog sedang kosong.</p>
                    @endforelse
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50/30">
                <a href="{{ route('peminjam.katalog') }}"
                   class="w-full block text-center bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold py-2 rounded-xl transition">
                    Buka Seluruh Katalog
                </a>
            </div>
        </div>

    </div>
@endsection
