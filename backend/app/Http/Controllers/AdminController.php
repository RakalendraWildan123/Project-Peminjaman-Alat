<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\DetailPinjam;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Menampilkan Dashboard admin, Diagram Status & Diagram Batang Peminjam
    public function index()
    {
        // 1. Data Ringkasan Metrik
        $totalAlat = Alat::count();
        $totalUser = User::count();
        $totalDipinjam = Peminjaman::where('status', 'dipinjam')->count();
        $totalLogs = LogAktivitas::count();

        // 2. Data untuk Diagram Lingkaran: Status Peminjaman
        $statusPeminjamanCounts = [
            'Diajukan' => Peminjaman::where('status', 'diajukan')->count(),
            'Dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'Selesai'  => Peminjaman::whereIn('status', ['selesai', 'dikembalikan', 'telat'])->count(),
            'Ditolak'  => Peminjaman::where('status', 'ditolak')->count(),
        ];

        // 3. Data untuk Diagram Batang: Seberapa banyak orang melakukan peminjaman
        $peminjamStats = User::where('role', 'peminjam')
            ->withCount('peminjaman')
            ->orderByDesc('peminjaman_count')
            ->take(8)
            ->get();

        $peminjamLabels = $peminjamStats->pluck('name')->toArray();
        $peminjamCounts = $peminjamStats->pluck('peminjaman_count')->toArray();
        $totalOrangMeminjam = User::where('role', 'peminjam')->has('peminjaman')->count();

        // 4. Log Aktivitas Terbaru
        $logs = LogAktivitas::with('user')->latest()->take(15)->get();

        return view('admin.dashboard', compact(
            'totalAlat',
            'totalUser',
            'totalDipinjam',
            'totalLogs',
            'statusPeminjamanCounts',
            'peminjamLabels',
            'peminjamCounts',
            'totalOrangMeminjam',
            'logs'
        ));
    }

    // =========================================================================
    // CRUD ALAT
    // =========================================================================

    // 1. Menampilkan daftar alat dengan search & paginasi
    public function indexAlat(Request $request)
    {
        $search = $request->input('search');

        $alats = Alat::with('kategori')
            ->when($search, function ($query, $search) {
                return $query->where('nama_alat', 'like', "%{$search}%")
                    ->orWhere('status_kondisi', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where('nama_kategori', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.alat.index', compact('alats', 'search'));
    }

    // 2. Menampilkan form tambah alat
    public function createAlat()
    {
        $kategoris = Kategori::all();
        return view('admin.alat.create', compact('kategoris'));
    }

    // 3. Menyimpan alat baru
    public function storeAlat(Request $request)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Handle Upload Gambar jika ada
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        Alat::create($data);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil ditambahkan.');
    }

    // 4. Menampilkan form edit alat
    public function editAlat($id)
    {
        $alat = Alat::findOrFail($id);
        $kategoris = Kategori::all();
        return view('admin.alat.edit', compact('alat', 'kategoris'));
    }

    // 5. Memperbarui data alat
    public function updateAlat(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);

        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Handle Update Gambar jika ada file baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($alat->gambar) {
                $cleanOld = ltrim($alat->gambar, '/');
                $oldPath = str_starts_with($cleanOld, 'storage/') ? public_path($cleanOld) : public_path('storage/' . $cleanOld);
                if (file_exists($oldPath) && is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        $alat->update($data);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil diperbarui.');
    }

    // 6. Menghapus data alat
    public function destroyAlat($id)
    {
        $alat = Alat::findOrFail($id);

        // Hapus file gambar fisik jika ada
        if ($alat->gambar) {
            $cleanPath = ltrim($alat->gambar, '/');
            $fullPath = str_starts_with($cleanPath, 'storage/') ? public_path($cleanPath) : public_path('storage/' . $cleanPath);
            if (file_exists($fullPath) && is_file($fullPath)) {
                @unlink($fullPath);
            }
        }

        $alat->delete();

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil dihapus.');
    }

    // =========================================================================
    // CRUD USER (Manajemen User Admin, Petugas, Peminjam)
    // =========================================================================

    public function indexUser(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('role', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('admin.user.index', compact('users', 'search'));
    }

    public function createUser()
    {
        return view('admin.user.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,petugas,peminjam',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'no_hp' => $request->no_hp,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,petugas,peminjam',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'no_hp' => $request->no_hp,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.user.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroyUser($id)
    {
        if (auth()->id() == $id) {
            return redirect()->route('admin.user.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus.');
    }

    public function showUser($id)
    {
        return redirect()->route('admin.user.edit', $id);
    }

    // =========================================================================
    // CRUD KATEGORI
    // =========================================================================

    public function indexKategori(Request $request)
    {
        $search = $request->input('search');

        $kategoris = Kategori::when($search, function ($query, $search) {
            return $query->where('nama_kategori', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(5)
        ->withQueryString();

        return view('admin.kategori.index', compact('kategoris', 'search'));
    }

    public function createKategori()
    {
        return view('admin.kategori.create');
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function editKategori($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function updateKategori(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,' . $id,
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        if ($kategori->alats()->count() > 0) {
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data alat.');
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
    // =========================================================================
    // MANAGEMENT PEMINJAMAN
    // =========================================================================

    // 1. Menampilkan daftar peminjam
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->when($search, function ($query, $search) {
                return $query->where('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.peminjaman.index', compact('peminjamans', 'search'));
    }

    // 2. Menampilkan form tambah peminjaman
    public function createPeminjaman()
    {
        $users = User::where('role', 'peminjam')->get(); // Atau ambil semua user jika bebas
        $alats = Alat::where('stok', '>', 0)->get();
        return view('admin.peminjaman.create', compact('users', 'alats'));
    }

    // 3. Menyimpan data peminjaman baru
    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tgl_pinjam' => 'required|date',
            'tgl_kembali_plan' => 'required|date|after_or_equal:tgl_pinjam',
            'alat_id' => 'required|array',
            'alat_id.*' => 'exists:alat,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Buat transaksi utama peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => $request->user_id,
                'tgl_pinjam' => $request->tgl_pinjam,
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan', // Status awal
            ]);

            // Simpan detail alat yang dipinjam
            foreach ($request->alat_id as $index => $alatId) {
                $jumlahPinjam = $request->jumlah[$index];

                $alat = Alat::findOrFail($alatId);

                // Validasi stok
                if ($alat->stok < $jumlahPinjam) {
                    throw new \Exception("Stok alat '{$alat->nama_alat}' tidak mencukupi.");
                }

                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => $jumlahPinjam,
                ]);

                // Kurangi stok alat jika status langsung disetujui/dipinjam (Opsional, atau dikurangi saat status berubah jadi 'dipinjam')
            }

            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // 4. Memperbarui status peminjaman (Misal: dari diajukan -> dipinjam / selesai / ditolak)
    public function updateStatusPeminjaman(Request $request, $id)
    {
        $peminjaman = Peminjaman::with('detailPinjam.alat')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:diajukan,dipinjam,selesai,dikembalikan,ditolak,telat',
        ]);

        DB::beginTransaction();
        try {
            $statusLama = $peminjaman->status;
            $statusBaru = $request->status;

            // Logika pengelolaan stok otomatis
            if ($statusLama != 'dipinjam' && $statusBaru == 'dipinjam') {
                // Kurangi stok karena barang resmi dipinjam
                foreach ($peminjaman->detailPinjam as $detail) {
                    $alat = Alat::where('id', $detail->alat_id)->lockForUpdate()->firstOrFail();
                    if ($alat->stok < $detail->jumlah) {
                        throw new \Exception("Stok alat {$alat->nama_alat} tidak mencukupi untuk dipinjam.");
                    }
                    $alat->decrement('stok', $detail->jumlah);
                }
            } elseif ($statusLama == 'dipinjam' && in_array($statusBaru, ['selesai', 'dikembalikan', 'ditolak'])) {
                // Kembalikan stok karena barang sudah kembali / dibatalkan
                foreach ($peminjaman->detailPinjam as $detail) {
                    $alat = Alat::where('id', $detail->alat_id)->lockForUpdate()->firstOrFail();
                    $alat->increment('stok', $detail->jumlah);
                }
            }

            $peminjaman->update(['status' => $statusBaru]);

            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Status peminjaman berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    
    public function indexPengembalian(Request $request)
    {
        return redirect()->route('admin.pengembalian.menunggu', $request->query());
    }

    public function indexMenungguPengembalian(Request $request)
    {
        $search = $request->input('search');

        $peminjamanAktif = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'dipinjam')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengembalian.menunggu', compact('peminjamanAktif', 'search'));
    }

    public function indexRiwayatPengembalian(Request $request)
    {
        $search = $request->input('search');

        $pengembalians = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPinjam.alat', 'petugas'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->whereHas('peminjaman.user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })->orWhere('kondisi_kembali', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengembalian.riwayat', compact('pengembalians', 'search'));
    }

    // Admin memproses pengembalian baru (sama seperti petugas)
    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($peminjamanId);

            if ($peminjaman->status !== 'dipinjam') {
                DB::rollBack();
                return redirect()->back()->with('error', 'Peminjaman ini sudah diproses sebelumnya.');
            }

            Pengembalian::create([
                'peminjaman_id'   => $peminjaman->id,
                'tgl_kembali'     => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda'           => $request->denda ?? 0,
                'petugas_id'      => auth()->id(), // admin yang proses, tetap tercatat di kolom ini
            ]);

            // Cek keterlambatan
            $tglKembaliPlan = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
            $hariIni = \Carbon\Carbon::now()->startOfDay();
            $statusAkhir = $hariIni->greaterThan($tglKembaliPlan) ? 'telat' : 'selesai';

            $peminjaman->update(['status' => $statusAkhir]);

            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::where('id', $detail->alat_id)->lockForUpdate()->firstOrFail();
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Form edit data pengembalian yang sudah ada
    public function editPengembalian($id)
    {
        $pengembalian = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPinjam.alat'])->findOrFail($id);
        return view('admin.pengembalian.edit', compact('pengembalian'));
    }

    // Update kondisi/denda (koreksi data, TIDAK mengubah stok karena barang sudah kembali)
    public function updatePengembalian(Request $request, $id)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda' => 'nullable|integer|min:0',
        ]);

        $pengembalian = Pengembalian::findOrFail($id);
        $pengembalian->update([
            'kondisi_kembali' => $request->kondisi_kembali,
            'denda' => $request->denda ?? 0,
        ]);

        return redirect()->route('admin.pengembalian.riwayat')->with('success', 'Data pengembalian berhasil diperbarui.');
    }

    // Hapus data riwayat pengembalian
    public function destroyPengembalian($id)
    {
        DB::beginTransaction();
        try {
            $pengembalian = Pengembalian::with('peminjaman.detailPinjam')->findOrFail($id);
            $peminjaman = $pengembalian->peminjaman;

            // Kembalikan status peminjaman ke 'dipinjam' dan sesuaikan stok jika ada
            if ($peminjaman) {
                $peminjaman->update(['status' => 'dipinjam']);
                foreach ($peminjaman->detailPinjam as $detail) {
                    $alat = Alat::find($detail->alat_id);
                    if ($alat) {
                        $alat->decrement('stok', $detail->jumlah);
                    }
                }
            }

            $pengembalian->delete();

            DB::commit();
            return redirect()->route('admin.pengembalian.riwayat')->with('success', 'Data pengembalian berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}