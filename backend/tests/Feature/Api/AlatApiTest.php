<?php

namespace Tests\Feature\Api;

use App\Models\Alat;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AlatApiTest extends ApiTestCase
{
    public function test_katalog_berhasil_diakses_oleh_peminjam(): void
    {
        $this->actingAsPeminjam();
        $this->createAlat(['nama_alat' => 'Router WiFi', 'stok' => 5]);

        $response = $this->getJson('/api/katalog');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'nama_alat', 'stok', 'kategori']
                ]
            ]);
    }

    public function test_katalog_berhasil_diakses_oleh_petugas(): void
    {
        $this->actingAsPetugas();

        $response = $this->getJson('/api/katalog');

        $response->assertStatus(200);
    }

    public function test_katalog_berhasil_diakses_oleh_admin(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/katalog');

        $response->assertStatus(200);
    }

    public function test_katalog_ditolak_401_tanpa_login(): void
    {
        $response = $this->getJson('/api/katalog');

        $response->assertStatus(401);
    }

    public function test_katalog_hanya_menampilkan_alat_dengan_stok_lebih_dari_nol(): void
    {
        $this->actingAsPeminjam();
        $this->createAlat(['nama_alat' => 'Alat Tersedia', 'stok' => 3]);
        $this->createAlat(['nama_alat' => 'Alat Habis', 'stok' => 0]);

        $response = $this->getJson('/api/katalog');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Alat Tersedia', $data[0]['nama_alat']);
    }

    public function test_alat_index_berhasil_diakses_oleh_admin(): void
    {
        $this->actingAsAdmin();
        $this->createAlat(['nama_alat' => 'Switch Hub', 'stok' => 10]);

        $response = $this->getJson('/api/alat');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'nama_alat', 'stok', 'status_kondisi', 'kategori']
                ]
            ]);
    }

    public function test_alat_index_ditolak_403_untuk_bukan_admin(): void
    {
        $this->actingAsPetugas();

        $response = $this->getJson('/api/alat');

        $response->assertStatus(403);
    }

    public function test_alat_store_berhasil_oleh_admin(): void
    {
        $this->actingAsAdmin();
        $kategori = $this->createKategori();

        $payload = [
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Crimping Tool Pro',
            'stok' => 8,
            'status_kondisi' => 'Baik',
            'deskripsi' => 'Alat untuk pasang RJ45',
        ];

        $response = $this->postJson('/api/alat', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Alat berhasil ditambahkan.',
                'data' => [
                    'nama_alat' => 'Crimping Tool Pro',
                    'stok' => 8,
                ]
            ]);

        $this->assertDatabaseHas('alat', [
            'nama_alat' => 'Crimping Tool Pro',
            'stok' => 8,
        ]);
    }

    public function test_alat_store_gagal_nama_alat_kosong(): void
    {
        $this->actingAsAdmin();
        $kategori = $this->createKategori();

        $response = $this->postJson('/api/alat', [
            'kategori_id' => $kategori->id,
            'stok' => 5,
            'status_kondisi' => 'Baik',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nama_alat']);
    }

    public function test_alat_store_gagal_kategori_tidak_terdaftar(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/alat', [
            'kategori_id' => 99999,
            'nama_alat' => 'Alat Misteri',
            'stok' => 2,
            'status_kondisi' => 'Baik',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['kategori_id']);
    }

    public function test_alat_store_gagal_stok_kurang_dari_nol(): void
    {
        $this->actingAsAdmin();
        $kategori = $this->createKategori();

        $response = $this->postJson('/api/alat', [
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Alat Minus',
            'stok' => -1,
            'status_kondisi' => 'Baik',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['stok']);
    }

    public function test_alat_store_gagal_status_kondisi_kosong(): void
    {
        $this->actingAsAdmin();
        $kategori = $this->createKategori();

        $response = $this->postJson('/api/alat', [
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Alat Kondisi Kosong',
            'stok' => 3,
            'status_kondisi' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status_kondisi']);
    }

    public function test_alat_store_berhasil_dengan_upload_gambar_dummy(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();
        $kategori = $this->createKategori();
        $file = UploadedFile::fake()->image('alat_test.jpg');

        $response = $this->postJson('/api/alat', [
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Kamera DSLR Uji',
            'stok' => 4,
            'status_kondisi' => 'Baik',
            'gambar' => $file,
        ]);

        $response->assertStatus(201);
        $alat = Alat::where('nama_alat', 'Kamera DSLR Uji')->first();
        $this->assertNotNull($alat->gambar);
        Storage::disk('public')->assertExists($alat->gambar);
    }

    public function test_alat_show_berhasil_menampilkan_detail_alat(): void
    {
        $this->actingAsAdmin();
        $alat = $this->createAlat(['nama_alat' => 'Laptop ThinkPad']);

        $response = $this->getJson('/api/alat/' . $alat->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $alat->id,
                    'nama_alat' => 'Laptop ThinkPad',
                ]
            ]);
    }

    public function test_alat_show_gagal_404_jika_alat_tidak_ada(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/alat/99999');

        $response->assertStatus(404);
    }

    public function test_alat_update_berhasil_mengubah_data_alat(): void
    {
        $this->actingAsAdmin();
        $alat = $this->createAlat(['nama_alat' => 'Nama Awal', 'stok' => 10]);

        $response = $this->putJson('/api/alat/' . $alat->id, [
            'kategori_id' => $alat->kategori_id,
            'nama_alat' => 'Nama Diperbarui',
            'stok' => 15,
            'status_kondisi' => 'Baik',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Alat berhasil diperbarui.',
                'data' => [
                    'nama_alat' => 'Nama Diperbarui',
                    'stok' => 15,
                ]
            ]);

        $this->assertDatabaseHas('alat', [
            'id' => $alat->id,
            'nama_alat' => 'Nama Diperbarui',
            'stok' => 15,
        ]);
    }

    public function test_alat_update_gagal_stok_negatif(): void
    {
        $this->actingAsAdmin();
        $alat = $this->createAlat();

        $response = $this->putJson('/api/alat/' . $alat->id, [
            'kategori_id' => $alat->kategori_id,
            'nama_alat' => $alat->nama_alat,
            'stok' => -5,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['stok']);
    }

    public function test_alat_destroy_berhasil_menghapus_alat(): void
    {
        $this->actingAsAdmin();
        $alat = $this->createAlat(['nama_alat' => 'Alat Mau Dihapus']);

        $response = $this->deleteJson('/api/alat/' . $alat->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Alat berhasil dihapus.'
            ]);

        $this->assertDatabaseMissing('alat', [
            'id' => $alat->id,
        ]);
    }
}
