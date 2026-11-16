@extends('layouts.peminjam')

@section('title', 'Pesanan & Riwayat - AlatKu')
@section('header-title', 'Status & Riwayat Peminjaman')

@section('content')
    <!-- FILTER STATUS TAB -->
    <div class="flex flex-wrap gap-2 mb-6">
        @foreach(['semua' => 'Semua Pengajuan', 'diajukan' => '⏳ Menunggu (Diajukan)', 'dipinjam' => '📦 Sedang Dipinjam', 'selesai' => '✅ Selesai'] as $key => $label)
            <a href="{{ route('peminjam.riwayat', ['tab' => $key]) }}"
               class="px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 {{ $tab == $key ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-md shadow-orange-500/20' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- LIST RIWAYAT PEMINJAMAN -->
    <div class="space-y-4">
        @forelse($peminjamans as $item)
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow p-5 overflow-hidden">
            <!-- Header Kartu -->
            <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center text-sm font-bold">
                        #{{ $item->id }}
                    </div>
                    <div>
                        <div class="text-xs text-slate-400">Tanggal Pengajuan:</div>
                        <div class="text-xs font-bold text-slate-800">
                            {{ \Carbon\Carbon::parse($item->tgl_pinjam)->translatedFormat('l, d F Y') }}
                        </div>
                    </div>
                </div>

                <!-- Status Badge -->
                <div>
                    @if($item->status == 'diajukan')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            Menunggu Persetujuan
                        </span>
                    @elseif($item->status == 'dipinjam')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            Sedang Dipinjam
                        </span>
                    @elseif($item->status == 'selesai')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Selesai Dikembalikan
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-red-50 text-red-700 border border-red-200">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            Ditolak
                        </span>
                    @endif
                </div>
            </div>

            <!-- Daftar Alat -->
            <div class="py-3.5 space-y-2">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Daftar Alat yang Dipinjam:</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($item->detailPinjam as $d)
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                        <span class="font-semibold text-slate-800 flex items-center gap-2">
                            <span>🛠️</span>
                            <span>{{ $d->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                        </span>
                        <span class="bg-white border border-slate-200 px-2.5 py-0.5 rounded-full text-slate-600 font-bold">
                            {{ $d->jumlah }} unit
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Footer Kartu -->
            <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-1.5 text-slate-500">
                    <span>⏰</span>
                    <span>Batas Rencana Pengembalian:</span>
                    <strong class="text-slate-800 font-semibold">{{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->translatedFormat('d F Y') }}</strong>
                </div>

                @if($item->status == 'dipinjam')
                    @if($item->permintaan_kembali)
                        <span class="inline-flex items-center gap-1.5 text-blue-600 font-semibold bg-blue-50 px-3 py-1.5 rounded-xl border border-blue-100">
                            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                            Permintaan kembali sedang diverifikasi petugas
                        </span>
                    @else
                        <form action="{{ route('peminjam.pengembalian.ajukan', $item->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="button" onclick="confirmAjukanKembali(this)"
                                class="bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white px-4 py-2 rounded-xl text-xs font-semibold transition cursor-pointer shadow-sm shadow-orange-500/20 inline-flex items-center gap-1.5">
                                <span>📦</span>
                                <span>Ajukan Pengembalian</span>
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center text-3xl">
                🧾
            </div>
            <h4 class="font-bold text-slate-700 text-sm">Belum Ada Riwayat Peminjaman</h4>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-4">
                Pengajuan yang Anda buat akan tercatat secara detail pada halaman ini.
            </p>
            <a href="{{ route('peminjam.katalog') }}"
               class="inline-block bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold px-4 py-2 rounded-xl transition">
                Jelajahi Katalog &rarr;
            </a>
        </div>
        @endforelse
    </div>

    <!-- PAGINATION -->
    <div class="mt-6">
        {{ $peminjamans->links() }}
    </div>
@endsection
