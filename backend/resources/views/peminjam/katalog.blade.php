@extends('layouts.peminjam')

@section('title', 'Katalog Alat - AlatKu')
@section('header-title', 'Katalog Alat Tersedia')

@section('content')
    <!-- FILTER & PENCARIAN -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm mb-6">
        <form action="{{ route('peminjam.katalog') }}" method="GET" class="flex flex-col md:flex-row gap-4 justify-between items-center">
            <!-- Input Cari -->
            <div class="relative w-full md:w-96">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    🔍
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama alat..."
                    class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition">
            </div>

            <!-- Filter Kategori & Tombol -->
            <div class="flex items-center gap-2 w-full md:w-auto">
                <select name="kategori_id" onchange="this.form.submit()"
                        class="w-full md:w-auto px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-slate-700 font-medium cursor-pointer">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition shadow-sm whitespace-nowrap">
                    Filter
                </button>

                @if(request('search') || request('kategori_id'))
                    <a href="{{ route('peminjam.katalog') }}"
                       class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold px-4 py-2.5 rounded-xl transition whitespace-nowrap">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- DAFTAR GRID ALAT -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
        @forelse($alats as $alat)
        <div class="group bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col justify-between">
            <div>
                <!-- Gambar Alat -->
                <div class="aspect-video sm:aspect-square bg-slate-100 relative overflow-hidden flex items-center justify-center">
                    @if($alat->gambar_url)
                        <img src="{{ $alat->gambar_url }}" alt="{{ $alat->nama_alat }}"
                             onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span class="text-5xl group-hover:scale-110 transition-transform hidden">🛠️</span>
                    @else
                        <span class="text-5xl group-hover:scale-110 transition-transform">🛠️</span>
                    @endif

                    <!-- Badge Kategori Floating -->
                    <span class="absolute top-3 left-3 bg-slate-900/70 backdrop-blur-md text-white text-[11px] font-semibold px-2.5 py-1 rounded-full border border-white/20">
                        {{ $alat->kategori->nama_kategori ?? 'Umum' }}
                    </span>

                    <!-- Badge Kondisi -->
                    <span class="absolute top-3 right-3 text-[10px] font-bold px-2 py-0.5 rounded-full {{ $alat->status_kondisi == 'Baik' ? 'bg-emerald-500/90 text-white' : 'bg-amber-500/90 text-white' }}">
                        {{ $alat->status_kondisi }}
                    </span>
                </div>

                <!-- Konten Teks -->
                <div class="p-4">
                    <h3 class="font-bold text-slate-900 text-sm line-clamp-1 group-hover:text-orange-600 transition-colors">
                        {{ $alat->nama_alat }}
                    </h3>

                    <div class="mt-2.5 flex items-center justify-between text-xs">
                        <span class="text-slate-500">Ketersediaan:</span>
                        <span class="font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                            {{ $alat->stok }} Unit Tersedia
                        </span>
                    </div>

                    @if($alat->deskripsi)
                        <p class="mt-2 text-xs text-slate-500 line-clamp-2 leading-relaxed">
                            {{ $alat->deskripsi }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="p-4 pt-0 space-y-2">
                <button type="button" onclick="document.getElementById('modal-{{ $alat->id }}').classList.remove('hidden')"
                    class="w-full bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-semibold text-xs py-2.5 rounded-xl shadow-md shadow-orange-500/20 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <span>📦</span>
                    <span>Ajukan Peminjaman</span>
                </button>
                <a href="{{ route('peminjam.alat.show', $alat->id) }}"
                    class="w-full block text-center bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold text-xs py-2 rounded-xl border border-slate-200 transition">
                    Lihat Detail Alat
                </a>
            </div>
        </div>

        <!-- MODAL AJUKAN PEMINJAMAN -->
        <div id="modal-{{ $alat->id }}" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 transition-all">
            <div class="bg-white rounded-3xl shadow-2xl p-6 sm:p-7 w-full max-w-md border border-slate-100 animate-in fade-in zoom-in duration-200">
                <div class="flex items-start justify-between pb-4 mb-4 border-b border-slate-100">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 bg-orange-50 px-2.5 py-0.5 rounded-full">
                            Pengajuan Pinjam
                        </span>
                        <h4 class="font-extrabold text-slate-900 text-base mt-1.5">{{ $alat->nama_alat }}</h4>
                    </div>
                    <button type="button" onclick="document.getElementById('modal-{{ $alat->id }}').classList.add('hidden')"
                            class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                        ✕
                    </button>
                </div>

                <form action="{{ route('peminjam.ajukan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="alat_id" value="{{ $alat->id }}">

                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl mb-4 text-xs flex justify-between items-center text-slate-600">
                        <span>Maksimal Unit:</span>
                        <span class="font-bold text-slate-800">{{ $alat->stok }} Unit</span>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Jumlah Unit</label>
                        <input type="number" name="jumlah" value="1" min="1" max="{{ $alat->stok }}" required
                            class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Rencana Tanggal Pengembalian</label>
                        <input type="date" name="tgl_kembali_plan" min="{{ now()->addDay()->format('Y-m-d') }}" required
                            class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                        <p class="text-[11px] text-slate-400 mt-1">Pilih tanggal rencana Anda mengembalikan alat ke petugas.</p>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                        <button type="button" onclick="document.getElementById('modal-{{ $alat->id }}').classList.add('hidden')"
                            class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2.5 rounded-xl text-xs transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-semibold px-5 py-2.5 rounded-xl text-xs shadow-md shadow-orange-500/20 transition">
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-slate-200/80 p-8">
            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center text-3xl">
                🔍
            </div>
            <h3 class="font-bold text-slate-800 text-base">Tidak Ditemukan Alat</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-4">
                Tidak ada alat yang sesuai dengan kriteria pencarian atau kategori yang dipilih.
            </p>
            <a href="{{ route('peminjam.katalog') }}"
               class="inline-block bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold px-4 py-2 rounded-xl transition">
                Reset Pencarian
            </a>
        </div>
        @endforelse
    </div>

    <!-- PAGINATION -->
    <div class="mt-8">
        {{ $alats->links() }}
    </div>
@endsection
