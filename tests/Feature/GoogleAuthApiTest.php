<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;
use Throwable;

class GoogleAuthApiTest extends TestCase
{
    use RefreshDatabase;

    private function mockSocialite(SocialiteUser|Throwable $result): void
    {
        $provider = Mockery::mock();
        $provider->shouldReceive('stateless')->andReturnSelf();

        if ($result instanceof Throwable) {
            $provider->shouldReceive('userFromToken')->andThrow($result);
        } else {
            $provider->shouldReceive('userFromToken')->andReturn($result);
        }

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    public function test_google_login_requires_id_token(): void
    {
        $this->postJson('/api/auth/google', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('id_token');
    }

    public function test_google_login_rejects_invalid_token(): void
    {
        $this->mockSocialite(new \Exception('invalid token'));

        $this->postJson('/api/auth/google', ['id_token' => 'dummy'])
            ->assertStatus(401)
            ->assertJson(['message' => 'Token Google tidak valid.']);
    }

    public function test_google_login_creates_new_siswa_and_returns_usable_token(): void
    {
        $this->mockSocialite(SocialiteUser::fake([
            'name' => 'Andi Prasetyo',
            'email' => 'andi@gmail.com',
        ]));

        $response = $this->postJson('/api/auth/google', ['id_token' => 'valid-token']);

        $response->assertOk()
            ->assertJson(['message' => 'Login dengan Google berhasil.', 'role' => 'siswa'])
            ->assertJsonStructure(['message', 'role', 'user', 'token']);

        $this->assertArrayNotHasKey('password', $response->json('user'));
        $this->assertDatabaseHas('siswa', ['email' => 'andi@gmail.com']);

        $token = $response->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/me')
            ->assertOk()
            ->assertJson(['role' => 'siswa']);
    }

    public function test_google_login_reuses_existing_siswa_without_duplicate(): void
    {
        $siswa = Siswa::factory()->create(['email' => 'andi@gmail.com']);

        $this->mockSocialite(SocialiteUser::fake(['email' => 'andi@gmail.com']));

        $this->postJson('/api/auth/google', ['id_token' => 'valid-token'])
            ->assertOk()
            ->assertJson(['role' => 'siswa', 'user' => ['id' => $siswa->id]]);

        $this->assertDatabaseCount('siswa', 1);
    }

    public function test_google_login_detects_guru_role_and_does_not_create_siswa(): void
    {
        $guru = Guru::factory()->create(['email' => 'guru@gmail.com']);

        $this->mockSocialite(SocialiteUser::fake(['email' => 'guru@gmail.com']));

        $this->postJson('/api/auth/google', ['id_token' => 'valid-token'])
            ->assertOk()
            ->assertJson(['role' => 'guru', 'user' => ['id' => $guru->id]]);

        $this->assertDatabaseCount('siswa', 0);
    }

    public function test_google_login_matches_email_case_insensitively(): void
    {
        $siswa = Siswa::factory()->create(['email' => 'Andi@Gmail.com']);

        $this->mockSocialite(SocialiteUser::fake(['email' => 'andi@gmail.com']));

        $this->postJson('/api/auth/google', ['id_token' => 'valid-token'])
            ->assertOk()
            ->assertJson(['role' => 'siswa', 'user' => ['id' => $siswa->id]]);

        $this->assertDatabaseCount('siswa', 1);
    }
}
