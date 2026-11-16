<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Akun - AlatKu</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('sweetalert2.all.min.js') }}"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 sm:p-6 relative overflow-x-hidden selection:bg-indigo-500 selection:text-white">

    <!-- Ambient Glow Background Effects -->
    <div class="fixed top-0 left-1/4 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
    <div class="fixed bottom-0 right-1/4 w-96 h-96 bg-violet-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Main Container -->
    <div class="w-full max-w-4xl bg-slate-900/90 border border-slate-800 rounded-3xl shadow-2xl overflow-hidden backdrop-blur-xl z-10 grid grid-cols-1 lg:grid-cols-12">
        
        <!-- KOLOM KIRI: BRANDING, FITUR & SHORTCUT PRESENTASI -->
        <div class="lg:col-span-5 p-6 sm:p-8 bg-gradient-to-br from-indigo-950/80 via-slate-900 to-slate-950 flex flex-col justify-between border-b lg:border-b-0 lg:border-r border-slate-800/80 relative">
            <div>
                <!-- Brand Header -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/30 font-extrabold text-white text-lg">
                        ⚡
                    </div>
                    <div>
                        <h1 class="text-xl font-extrabold tracking-tight text-white leading-none">Alat<span class="text-indigo-400">Ku</span></h1>
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-semibold mt-1">Sistem Peminjaman Alat</p>
                    </div>
                </div>

                <!-- Headline Hero -->
                <h2 class="text-lg sm:text-xl font-bold text-white leading-snug mb-3">
                    Sirkulasi Alat Praktikum Jadi Tertib, Cepat, & Terpantau.
                </h2>
                <p class="text-xs text-slate-400 leading-relaxed mb-6">
                    Platform cerdas untuk mengelola inventaris, verifikasi peminjaman, serta pemantauan pengembalian dan sanksi denda secara akurat.
                </p>

                <!-- Fitur Utama -->
                <div class="space-y-3 mb-6">
                    <div class="flex items-start gap-2.5 text-xs text-slate-300">
                        <span class="w-5 h-5 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center shrink-0 text-sm">📦</span>
                        <span>Katalog alat real-time dengan spesifikasi & stok akurat.</span>
                    </div>
                    <div class="flex items-start gap-2.5 text-xs text-slate-300">
                        <span class="w-5 h-5 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center shrink-0 text-sm">⚡</span>
                        <span>Persetujuan terverifikasi oleh petugas laboratorium.</span>
                    </div>
                    <div class="flex items-start gap-2.5 text-xs text-slate-300">
                        <span class="w-5 h-5 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center shrink-0 text-sm">🛡️</span>
                        <span>Audit trail log aktivitas & diagram statistik interaktif.</span>
                    </div>
                </div>
            </div>

            <!-- SHORTCUT LOGIN DEMO (MEMPERCEPAT PRESENTASI PENGUJI/GURU) -->
            <div class="mt-4 pt-4 border-t border-slate-800/80">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider">⚡ Akun Demo Pengujian:</span>
                    <span class="text-[10px] text-indigo-400">Klik untuk isi otomatis</span>
                </div>
                <div class="grid grid-cols-3 gap-1.5">
                    <button type="button" onclick="fillDemo('admin@gmail.com', 'password123')"
                            class="px-2 py-1.5 rounded-xl bg-indigo-950/60 hover:bg-indigo-600/30 text-indigo-300 hover:text-white border border-indigo-500/30 text-[11px] font-semibold transition text-center cursor-pointer">
                        🛡️ Admin
                    </button>
                    <button type="button" onclick="fillDemo('petugas@gmail.com', 'password123')"
                            class="px-2 py-1.5 rounded-xl bg-emerald-950/60 hover:bg-emerald-600/30 text-emerald-300 hover:text-white border border-emerald-500/30 text-[11px] font-semibold transition text-center cursor-pointer">
                        ⚡ Petugas
                    </button>
                    <button type="button" onclick="fillDemo('rian@gmail.com', 'password123')"
                            class="px-2 py-1.5 rounded-xl bg-orange-950/60 hover:bg-orange-600/30 text-orange-300 hover:text-white border border-orange-500/30 text-[11px] font-semibold transition text-center cursor-pointer">
                        👤 Peminjam
                    </button>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: FORMULIR LOGIN -->
        <div class="lg:col-span-7 p-6 sm:p-10 bg-white text-slate-800 flex flex-col justify-center">
            
            <div class="mb-6">
                <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Selamat Datang</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Masukkan kredensial akun Anda untuk mengakses sistem inventaris.</p>
            </div>

            <!-- Flash Error / Success Alert -->
            @if(session('success'))
                <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs flex items-center gap-2.5 shadow-xs">
                    <span class="text-base text-emerald-600 font-bold">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 p-3.5 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-xs flex items-center gap-2.5 shadow-xs">
                    <span class="text-base text-red-600 font-bold">✕</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3.5 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-xs shadow-xs">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="flex items-center gap-2">
                                <span class="text-red-500">•</span>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Input Email -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input type="email" name="email" id="input-email" value="{{ old('email') }}" required autofocus
                               placeholder="nama@email.com"
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition">
                    </div>
                </div>

                <!-- Input Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Kata Sandi
                        </label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input type="password" name="password" id="input-password" required
                               placeholder="••••••••"
                               class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition">
                        
                        <!-- Toggle Password Visibility -->
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer">
                            <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2">
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 hover:from-indigo-700 hover:to-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-indigo-500/25 transition-all hover:scale-[1.01] active:scale-[0.99] cursor-pointer text-xs sm:text-sm">
                        <span>Masuk ke Sistem</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Tautan Registrasi -->
            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">
                    Belum memiliki akun peminjam?
                    <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:text-indigo-800 transition hover:underline ml-1">
                        Daftar Akun Baru &rarr;
                    </a>
                </p>
            </div>

        </div>

    </div>

    <!-- Script Autofill Demo & Toggle Password -->
    <script>
        function fillDemo(email, password) {
            const emailInput = document.getElementById('input-email');
            const passInput = document.getElementById('input-password');
            if (emailInput && passInput) {
                emailInput.value = email;
                passInput.value = password;
                emailInput.classList.add('ring-2', 'ring-indigo-500');
                passInput.classList.add('ring-2', 'ring-indigo-500');
                setTimeout(() => {
                    emailInput.classList.remove('ring-2', 'ring-indigo-500');
                    passInput.classList.remove('ring-2', 'ring-indigo-500');
                }, 800);
            }
        }

        function togglePassword() {
            const passInput = document.getElementById('input-password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passInput) {
                if (passInput.type === 'password') {
                    passInput.type = 'text';
                    eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>`;
                } else {
                    passInput.type = 'password';
                    eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>`;
                }
            }
        }
    </script>
</body>
</html>
