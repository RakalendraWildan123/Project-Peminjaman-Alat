<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // Menampilkan daftar pengajuan peminjaman dari siswa/peminjam
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'diajukan')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('petugas.peminjaman.index', compact('peminjamans', 'search'));
    }

    // Menyetujui Peminjaman (Mengubah status & mengurangi stok alat)
    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);
            $peminjaman->update(['status' => 'dipinjam']);

            // Kurangi stok alat secara otomatis
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menolak Peminjaman (Menghapus pengajuan agar siswa bisa mengajukan ulang)
    public function tolakPeminjaman($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            // Pastikan statusnya memang masih diajukan
            if ($peminjaman->status == 'diajukan') {
                $peminjaman->delete();
                return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil ditolak.');
            }

            return redirect()->back()->with('error', 'Status peminjaman sudah berubah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Memproses Pengembalian Alat
    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($peminjamanId);

            // Simpan data pengembalian
            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $request->denda ?? 0,
                'petugas_id' => auth()->id(),
            ]);

            // Update status peminjaman jadi selesai
            $peminjaman->update(['status' => 'selesai']);

            // Kembalikan stok alat ke inventaris
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian berhasil dicatat dan stok dipulihkan.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function indexPengembalian(Request $request)
    {
        $searchAktif = $request->input('search_aktif');
        $searchRiwayat = $request->input('search_riwayat');

        $peminjamanAktif = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'dipinjam')
            ->when($searchAktif, function ($query, $searchAktif) {
                return $query->whereHas('user', function ($q) use ($searchAktif) {
                    $q->where('name', 'like', "%{$searchAktif}%");
                });
            })
            ->latest()
            ->paginate(10, ['*'], 'page_aktif')
            ->withQueryString();

        $riwayatPengembalian = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPinjam.alat', 'petugas'])
            ->when($searchRiwayat, function ($query, $searchRiwayat) {
                return $query->whereHas('peminjaman.user', function ($q) use ($searchRiwayat) {
                    $q->where('name', 'like', "%{$searchRiwayat}%");
                })->orWhere('kondisi_kembali', 'like', "%{$searchRiwayat}%");
            })
            ->latest()
            ->paginate(10, ['*'], 'page_riwayat')
            ->withQueryString();

        return view('petugas.pengembalian.index', compact('peminjamanAktif', 'riwayatPengembalian', 'searchAktif', 'searchRiwayat'));
    }
    public function dashboard()
    {
        $totalMenunggu = Peminjaman::where('status', 'diajukan')->count();
        $totalDipinjam = Peminjaman::where('status', 'dipinjam')->count();

        $totalPengembalianBulanIni = Pengembalian::whereMonth('tgl_kembali', now()->month)
            ->whereYear('tgl_kembali', now()->year)
            ->count();

        $totalDendaBulanIni = Pengembalian::whereMonth('tgl_kembali', now()->month)
            ->whereYear('tgl_kembali', now()->year)
            ->sum('denda');

        $peminjamanTerbaru = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'diajukan')
            ->latest()
            ->take(5)
            ->get();

        return view('petugas.dashboard', compact(
            'totalMenunggu', 'totalDipinjam', 'totalPengembalianBulanIni', 'totalDendaBulanIni', 'peminjamanTerbaru'
        ));
    }

    // Laporan transaksi pengembalian (filter periode + siap cetak)
    public function laporan(Request $request)
    {
        $tglAwal = $request->input('tgl_awal');
        $tglAkhir = $request->input('tgl_akhir');

        $query = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPinjam.alat', 'petugas']);

        if ($tglAwal && $tglAkhir) {
            $query->whereBetween('tgl_kembali', [$tglAwal . ' 00:00:00', $tglAkhir . ' 23:59:59']);
        }

        $laporan = $query->latest()->get();
        $totalDenda = $laporan->sum('denda');

        return view('petugas.laporan.index', compact('laporan', 'tglAwal', 'tglAkhir', 'totalDenda'));
    }
}