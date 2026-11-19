<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PeminjamanResource;
use App\Models\Peminjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LaporanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'status' => ['nullable', 'string', 'in:diajukan,dipinjam,dikembalikan,selesai,telat,ditolak'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Parameter filter tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Peminjaman::with([
            'user',
            'detailPinjam.alat',
            'pengembalian.petugas'
        ]);

        $query->when(
            $request->filled('start_date') && $request->filled('end_date'),
            function ($q) use ($request) {
                $q->whereBetween('tgl_pinjam', [
                    $request->start_date,
                    $request->end_date
                ]);
            }
        );

        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $perPage = $request->input('per_page', 15);

        $laporan = $query->latest()->paginate($perPage);

        return PeminjamanResource::collection($laporan)
            ->additional([
                'message' => 'Laporan peminjaman berhasil ditarik.'
            ])
            ->response();
    }
}