@extends('layouts.app')

@section('title', 'Pemantauan Pengembalian - Dashboard Petugas')
@section('header-title', 'Pemantauan Pengembalian Alat')

@section('content')
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">{{ session('error') }}</div>
    @endif

    <!-- BAGIAN 1: Menunggu Pengembalian -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-200/80 mb-6">
        <div class="p-5 border-b border-slate-100 bg-slate-50/60 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-base font-bold text-slate-800">Menunggu Pengembalian</h3>
            <form action="{{ route('petugas.pengembalian.index') }}" method="GET" class="flex w-full md:w-auto">
                <input type="hidden" name="search_riwayat" value="{{ request('search_riwayat') }}">
                <input type="text" name="search_aktif" value="{{ request('search_aktif') }}" placeholder="Cari nama peminjam..."
                    class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-white">
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 text-sm font-semibold rounded-r-xl transition">Cari</button>
                @if(request('search_aktif'))
                    <a href="{{ route('petugas.pengembalian.index', ['search_riwayat' => request('search_riwayat')]) }}"
                       class="ml-2 bg-slate-200 hover:bg-slate-300 text-slate-700 px-3.5 py-2 text-sm font-semibold rounded-xl flex items-center transition whitespace-nowrap">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Alat</th>
                        <th class="py-3 px-4 border-b">Rencana Kembali</th>
                        <th class="py-3 px-4 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($peminjamanAktif as $item)
                    <tr class="hover:bg-gray-50 transition align-top">
                        <td class="py-3 px-4 border-b font-medium">
                            {{ $item->user->name ?? '-' }}
                            @if($item->permintaan_kembali)
                                <span class="ml-1 text-xs bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded-full">Diminta kembali</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 border-b">
                            @foreach($item->detailPinjam as $d)
                                <span class="text-xs bg-gray-200 px-1.5 py-0.5 rounded">{{ $d->alat->nama_alat ?? '-' }} ({{ $d->jumlah }} pcs)</span>
                            @endforeach
                        </td>
                        <td class="py-3 px-4 border-b">{{ $item->tgl_kembali_plan }}</td>
                        <td class="py-3 px-4 border-b">
                            <button type="button" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold"
                                onclick="document.getElementById('modal-{{ $item->id }}').classList.remove('hidden')">
                                Proses Pengembalian
                            </button>

                            <div id="modal-{{ $item->id }}" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                                <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md border border-gray-100">
                                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                                        <h4 class="font-bold text-gray-800 text-base">Proses Pengembalian</h4>
                                        <span class="text-xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-full font-medium">{{ $item->user->name ?? '-' }}</span>
                                    </div>
                                    <form action="{{ route('petugas.pengembalian.proses', $item->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Kondisi Alat Saat Kembali</label>
                                            <select name="kondisi_kembali" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                                                <option value="Baik">Baik (Lengkap & Normal)</option>
                                                <option value="Rusak Ringan">Rusak Ringan</option>
                                                <option value="Rusak Berat">Rusak Berat</option>
                                                <option value="Hilang">Hilang</option>
                                            </select>
                                        </div>
                                        <div class="mb-5">
                                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Denda Keterlambatan/Kerusakan (Rp)</label>
                                            <input type="number" name="denda" value="0" min="0" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="0">
                                        </div>
                                        <div class="flex justify-end gap-2.5 pt-2">
                                            <button type="button" onclick="document.getElementById('modal-{{ $item->id }}').classList.add('hidden')"
                                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-semibold transition">Batal</button>
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-semibold shadow-sm transition">Simpan Pengembalian</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-4 text-center text-gray-500">Tidak ada alat yang menunggu dikembalikan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 bg-gray-50">{{ $peminjamanAktif->links() }}</div>
    </div>

    <!-- BAGIAN 2: Riwayat Pengembalian -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-200/80">
        <div class="p-5 border-b border-slate-100 bg-slate-50/60 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-base font-bold text-slate-800">Riwayat Pengembalian</h3>
            <form action="{{ route('petugas.pengembalian.index') }}" method="GET" class="flex w-full md:w-auto">
                <input type="hidden" name="search_aktif" value="{{ request('search_aktif') }}">
                <input type="text" name="search_riwayat" value="{{ request('search_riwayat') }}" placeholder="Cari nama peminjam/kondisi..."
                    class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-white">
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 text-sm font-semibold rounded-r-xl transition">Cari</button>
                @if(request('search_riwayat'))
                    <a href="{{ route('petugas.pengembalian.index', ['search_aktif' => request('search_aktif')]) }}"
                       class="ml-2 bg-slate-200 hover:bg-slate-300 text-slate-700 px-3.5 py-2 text-sm font-semibold rounded-xl flex items-center transition whitespace-nowrap">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Alat</th>
                        <th class="py-3 px-4 border-b">Tgl Kembali</th>
                        <th class="py-3 px-4 border-b">Kondisi</th>
                        <th class="py-3 px-4 border-b">Denda</th>
                        <th class="py-3 px-4 border-b">Diproses Oleh</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($riwayatPengembalian as $p)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 border-b font-medium">{{ $p->peminjaman->user->name ?? '-' }}</td>
                        <td class="py-3 px-4 border-b">
                            @foreach($p->peminjaman->detailPinjam as $d)
                                <span class="text-xs bg-gray-200 px-1.5 py-0.5 rounded">{{ $d->alat->nama_alat ?? '-' }}</span>
                            @endforeach
                        </td>
                        <td class="py-3 px-4 border-b">{{ $p->tgl_kembali }}</td>
                        <td class="py-3 px-4 border-b">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                @if($p->kondisi_kembali == 'Baik') bg-emerald-100 text-emerald-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $p->kondisi_kembali }}
                            </span>
                        </td>
                        <td class="py-3 px-4 border-b">{{ $p->denda > 0 ? 'Rp ' . number_format($p->denda) : '-' }}</td>
                        <td class="py-3 px-4 border-b">{{ $p->petugas->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-4 text-center text-gray-500">Belum ada riwayat pengembalian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 bg-gray-50">{{ $riwayatPengembalian->links() }}</div>
    </div>
@endsection