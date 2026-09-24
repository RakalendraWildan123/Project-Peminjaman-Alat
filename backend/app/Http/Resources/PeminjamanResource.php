<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeminjamanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'peminjam' => $this->user?->name,
            'tgl_pinjam' => $this->tgl_pinjam ? date('Y-m-d', strtotime($this->tgl_pinjam)) : null,
            'tgl_kembali_plan' => $this->tgl_kembali_plan ? date('Y-m-d', strtotime($this->tgl_kembali_plan)) : null,
            'status' => $this->status,
            'item_dipinjam' => $this->detailPinjam?->map(function ($detail) {
                return [
                    'nama_alat' => $detail->alat?->nama_alat ?? 'Alat Dihapus/Tidak Ditemukan',
                    'jumlah' => (int) $detail->jumlah,
                ];
            })->values(),
            'info_pengembalian' => $this->pengembalian ? [
                'tgl_kembali' => $this->pengembalian->tgl_kembali
                    ? date('Y-m-d', strtotime($this->pengembalian->tgl_kembali))
                    : null,
                'kondisi' => $this->pengembalian->kondisi_kembali,
                'denda' => (int) $this->pengembalian->denda,
                'petugas_penerima' => $this->pengembalian->petugas?->name ?? 'sistem',
            ] : null,
        ];
    }
}