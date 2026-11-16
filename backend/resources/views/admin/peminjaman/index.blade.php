```php
@extends('layouts.app')

@section('title', 'Kelola Peminjaman – Panel Admin')
@section('header-title', 'Manajemen Transaksi Peminjaman')

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
            <h3 class="text-base font-bold text-slate-800">Daftar Transaksi Peminjaman</h3>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <form action="{{ route('admin.peminjaman.index') }}" method="GET" class="flex w-full md:w-80">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama peminjam / status..."
                        class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white">

                    <button type="submit"
                        class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 text-sm font-semibold rounded-r-xl transition">
                        Cari
                    </button>

                    @if(request('search'))
                        <a href="{{ route('admin.peminjaman.index') }}"
                            class="ml-2 bg-slate-200 hover:bg-slate-300 text-slate-700 px-3.5 py-2 text-sm font-semibold rounded-xl flex items-center transition">
                            Reset
                        </a>
                    @endif
                </form>

                <a href="{{ route('admin.peminjaman.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
                    + Tambah Peminjaman
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Alat yang Dipinjam</th>
                        <th class="py-3 px-4 border-b">Tgl Pinjam / Rencana Kembali</th>
                        <th class="py-3 px-4 border-b">Status</th>
                        <th class="py-3 px-4 border-b">Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-gray-700 text-sm">
                    @forelse($peminjamans as $peminjaman)
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="py-3 px-4 border-b font-medium text-gray-900">
                                {{ $peminjaman->user->name ?? 'User Dihapus' }}
                            </td>

                            <td class="py-3 px-4 border-b">
                               <ul class="list-disc list-inside space-y-1">
                                    @forelse($peminjaman->detailPinjam ?? [] as $detail)
                                        <li>
                                            <span class="font-semibold">
                                                {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}
                                            </span>
                                            <span class="text-xs bg-gray-200 px-1.5 py-0.5 rounded">
                                                ({{ $detail->jumlah }} pcs)
                                            </span>
                                        </li>
                                    @empty
                                        <li class="text-gray-400 italic">
                                            Tidak ada detail alat.
                                        </li>
                                    @endforelse
                                </ul>
                            </td>

                            <td class="py-3 px-4 border-b text-xs text-gray-600">
                                <span class="block">Pinjam: {{ $peminjaman->tgl_pinjam }}</span>
                                <span class="block font-semibold">
                                    Rencana: {{ $peminjaman->tgl_kembali_plan }}
                                </span>
                            </td>

                            <td class="py-3 px-4 border-b">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                    @if($peminjaman->status == 'diajukan') bg-yellow-100 text-yellow-800
                                    @elseif($peminjaman->status == 'dipinjam') bg-blue-100 text-blue-800
                                    @elseif($peminjaman->status == 'selesai') bg-emerald-100 text-emerald-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($peminjaman->status) }}
                                </span>
                            </td>

                            <td class="py-3 px-4 border-b">
                                <form action="{{ route('admin.peminjaman.updateStatus', $peminjaman->id) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')

                                    <select name="status" onchange="this.form.submit()"
                                        class="text-xs border border-gray-300 rounded px-2 py-1 focus:outline-none">
                                        <option value="diajukan" {{ $peminjaman->status == 'diajukan' ? 'selected' : '' }}>
                                            Diajukan
                                        </option>
                                        <option value="dipinjam" {{ $peminjaman->status == 'dipinjam' ? 'selected' : '' }}>
                                            Dipinjam
                                        </option>
                                        <option value="selesai" {{ $peminjaman->status == 'selesai' ? 'selected' : '' }}>
                                            Selesai
                                        </option>
                                        <option value="telat" {{ $peminjaman->status == 'telat' ? 'selected' : '' }}>
                                            Telat
                                        </option>
                                    </select>
                                </form>

                                <p class="text-xs text-gray-400 mt-2">
                                    Data transaksi tersimpan sebagai arsip.
                                </p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500">
                                Belum ada data peminjaman.
                            </td>
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
```