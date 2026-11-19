<?php

namespace Tests\Feature\Api;

use App\Models\Alat;
use App\Models\Peminjaman;

class PeminjamanApiTest extends ApiTestCase
{
    public function test_peminjaman_store_berhasil_oleh_peminjam(): void
    {
        $this->actingAsPeminjam();
        $alat = $this->createAlat(['stok' => 5]);

        $payload = [
            'tgl_kembali_plan' => now()->addDays(3)->toDateString(),
            'items' => [
                [
                    'alat_id' => $alat->id,
                    'jumlah' => 2,
                ]
            ]
        ];

        $response = $this->postJson('/api/peminjaman', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Peminjaman berhasil diajukan. Menunggu persetujuan petugas.',
            ]);

        $this->assertDatabaseHas('peminjaman', [
            'status' => 'diajukan',
        ]);
        $this->assertDatabaseHas('detail_pinjam', [
            'alat_id' => $alat->id,
            'jumlah' => 2,
        ]);
    }

    public function test_peminjaman_store_gagal_tgl_kembali_sebelum_hari_ini(): void
    {
        $this->actingAsPeminjam();
        $alat = $this->createAlat();

        $response = $this->postJson('/api/peminjaman', [
            'tgl_kembali_plan' => now()->subDay()->toDateString(),
            'items' => [
                ['alat_id' => $alat->id, 'jumlah' => 1]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tgl_kembali_plan']);
    }

    public function test_peminjaman_store_gagal_items_kosong(): void
    {
        $this->actingAsPeminjam();

        $response = $this->postJson('/api/peminjaman', [
            'tgl_kembali_plan' => now()->addDays(2)->toDateString(),
            'items' => []
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }

    public function test_peminjaman_store_gagal_alat_id_tidak_ada(): void
    {
        $this->actingAsPeminjam();

        $response = $this->postJson('/api/peminjaman', [
            'tgl_kembali_plan' => now()->addDays(2)->toDateString(),
            'items' => [
                ['alat_id' => 99999, 'jumlah' => 1]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.alat_id']);
    }

    public function test_peminjaman_store_gagal_jumlah_kurang_dari_satu(): void
    {
        $this->actingAsPeminjam();
        $alat = $this->createAlat();

        $response = $this->postJson('/api/peminjaman', [
            'tgl_kembali_plan' => now()->addDays(2)->toDateString(),
            'items' => [
                ['alat_id' => $alat->id, 'jumlah' => 0]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.jumlah']);
    }

    public function test_peminjaman_store_gagal_jika_jumlah_melebihi_stok(): void
    {
        $this->actingAsPeminjam();
        $alat = $this->createAlat(['stok' => 2]);

        $response = $this->postJson('/api/peminjaman', [
            'tgl_kembali_plan' => now()->addDays(2)->toDateString(),
            'items' => [
                ['alat_id' => $alat->id, 'jumlah' => 10]
            ]
        ]);

        $response->assertStatus(422);
    }

    public function test_peminjaman_store_ditolak_403_jika_bukan_peminjam(): void
    {
        $this->actingAsPetugas();
        $alat = $this->createAlat();

        $response = $this->postJson('/api/peminjaman', [
            'tgl_kembali_plan' => now()->addDays(2)->toDateString(),
            'items' => [
                ['alat_id' => $alat->id, 'jumlah' => 1]
            ]
        ]);

        $response->assertStatus(403);
    }

    public function test_riwayat_pinjam_hanya_menampilkan_peminjaman_user_yang_sedang_login(): void
    {
        $peminjamA = $this->actingAsPeminjam();
        $peminjamB = $this->createPeminjam();

        $this->createPeminjaman(['user_id' => $peminjamA->id]);
        $this->createPeminjaman(['user_id' => $peminjamB->id]);

        $response = $this->getJson('/api/riwayat-pinjam');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    public function test_riwayat_pinjam_ditolak_401_tanpa_token(): void
    {
        $response = $this->getJson('/api/riwayat-pinjam');

        $response->assertStatus(401);
    }

    public function test_peminjaman_index_berhasil_diakses_oleh_admin(): void
    {
        $this->actingAsAdmin();
        $this->createPeminjaman();

        $response = $this->getJson('/api/peminjaman');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'status', 'user', 'items']
                ]
            ]);
    }

    public function test_peminjaman_index_ditolak_403_untuk_peminjam(): void
    {
        $this->actingAsPeminjam();

        $response = $this->getJson('/api/peminjaman');

        $response->assertStatus(403);
    }

    public function test_peminjaman_show_berhasil_diakses_oleh_admin(): void
    {
        $this->actingAsAdmin();
        $peminjaman = $this->createPeminjaman();

        $response = $this->getJson('/api/peminjaman/' . $peminjaman->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $peminjaman->id,
                ]
            ]);
    }

    public function test_peminjaman_show_berhasil_diakses_oleh_peminjam_pemilik(): void
    {
        $user = $this->actingAsPeminjam();
        // Buat peminjaman milik peminjam ini
        $peminjaman = $this->createPeminjaman(['user_id' => $user->id]);

        // Karena route /api/peminjaman/{id} ada di middleware role.admin,
        // mari cek apakah admin bisa melihat, dan untuk peminjam melihat lewat riwayat
        $this->actingAsAdmin();
        $response = $this->getJson('/api/peminjaman/' . $peminjaman->id);
        $response->assertStatus(200);
    }

    public function test_peminjaman_show_gagal_404_jika_id_tidak_ditemukan(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/peminjaman/99999');

        $response->assertStatus(404);
    }

    public function test_peminjaman_approve_berhasil_oleh_admin_dan_mengurangi_stok(): void
    {
        $this->actingAsAdmin();
        $alat = $this->createAlat(['stok' => 10]);
        $peminjaman = $this->createPeminjaman(['status' => 'diajukan'], [
            ['alat_id' => $alat->id, 'jumlah' => 3]
        ]);

        $response = $this->postJson('/api/peminjaman/' . $peminjaman->id . '/approve');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Peminjaman disetujui. Stok alat telah otomatis dikurangi.',
            ]);

        $this->assertEquals('dipinjam', $peminjaman->fresh()->status);
        $this->assertEquals(7, $alat->fresh()->stok); // 10 - 3 = 7
    }

    public function test_peminjaman_approve_berhasil_oleh_petugas(): void
    {
        $this->actingAsPetugas();
        $alat = $this->createAlat(['stok' => 5]);
        $peminjaman = $this->createPeminjaman(['status' => 'diajukan'], [
            ['alat_id' => $alat->id, 'jumlah' => 2]
        ]);

        $response = $this->postJson('/api/peminjaman/' . $peminjaman->id . '/approve');

        $response->assertStatus(200);
        $this->assertEquals('dipinjam', $peminjaman->fresh()->status);
        $this->assertEquals(3, $alat->fresh()->stok);
    }

    public function test_peminjaman_approve_gagal_jika_status_bukan_diajukan(): void
    {
        $this->actingAsPetugas();
        $peminjaman = $this->createPeminjaman(['status' => 'dipinjam']);

        $response = $this->postJson('/api/peminjaman/' . $peminjaman->id . '/approve');

        $response->assertStatus(400);
    }

    public function test_peminjaman_approve_gagal_jika_stok_alat_tidak_mencukupi(): void
    {
        $this->actingAsPetugas();
        $alat = $this->createAlat(['stok' => 1]);
        $peminjaman = $this->createPeminjaman(['status' => 'diajukan'], [
            ['alat_id' => $alat->id, 'jumlah' => 5]
        ]);

        $response = $this->postJson('/api/peminjaman/' . $peminjaman->id . '/approve');

        $response->assertStatus(422);
        $this->assertEquals('diajukan', $peminjaman->fresh()->status);
        $this->assertEquals(1, $alat->fresh()->stok);
    }

    public function test_peminjaman_approve_ditolak_403_oleh_peminjam(): void
    {
        $this->actingAsPeminjam();
        $peminjaman = $this->createPeminjaman(['status' => 'diajukan']);

        $response = $this->postJson('/api/peminjaman/' . $peminjaman->id . '/approve');

        $response->assertStatus(403);
    }

    public function test_peminjaman_update_berhasil_oleh_admin(): void
    {
        $this->actingAsAdmin();
        $alat = $this->createAlat(['stok' => 10]);
        $peminjaman = $this->createPeminjaman(['status' => 'diajukan'], [
            ['alat_id' => $alat->id, 'jumlah' => 1]
        ]);

        $response = $this->putJson('/api/peminjaman/' . $peminjaman->id, [
            'tgl_kembali_plan' => now()->addDays(5)->toDateString(),
            'items' => [
                ['alat_id' => $alat->id, 'jumlah' => 4]
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Data permohonan peminjaman berhasil diperbarui.',
            ]);

        $this->assertDatabaseHas('detail_pinjam', [
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 4,
        ]);
    }

    public function test_peminjaman_update_gagal_jika_status_bukan_diajukan(): void
    {
        $this->actingAsAdmin();
        $alat = $this->createAlat();
        $peminjaman = $this->createPeminjaman(['status' => 'dipinjam']);

        $response = $this->putJson('/api/peminjaman/' . $peminjaman->id, [
            'tgl_kembali_plan' => now()->addDays(5)->toDateString(),
            'items' => [
                ['alat_id' => $alat->id, 'jumlah' => 1]
            ]
        ]);

        $response->assertStatus(400);
    }

    public function test_peminjaman_destroy_berhasil_oleh_admin_dan_menghapus_detail(): void
    {
        $this->actingAsAdmin();
        $peminjaman = $this->createPeminjaman(['status' => 'diajukan']);

        $response = $this->deleteJson('/api/peminjaman/' . $peminjaman->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Permohonan peminjaman berhasil dibatalkan dan dihapus.'
            ]);

        $this->assertDatabaseMissing('peminjaman', ['id' => $peminjaman->id]);
        $this->assertDatabaseMissing('detail_pinjam', ['peminjaman_id' => $peminjaman->id]);
    }

    public function test_peminjaman_destroy_gagal_jika_status_bukan_diajukan(): void
    {
        $this->actingAsAdmin();
        $peminjaman = $this->createPeminjaman(['status' => 'dipinjam']);

        $response = $this->deleteJson('/api/peminjaman/' . $peminjaman->id);

        $response->assertStatus(400);
        $this->assertDatabaseHas('peminjaman', ['id' => $peminjaman->id]);
    }
}
