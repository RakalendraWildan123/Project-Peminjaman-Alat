@extends('layouts.app')

@section('title', 'Persetujuan Peminjaman - Dashboard Petugas')
@section('header-title', 'Daftar Pengajuan Peminjaman Alat')

@section('content')
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-200/80">
        <div class="p-5 border-b border-slate-100 bg-slate-50/60 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-base font-bold text-slate-800">Menunggu Verifikasi Persetujuan</h3>
            <form action="{{ route('petugas.peminjaman.index') }}" method="GET" class="flex w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam..."
                    class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-white">
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 text-sm font-semibold rounded-r-xl transition">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('petugas.peminjaman.index') }}"
                       class="ml-2 bg-slate-200 hover:bg-slate-300 text-slate-700 px-3.5 py-2 text-sm font-semibold rounded-xl flex items-center transition whitespace-nowrap">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Tanggal Pinjam</th>
                        <th class="py-3 px-4 border-b">Rencana Kembali</th>
                        <th class="py-3 px-4 border-b">Detail Alat</th>
                        <th class="py-3 px-4 border-b text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($peminjamans as $item)
                    <tr class="hover:bg-gray-50 transition align-top">
                        <td class="py-3 px-4 border-b font-medium text-gray-900">
                            {{ $item->user->name ?? 'User Dihapus' }}
                        </td>
                        <td class="py-3 px-4 border-b">{{ $item->tgl_pinjam }}</td>
                        <td class="py-3 px-4 border-b">{{ $item->tgl_kembali_plan }}</td>
                        <td class="py-3 px-4 border-b">
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                @foreach($item->detailPinjam as $detail)
                                <li>
                                    <span class="font-semibold">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                    (Jumlah: {{ $detail->jumlah }})
                                </li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="py-3 px-4 border-b text-center">
                            @php
                                $alatList = $item->detailPinjam->map(function($d) {
                                    return ($d->alat->nama_alat ?? 'Alat') . ' (' . $d->jumlah . ' unit)';
                                })->implode(', ');
                            @endphp
                            <div class="flex justify-center items-center space-x-2">
                                <!-- Tombol Setujui -->
                                <form action="{{ route('petugas.peminjaman.setujui', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="button"
                                        onclick="confirmSetujui(this, '{{ addslashes($item->user->name ?? 'Peminjam') }}', '{{ addslashes($alatList) }}')"
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition shadow-sm inline-flex items-center gap-1 cursor-pointer hover:shadow">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        Setujui
                                    </button>
                                </form>

                                <!-- Tombol Tolak -->
                                <form action="{{ route('petugas.peminjaman.tolak', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="button"
                                        onclick="confirmTolak(this, '{{ addslashes($item->user->name ?? 'Peminjam') }}')"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition shadow-sm inline-flex items-center gap-1 cursor-pointer hover:shadow">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-500">Tidak ada pengajuan peminjaman baru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $peminjamans->links() }}
        </div>
    </div>
@endsection