<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - AlatKu')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        admin: {
                            50: '#eef2ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        },
                        petugas: {
                            50: '#ecfdf5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom scrollbar halus */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        @media print {
            .no-print { display: none !important; }
            aside, header { display: none !important; }
            main { padding: 0 !important; }
            .print-header { display: block !important; }
        }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">

    <div class="flex h-screen overflow-hidden">

        <!-- OVERLAY MOBILE -->
        <div id="sidebar-overlay" onclick="toggleSidebar()"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 hidden md:hidden transition-opacity duration-300">
        </div>

        @php
            $role = auth()->user()->role;
            $isAdmin = $role === 'admin';
            $isPetugas = $role === 'petugas';
            $pendingLoansCount = \App\Models\Peminjaman::where('status', 'diajukan')->count();
        @endphp

        <!-- SIDEBAR ADMIN / PETUGAS -->
        <aside id="sidebar-app"
               class="fixed md:static inset-y-0 left-0 z-50 w-64 lg:w-72 {{ $isAdmin ? 'bg-gradient-to-b from-slate-950 via-slate-900 to-indigo-950' : 'bg-gradient-to-b from-slate-950 via-slate-900 to-teal-950' }} text-white flex flex-col justify-between shadow-xl md:shadow-none -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out border-r border-slate-800/80">

            <!-- BAGIAN ATAS: BRAND LOGO & USER PROFILE -->
            <div class="flex flex-col">
                <!-- Brand Header -->
                <div class="p-5 flex items-center justify-between border-b border-slate-800/80">
                    <a href="{{ $isAdmin ? route('admin.dashboard') : route('petugas.dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl {{ $isAdmin ? 'bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500 shadow-indigo-500/25' : 'bg-gradient-to-tr from-emerald-500 via-teal-500 to-cyan-400 shadow-emerald-500/25' }} flex items-center justify-center text-white shadow-lg font-extrabold text-xl group-hover:scale-105 transition-transform">
                            {{ $isAdmin ? '🛡️' : '⚡' }}
                        </div>
                        <div>
                            <span class="text-lg font-bold tracking-tight text-white block leading-tight">AlatKu</span>
                            <span class="inline-block {{ $isAdmin ? 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30' : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' }} border text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                                {{ $isAdmin ? 'Panel Admin' : 'Panel Petugas' }}
                            </span>
                        </div>
                    </a>
                    <!-- Tombol Tutup Sidebar Mobile -->
                    <button type="button" onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-white p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Kartu Pengguna di Sidebar -->
                <div class="mx-4 mt-4 p-3 bg-white/5 border border-white/10 rounded-2xl flex items-center gap-3 backdrop-blur-sm">
                    @if(auth()->user()->foto)
                        <img src="{{ asset(auth()->user()->foto) }}" alt="Foto"
                             class="w-10 h-10 rounded-full object-cover border-2 {{ $isAdmin ? 'border-indigo-500/60' : 'border-emerald-500/60' }} shadow-sm shrink-0">
                    @else
                        <div class="w-10 h-10 rounded-full {{ $isAdmin ? 'bg-gradient-to-tr from-indigo-600 to-violet-600' : 'bg-gradient-to-tr from-emerald-600 to-teal-600' }} flex items-center justify-center text-white text-sm font-bold shadow-sm shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="overflow-hidden flex-1">
                        <div class="text-white font-semibold text-sm truncate leading-tight">{{ auth()->user()->name }}</div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-[11px] text-slate-300 font-medium capitalize">{{ $role }} Aktif</span>
                        </div>
                    </div>
                </div>

                <!-- NAVIGASI MENU -->
                <nav class="p-4 space-y-1.5 overflow-y-auto max-h-[calc(100vh-270px)]">

                    {{-- ================= MENU KHUSUS ADMIN ================= --}}
                    @if($isAdmin)
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3 pb-1 pt-2">
                            Navigasi Utama
                        </div>

                        <!-- Dashboard Admin -->
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span>Dashboard Utama</span>
                        </a>

                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3 pb-1 pt-3">
                            Master Data
                        </div>

                        <!-- Kelola Alat -->
                        <a href="{{ route('admin.alat.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.alat*') ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.alat*') ? 'text-white' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span>Kelola Alat</span>
                        </a>

                        <!-- Kelola Kategori -->
                        <a href="{{ route('admin.kategori.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.kategori*') ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.kategori*') ? 'text-white' : 'text-pink-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <span>Kelola Kategori</span>
                        </a>

                        <!-- Kelola User -->
                        <a href="{{ route('admin.user.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.user*') ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.user*') ? 'text-white' : 'text-cyan-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span>Kelola Pengguna</span>
                        </a>

                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3 pb-1 pt-3">
                            Transaksi & Sirkulasi
                        </div>

                        <!-- Kelola Peminjaman -->
                        <a href="{{ route('admin.peminjaman.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.peminjaman*') ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.peminjaman*') ? 'text-white' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span>Kelola Peminjaman</span>
                        </a>

                        <!-- Kelola Pengembalian -->
                        <a href="{{ route('admin.pengembalian.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.pengembalian*') ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('admin.pengembalian*') ? 'text-white' : 'text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Kelola Pengembalian</span>
                        </a>
                    @endif

                    {{-- ================= MENU KHUSUS PETUGAS ================= --}}
                    @if($isPetugas)
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3 pb-1 pt-2">
                            Operasional Petugas
                        </div>

                        <!-- Dashboard Petugas -->
                        <a href="{{ route('petugas.dashboard') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('petugas.dashboard') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('petugas.dashboard') ? 'text-white' : 'text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span>Dashboard Petugas</span>
                        </a>

                        <!-- Persetujuan Peminjaman -->
                        <a href="{{ route('petugas.peminjaman.index') }}"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('petugas.peminjaman*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 {{ request()->routeIs('petugas.peminjaman*') ? 'text-white' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Persetujuan Pinjam</span>
                            </div>
                            @if($pendingLoansCount > 0)
                                <span class="bg-amber-500 text-white text-[11px] font-bold px-2 py-0.5 rounded-full shadow-sm animate-pulse">
                                    {{ $pendingLoansCount }}
                                </span>
                            @endif
                        </a>

                        <!-- Pemantauan Pengembalian -->
                        <a href="{{ route('petugas.pengembalian.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('petugas.pengembalian*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('petugas.pengembalian*') ? 'text-white' : 'text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span>Pantau Pengembalian</span>
                        </a>

                        <!-- Cetak Laporan -->
                        <a href="{{ route('petugas.laporan.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('petugas.laporan*') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('petugas.laporan*') ? 'text-white' : 'text-purple-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            <span>Cetak Laporan</span>
                        </a>
                    @endif

                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3 pb-1 pt-3">
                        Pengaturan
                    </div>

                    <!-- Profil Saya -->
                    <a href="{{ route('profil.edit') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('profil*') ? ($isAdmin ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-500/20 font-semibold' : 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-500/20 font-semibold') : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('profil*') ? 'text-white' : 'text-sky-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Profil Saya</span>
                    </a>
                </nav>
            </div>

            <!-- BAGIAN BAWAH: WIDGET INFORMASI -->
            <div class="p-4 border-t border-slate-800/80">
                <!-- Info Status Card -->
                <div class="p-3 {{ $isAdmin ? 'bg-indigo-950/40 border-indigo-500/20' : 'bg-teal-950/40 border-teal-500/20' }} border rounded-xl text-[11px] text-slate-300">
                    <div class="flex items-center gap-1.5 font-semibold {{ $isAdmin ? 'text-indigo-300' : 'text-emerald-300' }} mb-1">
                        <span>{{ $isAdmin ? '🛡️' : '⚡' }}</span>
                        <span>{{ $isAdmin ? 'Sistem Inventaris' : 'Tips Petugas' }}</span>
                    </div>
                    <p class="leading-relaxed text-slate-400">
                        {{ $isAdmin ? 'Pencatatan log aktivitas sistem aktif dan terverifikasi secara real-time.' : 'Pastikan fisik dan kelengkapan unit alat selalu dicek saat pengembalian.' }}
                    </p>
                </div>
            </div>
        </aside>

        <!-- CONTAINER KONTEN UTAMA -->
        <div class="flex-1 flex flex-col overflow-y-auto">

            <!-- NAVBAR ATAS -->
            <header class="bg-white border-b border-slate-200/80 h-16 flex items-center justify-between px-4 sm:px-6 z-10 sticky top-0 shadow-[0_1px_3px_0_rgba(0,0,0,0.02)]">
                <div class="flex items-center gap-3">
                    <!-- Tombol Hamburger Mobile -->
                    <button type="button" onclick="toggleSidebar()" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <div>
                        <h1 class="text-base sm:text-lg font-bold text-slate-800 leading-tight">
                            @yield('header-title', 'Dashboard')
                        </h1>
                        <p class="text-[11px] text-slate-500 hidden sm:block">
                            {{ $isAdmin ? 'Panel Kontrol Manajemen Inventaris & Peminjaman' : 'Panel Verifikasi Operasional Peminjaman' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Role Pill Badge -->
                    <span class="hidden md:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $isAdmin ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                        <span>{{ $isAdmin ? '🛡️' : '⚡' }}</span>
                        <span class="uppercase tracking-wider text-[10px] font-bold">{{ $role }}</span>
                    </span>

                    <!-- Tombol Unduh PDF Proyek -->
                    <a href="{{ route('dokumentasi.download') }}" title="Unduh Penjelasan Proyek (PDF)"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white border border-blue-200 hover:border-blue-600 transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">Unduh</span>
                        <span>PDF</span>
                    </a>

                    <!-- User Pill Badge (Menuju Profil Saya & Manajemen Sesi) -->
                    <a href="{{ route('profil.edit') }}" title="Profil Saya & Pengaturan Sesi" class="flex items-center gap-2 p-1.5 pr-3 bg-slate-100 hover:bg-slate-200/80 rounded-full transition text-xs font-medium text-slate-700 border border-slate-200/70 hover:border-slate-300">
                        @if(auth()->user()->foto)
                            <img src="{{ asset(auth()->user()->foto) }}" alt="Foto" class="w-6 h-6 rounded-full object-cover">
                        @else
                            <div class="w-6 h-6 rounded-full {{ $isAdmin ? 'bg-indigo-600' : 'bg-emerald-600' }} text-white text-[10px] font-bold flex items-center justify-center">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="max-w-[120px] truncate hidden md:inline">{{ auth()->user()->name }}</span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </header>

            <!-- KONTEN UTAMA HALAMAN -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @if(session('success'))
                    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl shadow-sm text-sm flex items-center gap-2">
                        <span class="text-base">✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl shadow-sm text-sm flex items-center gap-2">
                        <span class="text-base">✕</span>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>

        </div>

    </div>

    <!-- Toggle Script Mobile Sidebar -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar-app');
            const overlay = document.getElementById('sidebar-overlay');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }
    </script>

    @include('layouts.sweetalert')
</body>
</html>
