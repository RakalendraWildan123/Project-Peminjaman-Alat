@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Peminjaman')
@section('header-title', 'Dashboard Utama Administrator')

@section('content')
    <!-- 1. ALERT BANNER SELAMAT DATANG -->
    <div class="mb-6 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-6 rounded-2xl shadow-md border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-semibold mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Administrator Aktif</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-white">
                Selamat Datang, {{ auth()->user()->name }}! 👋
            </h2>
            <p class="text-xs sm:text-sm text-slate-300 mt-1">
                Pantau statistik operasional peminjaman alat dan rekam jejak log aktivitas sistem secara langsung.
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('admin.alat.index') }}"
               class="bg-white/10 hover:bg-white/20 text-white text-xs font-semibold px-4 py-2 rounded-xl border border-white/10 transition">
                Kelola Alat
            </a>
            <a href="{{ route('admin.user.index') }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition shadow-sm">
                Kelola User
            </a>
        </div>
    </div>

    <!-- 2. KARTU STATISTIK UTAMA (METRICS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: Total Alat -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Data Alat</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ $totalAlat }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Item alat terdaftar</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-inner">
                🛠️
            </div>
        </div>

        <!-- Card 2: Total Pengguna -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Pengguna</p>
                <p class="text-2xl font-black text-blue-600 mt-1">{{ $totalUser }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Akun di sistem</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                👥
            </div>
        </div>

        <!-- Card 3: Peminjaman Aktif -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Peminjaman Aktif</p>
                <p class="text-2xl font-black text-amber-500 mt-1">{{ $totalDipinjam }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Sedang dipinjam saat ini</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl shadow-inner">
                📦
            </div>
        </div>

        <!-- Card 4: Total Log Aktivitas -->
        <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Log Aktivitas</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $totalLogs }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Total rekam aksi</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                📋
            </div>
        </div>
    </div>

    <!-- 3. DIAGRAM LINGKARAN (PIE / DOUGHNUT CHARTS) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Diagram 1: Status Peminjaman -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                <div>
                    <h3 class="font-bold text-gray-800 text-base">Diagram Status Peminjaman</h3>
                    <p class="text-xs text-gray-400">Persentase status permohonan & transaksi peminjaman</p>
                </div>
                <span class="text-xl">📊</span>
            </div>

            <div class="relative flex items-center justify-center" style="min-height: 260px;">
                <canvas id="chartStatusPeminjaman"></canvas>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-4 mt-2 border-t border-gray-100 text-center">
                <div class="p-2 rounded-xl bg-amber-50">
                    <span class="text-[10px] font-bold text-amber-800 uppercase block">Diajukan</span>
                    <span class="text-sm font-black text-amber-600">{{ $statusPeminjamanCounts['Diajukan'] }}</span>
                </div>
                <div class="p-2 rounded-xl bg-blue-50">
                    <span class="text-[10px] font-bold text-blue-800 uppercase block">Dipinjam</span>
                    <span class="text-sm font-black text-blue-600">{{ $statusPeminjamanCounts['Dipinjam'] }}</span>
                </div>
                <div class="p-2 rounded-xl bg-emerald-50">
                    <span class="text-[10px] font-bold text-emerald-800 uppercase block">Selesai</span>
                    <span class="text-sm font-black text-emerald-600">{{ $statusPeminjamanCounts['Selesai'] }}</span>
                </div>
                <div class="p-2 rounded-xl bg-red-50">
                    <span class="text-[10px] font-bold text-red-800 uppercase block">Ditolak</span>
                    <span class="text-sm font-black text-red-600">{{ $statusPeminjamanCounts['Ditolak'] }}</span>
                </div>
            </div>
        </div>

        <!-- Diagram 2: Seberapa Banyak Orang Melakukan Peminjaman (Diagram Batang) -->
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                <div>
                    <h3 class="font-bold text-gray-800 text-base">Frekuensi Peminjaman per Pengguna</h3>
                    <p class="text-xs text-gray-400">Seberapa banyak orang / peminjam melakukan peminjaman alat</p>
                </div>
                <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full border border-indigo-100">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    {{ $totalOrangMeminjam }} Orang Pernah Meminjam
                </span>
            </div>

            <div class="relative flex items-center justify-center w-full" style="min-height: 260px;">
                <canvas id="chartPeminjam"></canvas>
            </div>

            <div class="pt-4 mt-2 border-t border-gray-100 flex flex-wrap items-center justify-between text-xs text-gray-500 gap-2">
                <span>Total Peminjam: <strong class="text-gray-800">{{ $totalOrangMeminjam }} Pengguna</strong></span>
                <span>Peminjam Terbanyak: <strong class="text-indigo-600">{{ $peminjamLabels[0] ?? '-' }} ({{ $peminjamCounts[0] ?? 0 }}x)</strong></span>
            </div>
        </div>
    </div>

    <!-- 4. TABEL LOG AKTIVITAS REAL-TIME -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-200/80">
        <div class="p-5 border-b border-gray-200 bg-gray-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <h3 class="text-base font-bold text-gray-800">Log Aktivitas Sistem Real-Time</h3>
            </div>
            <span class="text-xs text-gray-500">
                Menampilkan {{ $logs->count() }} aktivitas terakhir yang terekam
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-100/70 text-gray-600 uppercase tracking-wider font-semibold border-b border-gray-200">
                        <th class="py-3 px-4 w-40">Waktu</th>
                        <th class="py-3 px-4 w-48">Pengguna</th>
                        <th class="py-3 px-4">Deskripsi Rekam Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-semibold text-gray-800">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}
                                </div>
                                <div class="text-[10px] text-gray-400">
                                    {{ $log->created_at->diffForHumans() }}
                                </div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-700 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 leading-tight">
                                            {{ $log->user->name ?? 'Sistem' }}
                                        </div>
                                        @if(isset($log->user->role))
                                            <span class="inline-block mt-0.5 text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.2 rounded
                                                @if($log->user->role == 'admin') bg-indigo-100 text-indigo-800
                                                @elseif($log->user->role == 'petugas') bg-emerald-100 text-emerald-800
                                                @else bg-orange-100 text-orange-800 @endif">
                                                {{ $log->user->role }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-800 leading-relaxed">
                                {{ $log->aktivitas }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-400">
                                Belum ada aktivitas yang tercatat dalam log sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- PUSTAKA CHART.JS & INISIALISASI DIAGRAM LINGKARAN -->
    <script src="{{ asset('chart.umd.min.js') }}"></script>
    <script>
        if (typeof Chart === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/chart.js"><\/script>');
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data untuk Diagram Status Peminjaman
            const statusLabels = @json(array_keys($statusPeminjamanCounts));
            const statusData = @json(array_values($statusPeminjamanCounts));
            const totalStatus = statusData.reduce((a, b) => a + b, 0);

            // Inisialisasi Chart 1: Status Peminjaman (Doughnut)
            const ctxStatus = document.getElementById('chartStatusPeminjaman').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: totalStatus === 0 ? [1, 0, 0, 0] : statusData,
                        backgroundColor: [
                            '#f59e0b', // Diajukan (Amber)
                            '#3b82f6', // Dipinjam (Blue)
                            '#10b981', // Selesai (Emerald)
                            '#ef4444'  // Ditolak (Red)
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 15,
                                font: { size: 11, weight: '600', family: 'inherit' },
                                color: '#4b5563'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    if (totalStatus === 0) return ' Belum ada data';
                                    const value = context.parsed;
                                    const percent = ((value / totalStatus) * 100).toFixed(1);
                                    return ` ${context.label}: ${value} (${percent}%)`;
                                }
                            }
                        }
                    },
                    cutout: '62%'
                }
            });

            // Data untuk Diagram Batang: Frekuensi Peminjaman per Pengguna
            const peminjamLabels = @json($peminjamLabels);
            const peminjamData = @json($peminjamCounts);

            // Inisialisasi Chart 2: Peminjam (Diagram Batang / Bar Chart)
            const ctxPeminjam = document.getElementById('chartPeminjam').getContext('2d');
            new Chart(ctxPeminjam, {
                type: 'bar',
                data: {
                    labels: peminjamLabels.length === 0 ? ['Belum ada peminjam'] : peminjamLabels,
                    datasets: [{
                        label: 'Frekuensi Peminjaman',
                        data: peminjamData.length === 0 ? [0] : peminjamData,
                        backgroundColor: [
                            'rgba(99, 102, 241, 0.85)', // Indigo
                            'rgba(59, 130, 246, 0.85)', // Blue
                            'rgba(16, 185, 129, 0.85)', // Emerald
                            'rgba(245, 158, 11, 0.85)', // Amber
                            'rgba(236, 72, 153, 0.85)', // Pink
                            'rgba(14, 165, 233, 0.85)', // Sky
                            'rgba(168, 85, 247, 0.85)', // Purple
                            'rgba(20, 184, 166, 0.85)'  // Teal
                        ],
                        borderColor: [
                            '#6366f1',
                            '#3b82f6',
                            '#10b981',
                            '#f59e0b',
                            '#ec4899',
                            '#0ea5e9',
                            '#a855f7',
                            '#14b8a6'
                        ],
                        borderWidth: 1.5,
                        borderRadius: 8,
                        maxBarThickness: 42
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return ` ${context.parsed.y} kali transaksi peminjaman`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                stepSize: 1,
                                color: '#64748b',
                                font: { size: 11, family: 'inherit' }
                            },
                            grid: {
                                color: '#f1f5f9'
                            },
                            title: {
                                display: true,
                                text: 'Jumlah Transaksi',
                                color: '#94a3b8',
                                font: { size: 10, weight: '600' }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#334155',
                                font: { size: 11, weight: '600', family: 'inherit' }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
