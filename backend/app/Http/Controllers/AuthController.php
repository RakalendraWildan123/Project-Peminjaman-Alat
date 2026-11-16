<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Menampilkan Form Register
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Memproses Registrasi
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'peminjam',
            'no_hp' => $validated['no_hp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan masuk dengan akun Anda.');
    }

    // Memproses Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Catat log aktivitas login
            LogAktivitas::create([
                'user_id' => $user->id,
                'aktivitas' => "Pengguna '{$user->name}' (" . ucfirst($user->role) . ") berhasil login ke sistem",
            ]);

            // Redirect berdasarkan Role sesuai matriks Anda
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'petugas') {
                return redirect()->route('petugas.peminjaman.index');
            } elseif ($user->role === 'peminjam') {
                return redirect()->route('peminjam.dashboard');
            }

            Auth::logout();
            return redirect()->route('login')->with('error', 'Role tidak dikenali.');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            LogAktivitas::create([
                'user_id' => $user->id,
                'aktivitas' => "Pengguna '{$user->name}' logout dari sistem",
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}