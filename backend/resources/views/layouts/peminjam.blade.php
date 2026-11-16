<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Peminjam - AlatKu')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
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
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">

    <div class="flex h-screen overflow-hidden">

        <!-- OVERLAY MOBILE -->
        <div id="sidebar-overlay" onclick="toggleSidebar()"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 hidden md:hidden transition-opacity duration-300">
        </div>

        <!-- SIDEBAR PEMINJAM -->
        <aside id="sidebar-peminjam"
               class="fixed md:static inset-y-0 left-0 z-50 w-64 lg:w-72 bg-gradient-to-b from-slate-950 via-slate-900 to-indigo-950 text-white flex flex-col justify-between shadow-xl md:shadow-none -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out border-r border-slate-800/80">

            <!-- BAGIAN ATAS: LOGO & USER PROFILE -->
            <div class="flex flex-col">
                <!-- Brand Header -->
                <div class="p-5 flex items-center justify-between border-b border-slate-800/80">
                    <a href="{{ route('peminjam.dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 flex items-center justify-center text-white shadow-lg shadow-orange-500/25 font-extrabold text-xl group-hover:scale-105 transition-transform">
                            📦
                        </div>
                        <div>
                            <span class="text-lg font-bold tracking-tight text-white block leading-tight">AlatKu</span>
                            <span class="inline-block bg-orange-500/20 text-orange-400 border border-orange-500/30 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                                Portal Peminjam
                            </span>
                        </div>
                    </a>
                    <!-- Tombol Tutup Sidebar di Mobile -->
                    <button type="button" onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-white p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Kartu Pengguna di Sidebar -->
                <div class="mx-4 mt-4 p-3 bg-white/5 border border-white/10 rounded-2xl flex items-center gap-3 backdrop-blur-sm">
                    @if(auth()->user()->foto)
                        <img src="{{ asset(auth()->user()->foto) }}" alt="Foto Profil"
                             class="w-10 h-10 rounded-full object-cover border-2 border-orange-500/60 shadow-sm shrink-0">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-orange-500 to-amber-500 flex items-center justify-center text-white text-sm font-bold shadow-sm shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="overflow-hidden flex-1">
                        <div class="text-white font-semibold text-sm truncate leading-tight">{{ auth()->user()->name }}</div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-[11px] text-slate-300 font-medium">Peminjam Aktif</span>
                        </div>
                    </div>
                </div>

                <!-- MENU NAVIGASI -->
                @php
                    $activeLoansCount = \App\Models\Peminjaman::where('user_id', auth()->id())->where('status', 'dipinjam')->count();
                @endphp
                <nav class="p-4 space-y-1.5">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3 pb-1 pt-2">
                        Menu Utama
                    </div>

                    <!-- 1. Dashboard -->
                    <a href="{{ route('peminjam.dashboard') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('peminjam.dashboard') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-md shadow-orange-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('peminjam.dashboard') ? 'text-white' : 'text-orange-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- 2. Katalog Alat -->
                    <a href="{{ route('peminjam.katalog') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('peminjam.katalog*') || request()->routeIs('peminjam.alat.show*') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-md shadow-orange-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('peminjam.katalog*') || request()->routeIs('peminjam.alat.show*') ? 'text-white' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span>Katalog Alat</span>
                    </a>

                    <!-- 3. Pesanan Saya -->
                    <a href="{{ route('peminjam.riwayat') }}"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('peminjam.riwayat*') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-md shadow-orange-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 {{ request()->routeIs('peminjam.riwayat*') ? 'text-white' : 'text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <span>Pesanan Saya</span>
                        </div>
                        @if($activeLoansCount > 0)
                            <span class="bg-orange-500 text-white text-[11px] font-bold px-2 py-0.5 rounded-full shadow-sm">
                                {{ $activeLoansCount }} aktif
                            </span>
                        @endif
                    </a>

                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3 pb-1 pt-4">
                        Pengaturan
                    </div>

                    <!-- 4. Profil Saya -->
                    <a href="{{ route('profil.edit') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('profil*') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-md shadow-orange-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('profil*') ? 'text-white' : 'text-sky-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Profil Saya</span>
                    </a>
                </nav>
            </div>

            <!-- BAGIAN BAWAH: WIDGET TIPS -->
            <div class="p-4 border-t border-slate-800/80">
                <!-- Info Box Card -->
                <div class="p-3 bg-indigo-950/40 border border-indigo-500/20 rounded-xl text-[11px] text-slate-300">
                    <div class="flex items-center gap-1.5 font-semibold text-amber-300 mb-1">
                        <span>💡</span>
                        <span>Tips Peminjaman</span>
                    </div>
                    <p class="leading-relaxed text-slate-400">
                        Kembalikan alat tepat waktu untuk menjaga status akun tetap baik dan bebas denda.
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
                            @yield('header-title', 'Portal Peminjam')
                        </h1>
                        <p class="text-[11px] text-slate-500 hidden sm:block">
                            Sistem Peminjaman & Pengembalian Alat
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if(!request()->routeIs('peminjam.katalog*'))
                        <a href="{{ route('peminjam.katalog') }}"
                           class="hidden sm:inline-flex items-center gap-1.5 bg-orange-50 hover:bg-orange-100 text-orange-600 border border-orange-200 text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                            <span>🔍</span>
                            <span>Cari Alat</span>
                        </a>
                    @endif

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
                            <div class="w-6 h-6 rounded-full bg-orange-500 text-white text-[10px] font-bold flex items-center justify-center">
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
            const sidebar = document.getElementById('sidebar-peminjam');
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
