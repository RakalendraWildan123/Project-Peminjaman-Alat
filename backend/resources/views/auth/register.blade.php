<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun Peminjam - AlatKu</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('sweetalert2.all.min.js') }}"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 sm:p-6 relative overflow-x-hidden selection:bg-orange-500 selection:text-white py-10">

    <!-- Ambient Glow Background Effects -->
    <div class="fixed top-0 right-1/4 w-96 h-96 bg-orange-600/15 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
    <div class="fixed bottom-0 left-1/4 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Main Container -->
    <div class="w-full max-w-4xl bg-slate-900/90 border border-slate-800 rounded-3xl shadow-2xl overflow-hidden backdrop-blur-xl z-10 grid grid-cols-1 lg:grid-cols-12">
        
        <!-- KOLOM KIRI: BRANDING & SYARAT PEMINJAM -->
        <div class="lg:col-span-5 p-6 sm:p-8 bg-gradient-to-br from-orange-950/70 via-slate-900 to-slate-950 flex flex-col justify-between border-b lg:border-b-0 lg:border-r border-slate-800/80">
            <div>
                <!-- Brand Header -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-500 flex items-center justify-center shadow-lg shadow-orange-500/30 font-extrabold text-white text-lg">
                        ⚡
                    </div>
                    <div>
                        <h1 class="text-xl font-extrabold tracking-tight text-white leading-none">Alat<span class="text-orange-400">Ku</span></h1>
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-semibold mt-1">Portal Peminjam Alat</p>
                    </div>
                </div>

                <!-- Headline Hero -->
                <h2 class="text-lg sm:text-xl font-bold text-white leading-snug mb-3">
                    Daftar Akun Mandiri untuk Meminjam Alat Praktikum.
                </h2>
                <p class="text-xs text-slate-400 leading-relaxed mb-6">
                    Lengkapi formulir pendaftaran akun untuk mulai menjelajah katalog unit, melihat spesifikasi stok, dan mengajukan peminjaman alat secara online.
                </p>

                <!-- Panduan Singkat -->
                <div class="space-y-3 mb-6">
                    <div class="flex items-start gap-2.5 text-xs text-slate-300">
                        <span class="w-5 h-5 rounded-lg bg-orange-500/10 text-orange-400 flex items-center justify-center shrink-0 font-bold">1</span>
                        <span>Isi data diri asli (nama lengkap & nomor kontak aktif).</span>
                    </div>
                    <div class="flex items-start gap-2.5 text-xs text-slate-300">
                        <span class="w-5 h-5 rounded-lg bg-orange-500/10 text-orange-400 flex items-center justify-center shrink-0 font-bold">2</span>
                        <span>Akun otomatis aktif seketika setelah formulir disubmit.</span>
                    </div>
                    <div class="flex items-start gap-2.5 text-xs text-slate-300">
                        <span class="w-5 h-5 rounded-lg bg-orange-500/10 text-orange-400 flex items-center justify-center shrink-0 font-bold">3</span>
                        <span>Dapat langsung login dan mengajukan alat di katalog.</span>
                    </div>
                </div>
            </div>

            <!-- Role Notification Card -->
            <div class="mt-4 p-4 rounded-2xl bg-orange-950/40 border border-orange-500/20 text-xs text-slate-300">
                <div class="flex items-center gap-2 font-bold text-orange-300 mb-1">
                    <span>👤</span>
                    <span>Hak Akses: Peminjam</span>
                </div>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Pendaftaran formulir ini ditujukan khusus untuk pengguna peminjam alat (mahasiswa / siswa).
                </p>
            </div>
        </div>

        <!-- KOLOM KANAN: FORMULIR REGISTRASI -->
        <div class="lg:col-span-7 p-6 sm:p-10 bg-white text-slate-800 flex flex-col justify-center">
            
            <div class="mb-6">
                <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Buat Akun Baru</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Masukkan informasi data diri Anda secara lengkap dan benar.</p>
            </div>

            <!-- Flash Error Alert -->
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

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Input Nama Lengkap -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                                   placeholder="Contoh: Rian Setiawan"
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition">
                        </div>
                    </div>

                    <!-- Input Email -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                            Alamat Email <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   placeholder="rian@email.com"
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Input Password -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                            Kata Sandi <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input type="password" name="password" id="register-password" required
                                   placeholder="Minimal 6 karakter"
                                   class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition">
                            <button type="button" onclick="toggleRegisterPassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer">
                                <svg id="register-eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Input Nomor HP / WA -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                            Nomor WhatsApp / HP
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                                   placeholder="Contoh: 081234567890"
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition">
                        </div>
                    </div>
                </div>

                <!-- Input Alamat -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                        Alamat Lengkap
                    </label>
                    <div class="relative">
                        <div class="absolute top-3 left-3.5 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <textarea name="alamat" rows="2"
                                  placeholder="Alamat domisili / kelas / instansi..."
                                  class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition">{{ old('alamat') }}</textarea>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2">
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-orange-500 via-amber-500 to-orange-600 hover:from-orange-600 hover:to-amber-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-orange-500/25 transition-all hover:scale-[1.01] active:scale-[0.99] cursor-pointer text-xs sm:text-sm">
                        <span>Daftar Akun Baru</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Tautan Kembali ke Login -->
            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">
                    Sudah memiliki akun terdaftar?
                    <a href="{{ route('login') }}" class="font-bold text-orange-600 hover:text-orange-800 transition hover:underline ml-1">
                        Masuk di Sini &rarr;
                    </a>
                </p>
            </div>

        </div>

    </div>

    <!-- Script Toggle Password Register -->
    <script>
        function toggleRegisterPassword() {
            const passInput = document.getElementById('register-password');
            const eyeIcon = document.getElementById('register-eye-icon');
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
