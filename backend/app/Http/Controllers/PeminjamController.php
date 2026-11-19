<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamController extends Controller
{
    // Dashboard ringkasan peminjam
    public function dashboard()
    {
        $userId = auth()->id();

        $totalDiajukan = Peminjaman::where('user_id', $userId)->where('status', 'diajukan')->count();
        $totalDipinjam = Peminjaman::where('user_id', $userId)->where('status', 'dipinjam')->count();
        $totalSelesai = Peminjaman::where('user_id', $userId)->whereIn('status', ['selesai', 'dikembalikan', 'telat'])->count();
        $totalAlatTersedia = Alat::where('stok', '>', 0)->count();

        // Pinjaman yang saat ini sedang aktif di tangan peminjam
        $pinjamanAktif = Peminjaman::with('detailPinjam.alat')
            ->where('user_id', $userId)
            ->where('status', 'dipinjam')
            ->latest()
            ->get();

        // Riwayat peminjaman terkini
        $riwayatTerbaru = Peminjaman::with('detailPinjam.alat')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // Rekomendasi alat tersedia di katalog
        $rekomendasiAlat = Alat::with('kategori')
            ->where('stok', '>', 0)
            ->latest()
            ->take(4)
            ->get();

        return view('peminjam.dashboard', compact(
            'totalDiajukan',
            'totalDipinjam',
            'totalSelesai',
            'totalAlatTersedia',
            'pinjamanAktif',
            'riwayatTerbaru',
            'rekomendasiAlat'
        ));
    }

    // Melihat daftar/katalog alat yang tersedia
    public function katalogAlat(Request $request)
    {
        $search = $request->input('search');
        $kategoriId = $request->input('kategori_id');

        $query = Alat::with('kategori')->where('stok', '>', 0);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_alat', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($katQuery) use ($search) {
                        $katQuery->where('nama_kategori', 'like', "%{$search}%");
                    });
            });
        }

        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }

        $alats = $query->latest()
            ->paginate(12)
            ->withQueryString();

        $kategoris = Kategori::all();

        return view('peminjam.katalog', compact('alats', 'search', 'kategoris', 'kategoriId'));
    }

    // Detail alat
    public function detailAlat($id)
    {
        $alat = Alat::with('kategori')->findOrFail($id);
        return view('peminjam.detail-alat', compact('alat'));
    }

    public function ajukanPeminjaman(Request $request)
    {
        $request->validate([
            'tgl_kembali_plan' => 'required|date|after_or_equal:today',
            'alat_id' => 'required',
            'jumlah' => 'required',
        ]);

        $alatIds = is_array($request->alat_id) ? $request->alat_id : [$request->alat_id];
        $jumlahs = is_array($request->jumlah) ? $request->jumlah : [$request->jumlah];

        DB::beginTransaction();
        try {
            // Buat header peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => auth()->id(),
                'tgl_pinjam' => now(),
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan',
                'permintaan_kembali' => false,
            ]);

            // Masukkan daftar alat yang dipinjam ke detail_pinjam
            foreach ($alatIds as $index => $alatId) {
                $qty = (int) ($jumlahs[$index] ?? 1);
                $alat = Alat::findOrFail($alatId);

                if ($alat->stok < $qty) {
                    throw new \Exception("Stok alat '{$alat->nama_alat}' tidak mencukupi (tersedia: {$alat->stok}).");
                }

                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => $qty,
                ]);
            }

            DB::commit();
            return redirect()->route('peminjam.riwayat')->with('success', 'Pengajuan peminjaman berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
        }
    }

    // Melihat riwayat peminjaman user yang sedang login
    public function riwayatPeminjaman(Request $request)
    {
        $tab = $request->input('tab', 'semua');

        $query = Peminjaman::with('detailPinjam.alat')
            ->where('user_id', auth()->id());

        if ($tab && $tab !== 'semua') {
            if ($tab === 'selesai') {
                $query->whereIn('status', ['selesai', 'dikembalikan', 'telat']);
            } else {
                $query->where('status', $tab);
            }
        }

        $peminjamans = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('peminjam.riwayat', compact('peminjamans', 'tab'));
    }

    // Mengajukan permintaan pengembalian alat
    public function ajukanPengembalian($id)
    {
        try {
            $peminjaman = Peminjaman::where('user_id', auth()->id())
                ->where('status', 'dipinjam')
                ->findOrFail($id);

            $peminjaman->update(['permintaan_kembali' => true]);

            return redirect()->route('peminjam.riwayat')->with('success', 'Permintaan pengembalian berhasil diajukan. Silakan serahkan alat ke petugas.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengajukan pengembalian: ' . $e->getMessage());
        }
    }
}