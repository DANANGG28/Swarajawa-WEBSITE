<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Contracts\Provider as SocialiteProvider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class AuthWebFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_halaman_autentikasi_dapat_ditampilkan(): void
    {
        $this->get('/masuk')->assertOk()->assertSee('Masuk');
        $this->get('/daftar')->assertOk()->assertSee('Daftar Akun Siswa');
        $this->get('/lupa-sandi')->assertOk()->assertSee('Lupa Kata Sandi?');
        $this->get('/reset-sandi/token-contoh?email=andi@test.test')
            ->assertOk()
            ->assertSee('Isi Kata Sandi Baru');
    }

    public function test_login_google_tanpa_konfigurasi_menampilkan_galat(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
        ]);

        $this->get('/auth/google')
            ->assertRedirect(route('masuk'))
            ->assertSessionHasErrors('email');
    }

    public function test_login_google_membuat_akun_siswa_baru(): void
    {
        $provider = \Mockery::mock(SocialiteProvider::class);
        $provider->shouldReceive('user')->andReturn($this->googleUser('budi@gmail.test', 'Budi Google'));

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get('/auth/google/callback')
            ->assertRedirect(route('siswa.dashboard'))
            ->assertSessionHas('sukses');

        $this->assertDatabaseHas('siswa', ['email' => 'budi@gmail.test', 'nama_lengkap' => 'Budi Google']);
    }

    public function test_login_google_masuk_ke_akun_guru_yang_sudah_ada(): void
    {
        Guru::factory()->create(['email' => 'guru@gmail.test', 'nama_lengkap' => 'Bu Guru']);

        $provider = \Mockery::mock(SocialiteProvider::class);
        $provider->shouldReceive('user')->andReturn($this->googleUser('guru@gmail.test', 'Bu Guru'));

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get('/auth/google/callback')->assertRedirect(route('guru.dashboard'));
    }

    public function test_kirim_tautan_reset_kata_sandi(): void
    {
        Notification::fake();

        $siswa = Siswa::factory()->create(['email' => 'andi@test.test']);

        $this->post('/lupa-sandi', ['email' => 'andi@test.test'])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($siswa, ResetPassword::class);
    }

    public function test_email_tidak_terdaftar_gagal_mengirim_tautan(): void
    {
        $this->post('/lupa-sandi', ['email' => 'tidak@ada.test'])
            ->assertSessionHasErrors('email');
    }

    public function test_simpan_kata_sandi_baru_dari_tautan_reset(): void
    {
        $siswa = Siswa::factory()->create(['email' => 'andi@test.test', 'password' => 'password']);
        $token = Password::broker('siswa')->createToken($siswa);

        $this->post('/reset-sandi', [
            'token' => $token,
            'email' => 'andi@test.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])
            ->assertRedirect(route('masuk'))
            ->assertSessionHas('sukses');

        $this->assertTrue(Hash::check('rahasia123', $siswa->fresh()->password));
    }

    private function googleUser(string $email, string $name): SocialiteUser
    {
        $user = new SocialiteUser;
        $user->id = '1234567890';
        $user->name = $name;
        $user->email = $email;

        return $user;
    }
}
