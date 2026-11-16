@extends('layouts.app')

@section('title', 'Kelola Alat - Panel Admin')
@section('header-title', 'Manajemen Data Alat')

@section('content')
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-200/80">
        <div class="p-5 border-b border-slate-100 bg-slate-50/60 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-base font-bold text-slate-800">Daftar Alat Laboratorium</h3>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <!-- Form Search -->
                <form action="{{ route('admin.alat.index') }}" method="GET" class="flex w-full md:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama alat, kategori..."
                        class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white">
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 text-sm font-semibold rounded-r-xl transition">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.alat.index') }}" 
                           class="ml-2 bg-slate-200 hover:bg-slate-300 text-slate-700 px-3.5 py-2 text-sm font-semibold rounded-xl flex items-center transition">
                            Reset
                        </a>
                    @endif
                </form>

                <!-- Tombol Tambah -->
                <a href="{{ route('admin.alat.create') }}" 
                   class="bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-md shadow-indigo-500/20 transition whitespace-nowrap">
                    + Tambah Alat
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Gambar</th>
                        <th class="py-3 px-4 border-b">Nama Alat</th>
                        <th class="py-3 px-4 border-b">Kategori</th>
                        <th class="py-3 px-4 border-b">Stok</th>
                        <th class="py-3 px-4 border-b">Kondisi</th>
                        <th class="py-3 px-4 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($alats as $alat)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 border-b">
                                @if($alat->gambar)
                                    <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="w-12 h-12 object-cover rounded-lg border">
                                @else
                                    <span class="text-xs text-gray-400 italic">Tidak ada</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 border-b font-medium text-gray-900">{{ $alat->nama_alat }}</td>
                            <td class="py-3 px-4 border-b">{{ $alat->kategori->nama_kategori ?? '-' }}</td>
                            <td class="py-3 px-4 border-b font-semibold">{{ $alat->stok }}</td>
                            <td class="py-3 px-4 border-b">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                    @if(strtolower($alat->status_kondisi) == 'baik') bg-emerald-100 text-emerald-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $alat->status_kondisi }}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.alat.edit', $alat->id) }}" 
                                       class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.alat.destroy', $alat->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmHapus(this, 'Alat: {{ addslashes($alat->nama_alat) }}')"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">Belum ada data alat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $alats->links() }}
        </div>
    </div>
@endsection