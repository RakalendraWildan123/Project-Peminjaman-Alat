<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Peminjaman Alat</title>
    <!-- Memuat Tailwind CSS melalui CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen py-8">

    <!-- Card Container -->
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md mx-4">
        <h3 class="text-2xl font-bold text-center text-gray-800 mb-6">Register Sistem</h3>

        <!-- Alert Error Session -->
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Alert Error Validasi -->
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                <ul class="list-disc pl-5 mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <!-- Input Nama -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <!-- Input Email -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Input Password -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Role / Hak Akses</label>
                <select name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="peminjam" selected>Peminjam</option>
                </select>
            </div>
            <!-- NO HP-->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">No. HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <!--Alamat-->
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Alamat</label>
                <textarea name="alamat" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('alamat') }}</textarea>
            </div>

            <!-- Tombol Submit -->
            <button type="submit"
                class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                Daftar Akun
            </button>
            <p class="text-center text-sm text-gray-600 mt-4">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Login di sini</a>
            </p>
        </form>
    </div>

</body>
</html>