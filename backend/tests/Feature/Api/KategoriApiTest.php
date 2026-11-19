<?php

namespace Tests\Feature\Api;

class KategoriApiTest extends ApiTestCase
{
    public function test_kategori_index_berhasil_diakses_oleh_admin(): void
    {
        $this->actingAsAdmin();
        $this->createKategori(['nama_kategori' => 'Kategori Jaringan']);

        $response = $this->getJson('/api/kategori');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'nama_kategori']
                ]
            ]);
    }

    public function test_kategori_index_ditolak_403_untuk_petugas(): void
    {
        $this->actingAsPetugas();

        $response = $this->getJson('/api/kategori');

        $response->assertStatus(403);
    }

    public function test_kategori_index_ditolak_403_untuk_peminjam(): void
    {
        $this->actingAsPeminjam();

        $response = $this->getJson('/api/kategori');

        $response->assertStatus(403);
    }

    public function test_kategori_index_ditolak_401_tanpa_token(): void
    {
        $response = $this->getJson('/api/kategori');

        $response->assertStatus(401);
    }

    public function test_kategori_store_berhasil_oleh_admin(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/kategori', [
            'nama_kategori' => 'Multimedia Baru'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Kategori berhasil ditambahkan.',
                'data' => [
                    'nama_kategori' => 'Multimedia Baru'
                ]
            ]);

        $this->assertDatabaseHas('kategori', [
            'nama_kategori' => 'Multimedia Baru'
        ]);
    }

    public function test_kategori_store_gagal_nama_kategori_kosong(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/kategori', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nama_kategori']);
    }

    public function test_kategori_store_gagal_nama_kategori_duplikat(): void
    {
        $this->actingAsAdmin();
        $this->createKategori(['nama_kategori' => 'Kategori Unik']);

        $response = $this->postJson('/api/kategori', [
            'nama_kategori' => 'Kategori Unik'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nama_kategori']);
    }

    public function test_kategori_store_ditolak_403_untuk_petugas(): void
    {
        $this->actingAsPetugas();

        $response = $this->postJson('/api/kategori', [
            'nama_kategori' => 'Percobaan Petugas'
        ]);

        $response->assertStatus(403);
    }

    public function test_kategori_store_ditolak_403_untuk_peminjam(): void
    {
        $this->actingAsPeminjam();

        $response = $this->postJson('/api/kategori', [
            'nama_kategori' => 'Percobaan Peminjam'
        ]);

        $response->assertStatus(403);
    }

    public function test_kategori_show_berhasil_menampilkan_detail_kategori(): void
    {
        $this->actingAsAdmin();
        $kategori = $this->createKategori(['nama_kategori' => 'Kamera']);

        $response = $this->getJson('/api/kategori/' . $kategori->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $kategori->id,
                    'nama_kategori' => 'Kamera'
                ]
            ]);
    }

    public function test_kategori_show_gagal_404_jika_id_tidak_ditemukan(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/kategori/99999');

        $response->assertStatus(404);
    }

    public function test_kategori_update_berhasil_mengubah_nama_kategori(): void
    {
        $this->actingAsAdmin();
        $kategori = $this->createKategori(['nama_kategori' => 'Nama Lama']);

        $response = $this->putJson('/api/kategori/' . $kategori->id, [
            'nama_kategori' => 'Nama Baru Diedit'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Kategori berhasil diperbarui.',
                'data' => [
                    'nama_kategori' => 'Nama Baru Diedit'
                ]
            ]);

        $this->assertDatabaseHas('kategori', [
            'id' => $kategori->id,
            'nama_kategori' => 'Nama Baru Diedit'
        ]);
    }

    public function test_kategori_update_gagal_nama_kategori_kosong(): void
    {
        $this->actingAsAdmin();
        $kategori = $this->createKategori(['nama_kategori' => 'Nama Asli']);

        $response = $this->putJson('/api/kategori/' . $kategori->id, [
            'nama_kategori' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nama_kategori']);
    }

    public function test_kategori_update_gagal_nama_duplikat_dengan_kategori_lain(): void
    {
        $this->actingAsAdmin();
        $this->createKategori(['nama_kategori' => 'Kategori A']);
        $kategoriB = $this->createKategori(['nama_kategori' => 'Kategori B']);

        // Coba ganti nama Kategori B menjadi Kategori A
        $response = $this->putJson('/api/kategori/' . $kategoriB->id, [
            'nama_kategori' => 'Kategori A'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nama_kategori']);
    }

    public function test_kategori_destroy_berhasil_menghapus_kategori_kosong(): void
    {
        $this->actingAsAdmin();
        $kategori = $this->createKategori(['nama_kategori' => 'Kategori Hapus']);

        $response = $this->deleteJson('/api/kategori/' . $kategori->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Kategori berhasil dihapus.'
            ]);

        $this->assertDatabaseMissing('kategori', [
            'id' => $kategori->id
        ]);
    }

    public function test_kategori_destroy_gagal_422_jika_masih_memiliki_relasi_alat(): void
    {
        $this->actingAsAdmin();
        $kategori = $this->createKategori(['nama_kategori' => 'Kategori Bersama Alat']);
        $this->createAlat(['kategori_id' => $kategori->id, 'nama_alat' => 'Router Terhubung']);

        $response = $this->deleteJson('/api/kategori/' . $kategori->id);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh data alat.'
            ]);

        $this->assertDatabaseHas('kategori', [
            'id' => $kategori->id
        ]);
    }
}
