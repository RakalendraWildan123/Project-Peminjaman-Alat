<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserApiTest extends ApiTestCase
{
    public function test_user_index_berhasil_diakses_oleh_admin(): void
    {
        $this->actingAsAdmin();
        $this->createPeminjam(['name' => 'Siswa 1']);

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'name', 'email', 'role']
                ]
            ]);
    }

    public function test_user_index_ditolak_403_untuk_petugas(): void
    {
        $this->actingAsPetugas();

        $response = $this->getJson('/api/user');

        $response->assertStatus(403);
    }

    public function test_user_index_ditolak_403_untuk_peminjam(): void
    {
        $this->actingAsPeminjam();

        $response = $this->getJson('/api/user');

        $response->assertStatus(403);
    }

    public function test_user_index_ditolak_401_tanpa_token(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    public function test_user_store_berhasil_menambahkan_petugas_baru(): void
    {
        $this->actingAsAdmin();

        $payload = [
            'name' => 'Petugas Baru',
            'email' => 'petugas_baru@example.com',
            'password' => 'password123',
            'role' => 'petugas',
            'no_hp' => '082111222333',
            'alamat' => 'Bandung Timur',
        ];

        $response = $this->postJson('/api/user', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Pengguna berhasil ditambahkan.',
                'data' => [
                    'email' => 'petugas_baru@example.com',
                    'role' => 'petugas',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'petugas_baru@example.com',
            'role' => 'petugas',
        ]);
    }

    public function test_user_store_berhasil_menambahkan_peminjam_baru(): void
    {
        $this->actingAsAdmin();

        $payload = [
            'name' => 'Peminjam Baru',
            'email' => 'peminjam_baru@example.com',
            'password' => 'password123',
            'role' => 'peminjam',
            'no_hp' => '083111222333',
            'alamat' => 'Bandung Selatan',
        ];

        $response = $this->postJson('/api/user', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.role', 'peminjam');

        $this->assertDatabaseHas('users', [
            'email' => 'peminjam_baru@example.com',
        ]);
    }

    public function test_user_store_gagal_nama_kosong(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/user', [
            'email' => 'noname@example.com',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_user_store_gagal_email_duplikat(): void
    {
        $this->actingAsAdmin();
        $this->createPeminjam(['email' => 'sama@example.com']);

        $response = $this->postJson('/api/user', [
            'name' => 'User Kembar',
            'email' => 'sama@example.com',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_store_gagal_password_kurang_dari_8_karakter(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/user', [
            'name' => 'Password Pendek',
            'email' => 'pendek@example.com',
            'password' => 'pass1',
            'role' => 'peminjam',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_user_store_gagal_role_tidak_valid(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/user', [
            'name' => 'Role Aneh',
            'email' => 'roleaneh@example.com',
            'password' => 'password123',
            'role' => 'superadmin_palsu',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_user_show_berhasil_menampilkan_detail_user(): void
    {
        $this->actingAsAdmin();
        $user = $this->createPeminjam(['name' => 'Target Show']);

        $response = $this->getJson('/api/user/' . $user->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => 'Target Show',
                ]
            ]);
    }

    public function test_user_show_gagal_404_jika_user_tidak_ditemukan(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/user/99999');

        $response->assertStatus(404);
    }

    public function test_user_update_berhasil_mengubah_data_user(): void
    {
        $this->actingAsAdmin();
        $user = $this->createPeminjam(['name' => 'Nama Sebelum Ubah']);

        $response = $this->putJson('/api/user/' . $user->id, [
            'name' => 'Nama Sesudah Ubah',
            'email' => $user->email,
            'password' => 'passwordBaru123',
            'role' => 'peminjam',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Data Pengguna berhasil diperbarui.',
                'data' => [
                    'name' => 'Nama Sesudah Ubah',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nama Sesudah Ubah',
        ]);
    }

    public function test_user_update_gagal_email_sudah_dipakai_user_lain(): void
    {
        $this->actingAsAdmin();
        $this->createPeminjam(['email' => 'userA@example.com']);
        $userB = $this->createPeminjam(['email' => 'userB@example.com']);

        $response = $this->putJson('/api/user/' . $userB->id, [
            'name' => 'Ganti Email Tabrakan',
            'email' => 'userA@example.com',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_destroy_berhasil_menghapus_user_lain(): void
    {
        $this->actingAsAdmin();
        $targetUser = $this->createPeminjam();

        $response = $this->deleteJson('/api/user/' . $targetUser->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Pengguna berhasil dihapus.'
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id
        ]);
    }

    public function test_user_destroy_gagal_422_admin_tidak_bisa_menghapus_akun_sendiri(): void
    {
        $admin = $this->actingAsAdmin();

        $response = $this->deleteJson('/api/user/' . $admin->id);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $admin->id
        ]);
    }
}
