@extends('layouts.peminjam')

@section('title', $alat->nama_alat . ' - Detail Alat')
@section('header-title', 'Detail Informasi Alat')

@section('content')
<div class="mb-5">
    <a href="{{ route('peminjam.katalog') }}" class="inline-flex items-center gap-1.5 text-xs text-orange-600 hover:text-orange-700 font-bold bg-orange-50 hover:bg-orange-100 px-3.5 py-2 rounded-xl transition">
        <span>&larr;</span>
        <span>Kembali ke Katalog Alat</span>
    </a>
</div>

<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="grid md:grid-cols-2 gap-0">
        <!-- Kolom Foto -->
        <div class="aspect-square bg-slate-100 relative flex items-center justify-center p-8 border-b md:border-b-0 md:border-r border-slate-100">
            @if($alat->gambar)
                <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="w-full h-full object-contain rounded-2xl">
            @else
                <span class="text-7xl">🛠️</span>
            @endif

            <span class="absolute top-4 left-4 bg-slate-900/70 backdrop-blur-md text-white text-xs font-semibold px-3 py-1 rounded-full">
                {{ $alat->kategori->nama_kategori ?? 'Umum' }}
            </span>
        </div>

        <!-- Kolom Form & Detail -->
        <div class="p-6 sm:p-8 flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-orange-600 bg-orange-50 px-2.5 py-0.5 rounded-full">
                    {{ $alat->kategori->nama_kategori ?? 'Umum' }}
                </span>
                <h1 class="text-2xl font-black text-slate-900 mt-2 leading-tight">{{ $alat->nama_alat }}</h1>

                <!-- Status Pills -->
                <div class="mt-5 grid grid-cols-2 gap-3">
                    <div class="bg-emerald-50/70 border border-emerald-100 rounded-2xl p-3.5">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-800">Stok Tersedia</p>
                        <p class="text-2xl font-extrabold text-emerald-700 mt-0.5">{{ $alat->stok }} <span class="text-xs font-medium text-emerald-600">Unit</span></p>
                    </div>
                    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3.5">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Kondisi Alat</p>
                        <p class="text-xl font-extrabold text-slate-800 mt-0.5">{{ $alat->status_kondisi }}</p>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="mt-6">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Deskripsi & Spesifikasi</h2>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        {{ $alat->deskripsi ?: 'Tidak ada deskripsi rinci untuk perlengkapan ini.' }}
                    </p>
                </div>
            </div>

            <!-- Form Pengajuan Langsung -->
            <form action="{{ route('peminjam.ajukan') }}" method="POST" class="mt-8 border-t border-slate-100 pt-6">
                @csrf
                <input type="hidden" name="alat_id" value="{{ $alat->id }}">

                <div class="grid sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="jumlah" class="block text-xs font-bold text-slate-700 mb-1.5">Jumlah Pinjam (Max: {{ $alat->stok }})</label>
                        <input id="jumlah" type="number" name="jumlah" value="1" min="1" max="{{ $alat->stok }}" required
                            class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                    </div>
                    <div>
                        <label for="tgl_kembali_plan" class="block text-xs font-bold text-slate-700 mb-1.5">Rencana Tanggal Pengembalian</label>
                        <input id="tgl_kembali_plan" type="date" name="tgl_kembali_plan" min="{{ now()->addDay()->format('Y-m-d') }}" required
                            class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-sm py-3 rounded-xl shadow-md shadow-orange-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span>📦</span>
                    <span>Ajukan Peminjaman Sekarang</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
