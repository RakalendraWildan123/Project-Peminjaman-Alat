<?php

namespace Tests\Feature\Api;

use App\Models\LogAktivitas;
use App\Models\Peminjaman;

class LaporanDanLogApiTest extends ApiTestCase
{
    public function test_log_aktivitas_berhasil_diakses_oleh_admin(): void
    {
        $admin = $this->actingAsAdmin();
        LogAktivitas::create([
            'user_id' => $admin->id,
            'aktivitas' => 'Testing log aktivitas admin',
        ]);

        $response = $this->getJson('/api/log-aktivitas');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'total_data',
                'data' => [
                    '*' => ['id', 'user_id', 'aktivitas', 'created_at']
                ]
            ]);
    }

    public function test_log_aktivitas_ditolak_403_untuk_petugas(): void
    {
        $this->actingAsPetugas();

        $response = $this->getJson('/api/log-aktivitas');

        $response->assertStatus(403);
    }

    public function test_log_aktivitas_ditolak_403_untuk_peminjam(): void
    {
        $this->actingAsPeminjam();

        $response = $this->getJson('/api/log-aktivitas');

        $response->assertStatus(403);
    }

    public function test_log_aktivitas_ditolak_401_tanpa_token(): void
    {
        $response = $this->getJson('/api/log-aktivitas');

        $response->assertStatus(401);
    }

    public function test_laporan_peminjaman_berhasil_diakses_oleh_admin(): void
    {
        $this->actingAsAdmin();
        $this->createPeminjaman();

        $response = $this->getJson('/api/laporan-peminjaman');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'status', 'user', 'items']
                ]
            ]);
    }

    public function test_laporan_peminjaman_berhasil_diakses_oleh_petugas(): void
    {
        $this->actingAsPetugas();
        $this->createPeminjaman();

        $response = $this->getJson('/api/laporan-peminjaman');

        $response->assertStatus(200);
    }

    public function test_laporan_peminjaman_ditolak_403_untuk_peminjam(): void
    {
        $this->actingAsPeminjam();

        $response = $this->getJson('/api/laporan-peminjaman');

        $response->assertStatus(403);
    }

    public function test_laporan_peminjaman_ditolak_401_tanpa_token(): void
    {
        $response = $this->getJson('/api/laporan-peminjaman');

        $response->assertStatus(401);
    }

    public function test_laporan_peminjaman_berhasil_filter_status(): void
    {
        $this->actingAsAdmin();
        $this->createPeminjaman(['status' => 'dipinjam']);
        $this->createPeminjaman(['status' => 'diajukan']);

        $response = $this->getJson('/api/laporan-peminjaman?status=dipinjam');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('dipinjam', $data[0]['status']);
    }

    public function test_laporan_peminjaman_berhasil_filter_rentang_tanggal(): void
    {
        $this->actingAsAdmin();
        $this->createPeminjaman(['tgl_pinjam' => '2026-01-01']);
        $this->createPeminjaman(['tgl_pinjam' => '2026-06-15']);

        $response = $this->getJson('/api/laporan-peminjaman?start_date=2026-06-01&end_date=2026-06-30');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    public function test_laporan_peminjaman_gagal_format_tanggal_salah(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/laporan-peminjaman?start_date=01-01-2026');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['start_date']);
    }

    public function test_laporan_peminjaman_gagal_end_date_sebelum_start_date(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/laporan-peminjaman?start_date=2026-06-10&end_date=2026-06-05');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }

    public function test_laporan_peminjaman_gagal_status_tidak_valid(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/laporan-peminjaman?status=status_ngawur');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_laporan_peminjaman_bisa_mengatur_per_page(): void
    {
        $this->actingAsAdmin();
        $this->createPeminjaman();
        $this->createPeminjaman();
        $this->createPeminjaman();

        $response = $this->getJson('/api/laporan-peminjaman?per_page=2');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(2, $data);
    }
}
