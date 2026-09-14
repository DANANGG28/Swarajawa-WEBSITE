<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\LevelMateri;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Superadmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/guru/dashboard')->assertRedirect(route('masuk'));
        $this->get('/superadmin/dashboard')->assertRedirect(route('masuk'));
        $this->get('/')->assertRedirect(route('masuk'));
        $this->get('/latihan-soal')->assertRedirect(route('masuk'));
    }

    public function test_guru_can_access_guru_area_but_not_superadmin_area(): void
    {
        $guru = Guru::factory()->create();

        $this->actingAs($guru, 'guru')->get('/guru/dashboard')->assertOk();
        $this->actingAs($guru, 'guru')->get('/guru/soal')->assertOk();
        $this->actingAs($guru, 'guru')->get('/guru/test')->assertOk();
        $this->actingAs($guru, 'guru')->get('/superadmin/dashboard')->assertForbidden();
    }

    public function test_superadmin_can_access_superadmin_area_but_not_guru_area(): void
    {
        $admin = Superadmin::factory()->create();

        foreach (['/superadmin/dashboard', '/superadmin/guru', '/superadmin/siswa', '/superadmin/level-materi', '/superadmin/soal'] as $url) {
            $this->actingAs($admin, 'superadmin')->get($url)->assertOk();
        }

        $this->actingAs($admin, 'superadmin')->get('/guru/dashboard')->assertForbidden();
    }

    public function test_login_redirects_each_role_to_its_own_dashboard(): void
    {
        Guru::factory()->create(['email' => 'guru@test.test', 'password' => 'password']);
        Superadmin::factory()->create(['email' => 'admin@test.test', 'password' => 'password']);

        $this->post('/masuk', ['email' => 'guru@test.test', 'password' => 'password'])
            ->assertRedirect(route('guru.dashboard'));

        $this->post('/masuk', ['email' => 'admin@test.test', 'password' => 'password'])
            ->assertRedirect(route('superadmin.dashboard'));
    }

    public function test_home_redirects_guru_and_superadmin_to_their_dashboard(): void
    {
        $guru = Guru::factory()->create();
        $admin = Superadmin::factory()->create();

        $this->actingAs($guru, 'guru')->get('/')->assertRedirect(route('guru.dashboard'));
        $this->actingAs($admin, 'superadmin')->get('/')->assertRedirect(route('superadmin.dashboard'));
    }

    public function test_guru_can_create_soal_via_web_form(): void
    {
        $guru = Guru::factory()->create();
        $level = LevelMateri::create(['nama_materi' => 'Dasar', 'deskripsi' => 'x', 'reward_exp' => 100, 'urutan' => 1]);

        $this->actingAs($guru, 'guru')->post('/guru/soal', [
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Pitakon uji?',
            'opsi_jawaban_raw' => json_encode([['label' => 'A', 'teks' => 'Bener']]),
            'kunci_jawaban_raw' => json_encode(['jawaban' => 'A']),
            'bobot_exp' => 10,
        ])->assertRedirect();

        $this->assertDatabaseHas('soal', ['pertanyaan' => 'Pitakon uji?', 'guru_id' => $guru->id]);
    }

    public function test_guru_cannot_update_another_gurus_soal_via_web(): void
    {
        $guruA = Guru::factory()->create();
        $guruB = Guru::factory()->create();
        $level = LevelMateri::create(['nama_materi' => 'Dasar', 'deskripsi' => 'x', 'reward_exp' => 100, 'urutan' => 1]);

        $soal = Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Pitakon?',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'A']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'guru_id' => $guruA->id,
        ]);

        $this->actingAs($guruB, 'guru')->put("/guru/soal/{$soal->id}", [
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Diowahi',
            'kunci_jawaban_raw' => json_encode(['jawaban' => 'A']),
            'bobot_exp' => 10,
        ])->assertForbidden();
    }

    public function test_superadmin_can_create_level_materi_via_web_form(): void
    {
        $admin = Superadmin::factory()->create();

        $this->actingAs($admin, 'superadmin')->post('/superadmin/level-materi', [
            'nama_materi' => 'Level Anyar',
            'deskripsi' => 'Deskripsi',
            'reward_exp' => 120,
            'urutan' => 6,
        ])->assertRedirect();

        $this->assertDatabaseHas('level_materi', ['nama_materi' => 'Level Anyar']);
    }

    public function test_siswa_can_access_siswa_area_after_login(): void
    {
        $siswa = Siswa::factory()->create();

        foreach (['/', '/dashboard', '/latihan-soal', '/papan-skor', '/asisten-ai', '/profil'] as $url) {
            $this->actingAs($siswa, 'siswa')->get($url)->assertOk();
        }
    }

    public function test_guru_cannot_access_siswa_area(): void
    {
        $guru = Guru::factory()->create();

        $this->actingAs($guru, 'guru')->get('/latihan-soal')->assertForbidden();
        $this->actingAs($guru, 'guru')->get('/papan-skor')->assertForbidden();
    }
}
