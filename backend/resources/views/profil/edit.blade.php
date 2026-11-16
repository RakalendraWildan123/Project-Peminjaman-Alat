@extends(auth()->user()->role === 'peminjam' ? 'layouts.peminjam' : 'layouts.app')

@php
    $role = auth()->user()->role;
    $isAdmin = $role === 'admin';
    $isPetugas = $role === 'petugas';
    $isPeminjam = $role === 'peminjam';

    $dashboardRoute = $isPeminjam ? route('peminjam.dashboard') : ($isAdmin ? route('admin.dashboard') : route('petugas.dashboard'));

    $themeGrad = $isAdmin
        ? 'from-indigo-600 to-violet-600'
        : ($isPetugas ? 'from-emerald-600 to-teal-600' : 'from-orange-500 to-amber-600');

    $themeBadge = $isAdmin
        ? 'bg-indigo-50 text-indigo-700 border-indigo-200'
        : ($isPetugas ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-orange-50 text-orange-700 border-orange-200');

    $roleLabel = $isAdmin ? 'Administrator' : ($isPetugas ? 'Petugas Operasional' : 'Peminjam Alat');
    $roleIcon = $isAdmin ? '🛡️' : ($isPetugas ? '⚡' : '👤');
@endphp

@section('title', 'Profil Saya & Pengaturan Akun')
@section('header-title', 'Profil Saya')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Navigasi Balik -->
    <div class="flex items-center justify-between">
        <a href="{{ $dashboardRoute }}"
           class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-500 hover:text-slate-800 transition bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-xs hover:border-slate-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Dashboard</span>
        </a>

        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold {{ $themeBadge }} border shadow-xs">
            <span>{{ $roleIcon }}</span>
            <span>{{ $roleLabel }}</span>
        </div>
    </div>

    <!-- Alert Sukses Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl shadow-xs text-sm flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0 font-bold">
                ✓
            </div>
            <div>
                <p class="font-semibold">Berhasil!</p>
                <p class="text-xs text-emerald-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- KOLOM KIRI: RINGKASAN AKUN & TOMBOL LOGOUT (HERO) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Card Informasi Pengguna -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 text-center">
                <div class="relative inline-block mb-4">
                    @if($user->foto)
                        <img id="avatar-preview" src="{{ asset($user->foto) }}" alt="Foto Profil"
                             class="w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover border-4 border-slate-100 shadow-md mx-auto">
                    @else
                        <div id="avatar-preview-fallback"
                             class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-gradient-to-tr {{ $themeGrad }} text-white text-3xl font-extrabold flex items-center justify-center shadow-md mx-auto border-4 border-slate-100">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="absolute bottom-1 right-1 w-6 h-6 bg-emerald-500 border-2 border-white rounded-full flex items-center justify-center text-[10px] text-white font-bold" title="Status: Aktif">
                        ✓
                    </div>
                </div>

                <h2 class="text-lg font-bold text-slate-800">{{ $user->name }}</h2>
                <p class="text-xs text-slate-500 mb-3">{{ $user->email }}</p>

                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $themeBadge }} border">
                    <span>{{ $roleIcon }}</span>
                    <span>{{ $roleLabel }}</span>
                </div>

                <div class="mt-6 pt-5 border-t border-slate-100 text-left text-xs space-y-2.5 text-slate-600">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Nomor HP:</span>
                        <span class="font-medium text-slate-700">{{ $user->no_hp ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Bergabung:</span>
                        <span class="font-medium text-slate-700">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Status Sesi:</span>
                        <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Terhubung
                        </span>
                    </div>
                </div>
            </div>

            <!-- CARD SESI & TOMBOL LOGOUT (DIPERMINTAAN USER: LOGOUT DALAM PROFIL) -->
            <div class="bg-gradient-to-b from-red-50/60 to-white rounded-2xl border border-red-200/80 shadow-xs p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-lg font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Sesi Akun & Logout</h3>
                        <p class="text-[11px] text-slate-500">Keluar dari akun ini dengan aman</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 mb-4 leading-relaxed">
                    Selesai menggunakan sistem inventaris? Anda dapat keluar untuk mengamankan data dan mencegah akses oleh orang lain pada perangkat ini.
                </p>

                <!-- Tombol Logout dengan SweetAlert2 Confirmation -->
                <form action="{{ route('logout') }}" method="POST"
                      data-confirm="Apakah Anda yakin ingin keluar dari sistem inventaris?"
                      data-confirm-title="Keluar dari Akun?"
                      data-confirm-icon="warning"
                      data-confirm-btn="Ya, Logout Sekarang"
                      data-confirm-color="#dc2626">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow-md shadow-red-500/20 transition-all hover:scale-[1.01] active:scale-[0.99] cursor-pointer text-xs sm:text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Keluar dari Akun (Logout)</span>
                    </button>
                </form>

                <p class="text-[11px] text-slate-400 text-center mt-3">
                    🔒 Data dan aktivitas Anda tetap tersimpan dengan aman.
                </p>
            </div>

            <!-- CARD DOKUMENTASI PROYEK & UNDUH PDF -->
            <div class="bg-gradient-to-b from-blue-50/60 to-white rounded-2xl border border-blue-200/80 shadow-xs p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Dokumentasi Proyek (PDF)</h3>
                        <p class="text-[11px] text-slate-500">Panduan lengkap sistem, fitur & skema</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 mb-4 leading-relaxed">
                    Unduh berkas PDF resmi penjelasan proyek untuk presentasi, laporan, atau pengujian sistem inventaris.
                </p>

                <div class="space-y-2">
                    <a href="{{ route('dokumentasi.download') }}"
                       class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow-md shadow-blue-500/20 transition-all hover:scale-[1.01] active:scale-[0.99] text-xs sm:text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span>Unduh PDF Langsung</span>
                    </a>

                    <a href="{{ asset('Penjelasan_Proyek_Peminjaman_Alat.pdf') }}" target="_blank"
                       class="w-full flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold py-2 px-4 rounded-xl border border-slate-200 transition text-xs">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        <span>Buka di Google Chrome (Preview)</span>
                    </a>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN: FORMULIR EDIT PROFIL & KATA SANDI -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
                
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="text-base sm:text-lg font-bold text-slate-800">Perbarui Informasi Profil</h2>
                    <p class="text-xs sm:text-sm text-slate-500">Kelola informasi data diri, foto profil, dan kata sandi akun Anda.</p>
                </div>

                <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Upload Foto Profil -->
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-2">
                            Ganti Foto Profil
                        </label>
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                @if($user->foto)
                                    <img id="form-avatar-thumb" src="{{ asset($user->foto) }}" alt="Preview" class="w-14 h-14 rounded-full object-cover border border-slate-200 shadow-xs">
                                @else
                                    <div id="form-avatar-thumb-fallback" class="w-14 h-14 rounded-full bg-slate-100 border border-slate-200 text-slate-500 font-bold flex items-center justify-center text-lg">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="foto" id="foto-input" accept="image/*"
                                    class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                                <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Maksimal ukuran 2MB.</p>
                                @error('foto') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama Lengkap -->
                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            </div>
                            @error('name') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                                Alamat Email <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                </div>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            </div>
                            @error('email') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Nomor HP -->
                    <div>
                        <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                            Nomor WhatsApp / HP
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" placeholder="Contoh: 081234567890"
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                        </div>
                        @error('no_hp') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- PEMISAH: GANTI KATA SANDI -->
                    <div class="pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800">Keamanan & Ubah Password</h3>
                            <span class="text-[11px] text-slate-400 font-normal">(Kosongkan bila tidak ingin diganti)</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Password Baru -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                    Password Baru
                                </label>
                                <input type="password" name="password" placeholder="Minimal 6 karakter"
                                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                @error('password') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            <!-- Konfirmasi Password -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                    Konfirmasi Password Baru
                                </label>
                                <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Simpan Perubahan -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-gradient-to-r {{ $themeGrad }} hover:opacity-95 text-white font-semibold px-6 py-2.5 rounded-xl shadow-md transition-all hover:scale-[1.01] active:scale-[0.99] cursor-pointer text-xs sm:text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

<!-- Script Instant Preview Foto Profil -->
<script>
    document.getElementById('foto-input')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const preview = document.getElementById('avatar-preview');
                const fallback = document.getElementById('avatar-preview-fallback');
                const thumb = document.getElementById('form-avatar-thumb');
                const thumbFallback = document.getElementById('form-avatar-thumb-fallback');

                if (preview) {
                    preview.src = evt.target.result;
                } else if (fallback) {
                    fallback.outerHTML = `<img id="avatar-preview" src="${evt.target.result}" alt="Foto Profil" class="w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover border-4 border-slate-100 shadow-md mx-auto">`;
                }

                if (thumb) {
                    thumb.src = evt.target.result;
                } else if (thumbFallback) {
                    thumbFallback.outerHTML = `<img id="form-avatar-thumb" src="${evt.target.result}" alt="Preview" class="w-14 h-14 rounded-full object-cover border border-slate-200 shadow-xs">`;
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
