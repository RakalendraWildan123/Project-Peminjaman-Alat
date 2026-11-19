<?php

namespace Tests\Feature\Api;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;

class PengembalianApiTest extends ApiTestCase
{
    public function test_pengembalian_index_berhasil_diakses_oleh_admin(): void
    {
        $this->actingAsAdmin();
        $this->createPengembalian();

        $response = $this->getJson('/api/pengembalian');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'peminjaman_id', 'tgl_kembali', 'kondisi_kembali', 'denda']
                ]
            ]);
    }

    public function test_pengembalian_index_ditolak_403_untuk_peminjam(): void
    {
        $this->actingAsPeminjam();

        $response = $this->getJson('/api/pengembalian');

        $response->assertStatus(403);
    }

    public function test_pengembalian_index_ditolak_401_tanpa_token(): void
    {
        $response = $this->getJson('/api/pengembalian');

        $response->assertStatus(401);
    }

    public function test_pengembalian_store_berhasil_oleh_petugas_dan_menambah_stok(): void
    {
        $petugas = $this->actingAsPetugas();
        $alat = $this->createAlat(['stok' => 5]);
        $peminjaman = $this->createPeminjaman(['status' => 'dipinjam', 'tgl_kembali_plan' => now()->addDays(2)->toDateString()], [
            ['alat_id' => $alat->id, 'jumlah' => 2]
        ]);

        $payload = [
            'peminjaman_id' => $peminjaman->id,
            'kondisi_kembali' => 'Lengkap dan Mulus',
            'denda' => 0,
        ];

        $response = $this->postJson('/api/pengembalian', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Proses pengembalian alat berhasil diselesaikan.',
            ]);

        // Stok bertambah kembali: 5 + 2 = 7
        $this->assertEquals(7, $alat->fresh()->stok);
        $this->assertEquals('dikembalikan', $peminjaman->fresh()->status);
    }

    public function test_pengembalian_store_mengubah_status_peminjaman_menjadi_dikembalikan_jika_tepat_waktu(): void
    {
        $this->actingAsPetugas();
        $peminjaman = $this->createPeminjaman([
            'status' => 'dipinjam',
            'tgl_kembali_plan' => now()->addDays(1)->toDateString(),
        ]);

        $response = $this->postJson('/api/pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'kondisi_kembali' => 'Kondisi Baik',
        ]);

        $response->assertStatus(201);
        $this->assertEquals('dikembalikan', $peminjaman->fresh()->status);
    }

    public function test_pengembalian_store_mengubah_status_peminjaman_menjadi_telat_jika_lewat_tanggal_kembali(): void
    {
        $this->actingAsPetugas();
        $peminjaman = $this->createPeminjaman([
            'status' => 'dipinjam',
            'tgl_kembali_plan' => now()->subDays(2)->toDateString(),
        ]);

        $response = $this->postJson('/api/pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'kondisi_kembali' => 'Kondisi Baik tapi telat',
            'denda' => 20000,
        ]);

        $response->assertStatus(201);
        $this->assertEquals('telat', $peminjaman->fresh()->status);
    }

    public function test_pengembalian_store_bisa_mencatat_nilai_denda(): void
    {
        $this->actingAsPetugas();
        $peminjaman = $this->createPeminjaman(['status' => 'dipinjam']);

        $response = $this->postJson('/api/pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'kondisi_kembali' => 'Kabel sobek sedikit',
            'denda' => 15000,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'denda' => 15000,
        ]);
    }

    public function test_pengembalian_store_gagal_jika_peminjaman_tidak_berstatus_dipinjam(): void
    {
        $this->actingAsPetugas();
        $peminjaman = $this->createPeminjaman(['status' => 'diajukan']);

        $response = $this->postJson('/api/pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'kondisi_kembali' => 'Baik',
        ]);

        $response->assertStatus(422);
    }

    public function test_pengembalian_store_gagal_peminjaman_id_kosong(): void
    {
        $this->actingAsPetugas();

        $response = $this->postJson('/api/pengembalian', [
            'kondisi_kembali' => 'Baik',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['peminjaman_id']);
    }

    public function test_pengembalian_store_gagal_peminjaman_id_tidak_terdaftar(): void
    {
        $this->actingAsPetugas();

        $response = $this->postJson('/api/pengembalian', [
            'peminjaman_id' => 99999,
            'kondisi_kembali' => 'Baik',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['peminjaman_id']);
    }

    public function test_pengembalian_store_gagal_kondisi_kembali_kosong(): void
    {
        $this->actingAsPetugas();
        $peminjaman = $this->createPeminjaman(['status' => 'dipinjam']);

        $response = $this->postJson('/api/pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'kondisi_kembali' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['kondisi_kembali']);
    }

    public function test_pengembalian_store_ditolak_403_oleh_peminjam(): void
    {
        $this->actingAsPeminjam();
        $peminjaman = $this->createPeminjaman(['status' => 'dipinjam']);

        $response = $this->postJson('/api/pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'kondisi_kembali' => 'Baik',
        ]);

        $response->assertStatus(403);
    }

    public function test_pengembalian_show_berhasil_diakses_oleh_admin(): void
    {
        $this->actingAsAdmin();
        $pengembalian = $this->createPengembalian();

        $response = $this->getJson('/api/pengembalian/' . $pengembalian->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $pengembalian->id,
                ]
            ]);
    }

    public function test_pengembalian_show_gagal_404_jika_id_salah(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/pengembalian/99999');

        $response->assertStatus(404);
    }

    public function test_pengembalian_update_berhasil_mengubah_kondisi_dan_denda(): void
    {
        $this->actingAsAdmin();
        $pengembalian = $this->createPengembalian(['denda' => 0]);

        $response = $this->putJson('/api/pengembalian/' . $pengembalian->id, [
            'kondisi_kembali' => 'Kondisi direvisi oleh admin',
            'denda' => 25000,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Data pengembalian berhasil diperbarui.',
                'data' => [
                    'kondisi_kembali' => 'Kondisi direvisi oleh admin',
                    'denda' => 25000,
                ]
            ]);

        $this->assertDatabaseHas('pengembalian', [
            'id' => $pengembalian->id,
            'denda' => 25000,
        ]);
    }

    public function test_pengembalian_update_gagal_kondisi_kembali_kosong(): void
    {
        $this->actingAsAdmin();
        $pengembalian = $this->createPengembalian();

        $response = $this->putJson('/api/pengembalian/' . $pengembalian->id, [
            'kondisi_kembali' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['kondisi_kembali']);
    }

    public function test_pengembalian_destroy_berhasil_membatalkan_pengembalian_dan_mengembalikan_stok(): void
    {
        $this->actingAsAdmin();
        $alat = $this->createAlat(['stok' => 5]);
        $peminjaman = $this->createPeminjaman(['status' => 'dikembalikan'], [
            ['alat_id' => $alat->id, 'jumlah' => 2]
        ]);
        $pengembalian = $this->createPengembalian(['peminjaman_id' => $peminjaman->id]);

        $response = $this->deleteJson('/api/pengembalian/' . $pengembalian->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Data pengembalian berhasil dihapus. Stok dan status peminjaman telah dikembalikan ke kondisi semula.'
            ]);

        $this->assertDatabaseMissing('pengembalian', ['id' => $pengembalian->id]);
        $this->assertEquals('dipinjam', $peminjaman->fresh()->status);
        $this->assertEquals(3, $alat->fresh()->stok); // 5 - 2 = 3 (stok ditarik kembali)
    }
}
