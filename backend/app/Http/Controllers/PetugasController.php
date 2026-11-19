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

            if ($peminjaman->status !== 'diajukan') {
                DB::rollBack();
                return redirect()->back()->with('error', 'Peminjaman tidak dalam status diajukan.');
            }

            // Validasi ketersediaan stok & kurangi stok alat secara atomik
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::where('id', $detail->alat_id)->lockForUpdate()->firstOrFail();
                if ($alat->stok < $detail->jumlah) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Stok alat '{$alat->nama_alat}' tidak mencukupi untuk disetujui (tersedia: {$alat->stok}, diminta: {$detail->jumlah}).");
                }
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            $peminjaman->update(['status' => 'dipinjam']);

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menolak Peminjaman
    public function tolakPeminjaman($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            // Pastikan statusnya memang masih diajukan
            if ($peminjaman->status == 'diajukan') {
                $peminjaman->update(['status' => 'ditolak']);
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
            'denda' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($peminjamanId);

            if ($peminjaman->status !== 'dipinjam') {
                DB::rollBack();
                return redirect()->back()->with('error', 'Peminjaman ini sudah diproses sebelumnya.');
            }

            // Simpan data pengembalian
            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $request->denda ?? 0,
                'petugas_id' => auth()->id(),
            ]);

            // Cek keterlambatan
            $tglKembaliPlan = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
            $hariIni = \Carbon\Carbon::now()->startOfDay();
            $statusAkhir = $hariIni->greaterThan($tglKembaliPlan) ? 'telat' : 'selesai';

            $peminjaman->update(['status' => $statusAkhir]);

            // Kembalikan stok alat ke inventaris
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::where('id', $detail->alat_id)->lockForUpdate()->firstOrFail();
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian berhasil dicatat dan stok dipulihkan.');

        } catch (\Exception $e) {
            DB::rollBack();
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