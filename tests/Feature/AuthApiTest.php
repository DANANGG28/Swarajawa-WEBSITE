<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Superadmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/auth/siswa/register', [
            'nis' => '1234567890',
            'nama_lengkap' => 'Siswa Anyar',
            'jenis_kelamin' => 'L',
            'kelas' => '7A',
            'email' => 'anyar@sinaujowo.test',
            'password' => 'password',
        ]);

        $response->assertStatus(201)->assertJsonStructure(['message', 'role', 'user', 'token']);
        $this->assertDatabaseHas('siswa', ['email' => 'anyar@sinaujowo.test']);
        $this->assertDatabaseHas('exp', ['siswa_id' => $response->json('user.id')]);
    }

    public function test_siswa_can_login_and_wrong_password_fails(): void
    {
        Siswa::factory()->create(['email' => 'andi@test.test', 'password' => 'password']);

        $this->postJson('/api/auth/siswa/login', [
            'email' => 'andi@test.test',
            'password' => 'password',
        ])->assertOk()->assertJsonStructure(['token']);

        $this->postJson('/api/auth/siswa/login', [
            'email' => 'andi@test.test',
            'password' => 'salah',
        ])->assertStatus(422);
    }

    public function test_guru_can_login_with_precreated_account(): void
    {
        Guru::factory()->create(['email' => 'guru@test.test', 'password' => 'password']);

        $this->postJson('/api/auth/guru/login', [
            'email' => 'guru@test.test',
            'password' => 'password',
        ])->assertOk()->assertJson(['role' => 'guru']);
    }

    public function test_guru_has_no_self_registration_route(): void
    {
        $this->postJson('/api/auth/guru/register', [])->assertStatus(404);
    }

    public function test_protected_route_requires_authentication(): void
    {
        $this->getJson('/api/materi')->assertStatus(401);
    }

    public function test_unified_login_detects_siswa_role(): void
    {
        Siswa::factory()->create(['email' => 'andi@test.test', 'password' => 'password']);

        $this->postJson('/api/auth/login', ['email' => 'andi@test.test', 'password' => 'password'])
            ->assertOk()
            ->assertJson(['role' => 'siswa'])
            ->assertJsonStructure(['token']);
    }

    public function test_unified_login_detects_guru_role(): void
    {
        Guru::factory()->create(['email' => 'guru@test.test', 'password' => 'password']);

        $this->postJson('/api/auth/login', ['email' => 'guru@test.test', 'password' => 'password'])
            ->assertOk()
            ->assertJson(['role' => 'guru']);
    }

    public function test_unified_login_detects_superadmin_role(): void
    {
        Superadmin::factory()->create(['email' => 'admin@test.test', 'password' => 'password']);

        $this->postJson('/api/auth/login', ['email' => 'admin@test.test', 'password' => 'password'])
            ->assertOk()
            ->assertJson(['role' => 'superadmin']);
    }

    public function test_unified_login_rejects_wrong_password(): void
    {
        Siswa::factory()->create(['email' => 'andi@test.test', 'password' => 'password']);

        $this->postJson('/api/auth/login', ['email' => 'andi@test.test', 'password' => 'salah'])
            ->assertStatus(422);
    }

    public function test_web_login_without_role_selects_guard_automatically(): void
    {
        $siswa = Siswa::factory()->create(['email' => 'andi@test.test', 'password' => 'password']);

        $this->post('/masuk', ['email' => 'andi@test.test', 'password' => 'password'])
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($siswa, 'siswa');
    }
}
