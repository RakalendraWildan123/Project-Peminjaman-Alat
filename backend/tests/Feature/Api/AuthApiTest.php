<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class AuthApiTest extends ApiTestCase
{
    public function test_register_berhasil_dengan_data_valid(): void
    {
        $payload = [
            'name' => 'User Baru',
            'email' => 'baru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Kebon Jeruk No. 5',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'email', 'role'],
                'access_token',
                'token_type',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'baru@example.com',
            'role' => 'peminjam',
        ]);
    }

    public function test_register_gagal_nama_kosong(): void
    {
        $payload = [
            'email' => 'nama_kosong@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_register_gagal_email_kosong(): void
    {
        $payload = [
            'name' => 'Budi',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_gagal_format_email_salah(): void
    {
        $payload = [
            'name' => 'Budi',
            'email' => 'bukan-email-valid',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_gagal_email_sudah_digunakan(): void
    {
        $this->createPeminjam(['email' => 'duplikat@example.com']);

        $payload = [
            'name' => 'User Lain',
            'email' => 'duplikat@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_gagal_password_kosong(): void
    {
        $payload = [
            'name' => 'User Test',
            'email' => 'nopass@example.com',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_register_gagal_password_kurang_dari_8_karakter(): void
    {
        $payload = [
            'name' => 'User Test',
            'email' => 'pendek@example.com',
            'password' => '12345',
            'password_confirmation' => '12345',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_register_gagal_password_konfirmasi_tidak_cocok(): void
    {
        $payload = [
            'name' => 'User Test',
            'email' => 'beda@example.com',
            'password' => 'password123',
            'password_confirmation' => 'passwordBerbeda',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_register_gagal_no_hp_terlalu_panjang(): void
    {
        $payload = [
            'name' => 'User Test',
            'email' => 'panjang@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_hp' => '081234567890123456789',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['no_hp']);
    }

    public function test_register_otomatis_memiliki_role_peminjam(): void
    {
        $payload = [
            'name' => 'Coba Role',
            'email' => 'cobarole@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin', // Mencoba menyisipkan role admin
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201);
        $user = User::where('email', 'cobarole@example.com')->first();
        $this->assertEquals('peminjam', $user->role);
    }

    public function test_login_berhasil_untuk_admin(): void
    {
        $this->createAdmin([
            'email' => 'admin_login@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin_login@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'email', 'role'],
                'access_token',
                'token_type',
            ]);
    }

    public function test_login_berhasil_untuk_petugas(): void
    {
        $this->createPetugas([
            'email' => 'petugas_login@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'petugas_login@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.role', 'petugas');
    }

    public function test_login_berhasil_untuk_peminjam(): void
    {
        $this->createPeminjam([
            'email' => 'peminjam_login@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'peminjam_login@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.role', 'peminjam');
    }

    public function test_login_gagal_email_tidak_terdaftar(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'tidakada@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_gagal_password_salah(): void
    {
        $this->createPeminjam([
            'email' => 'salahpass@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'salahpass@example.com',
            'password' => 'passwordsalah',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_gagal_email_kosong(): void
    {
        $response = $this->postJson('/api/login', [
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_gagal_password_kosong(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_me_berhasil_mengambil_profil_saat_terautentikasi(): void
    {
        $user = $this->actingAsPeminjam();

        $response = $this->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Data profil berhasil diambil.',
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ],
            ]);
    }

    public function test_me_gagal_401_jika_unauthenticated(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
    }

    public function test_logout_berhasil_menghapus_token(): void
    {
        $user = $this->createPeminjam();
        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logout berhasil. Token telah dihapus.',
            ]);

        $this->assertCount(0, $user->tokens);
    }

    public function test_logout_gagal_401_tanpa_token(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }

    public function test_token_tidak_dapat_digunakan_kembali_setelah_logout(): void
    {
        $user = $this->createPeminjam();
        $token = $user->createToken('test_token')->plainTextToken;

        // Logout
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout')
            ->assertStatus(200);

        // Coba akses endpoint terproteksi dengan token yang sudah dihapus
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/me');

        $response->assertStatus(401);
    }
}
