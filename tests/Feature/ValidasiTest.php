<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Models\Superadmin;
use App\Models\Topik;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ValidasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_pengelola_tidak_valid_ditolak(): void
    {
        $admin = Superadmin::factory()->create();

        $this->actingAs($admin, 'superadmin')->post('/superadmin/guru', [
            'role' => 'root',
            'nama_lengkap' => 'Akun Palsu',
            'email' => 'palsu@test.test',
            'password' => 'password',
        ])->assertStatus(422);

        $this->assertDatabaseMissing('guru', ['email' => 'palsu@test.test']);
        $this->assertDatabaseMissing('superadmin', ['email' => 'palsu@test.test']);
    }

    public function test_soal_menolak_json_jawaban_tidak_valid(): void
    {
        $guru = Guru::factory()->create();
        $level = LevelMateri::factory()->create();

        $this->actingAs($guru, 'guru')->post(route('guru.soal.store'), [
            'level_materi_id' => $level->id,
            'tipe_soal' => 'pilihan_ganda',
            'pertanyaan' => 'Pitakon uji?',
            'opsi_jawaban_raw' => '{bukan json valid',
            'kunci_jawaban_raw' => json_encode(['jawaban' => 'A']),
            'bobot_exp' => 10,
        ])->assertSessionHasErrors('opsi_jawaban_raw');
    }

    public function test_api_register_menolak_kata_sandi_pendek(): void
    {
        $this->postJson('/api/auth/siswa/register', [
            'nis' => '1234567890',
            'nama_lengkap' => 'Siswa Pendek',
            'jenis_kelamin' => 'L',
            'email' => 'pendek@test.test',
            'password' => 'pendek',
        ])->assertStatus(422)->assertJsonValidationErrors('password');
    }

    public function test_api_level_materi_nama_harus_unik_per_topik(): void
    {
        Sanctum::actingAs(Superadmin::factory()->create());

        $topik = Topik::create(['nama' => 'Basa', 'urutan' => 1]);
        LevelMateri::create([
            'topik_id' => $topik->id,
            'nama_materi' => 'Dasar',
            'reward_exp' => 10,
            'urutan' => 1,
        ]);

        $this->postJson('/api/level-materi', [
            'topik_id' => $topik->id,
            'nama_materi' => 'Dasar',
            'reward_exp' => 10,
            'urutan' => 2,
        ])->assertStatus(422)->assertJsonValidationErrors('nama_materi');
    }

    public function test_api_kuis_selesai_tidak_memberi_reward_dua_kali(): void
    {
        $level = LevelMateri::create(['nama_materi' => 'Level 1', 'reward_exp' => 100, 'urutan' => 1]);
        $siswa = Siswa::factory()->create();
        $siswa->exp()->create(['total_exp' => 0]);
        $siswa->strek()->create(['current_streak' => 0, 'highest_streak' => 0]);
        ProgresSiswa::create([
            'siswa_id' => $siswa->id,
            'level_materi_id' => $level->id,
            'status' => ProgresSiswa::STATUS_BERJALAN,
        ]);

        Sanctum::actingAs($siswa);

        $this->postJson('/api/kuis/selesai', ['level_materi_id' => $level->id])
            ->assertOk()
            ->assertJson(['reward_exp' => 100]);

        $this->postJson('/api/kuis/selesai', ['level_materi_id' => $level->id])
            ->assertOk()
            ->assertJson(['reward_exp' => 0]);

        $this->assertDatabaseHas('exp', ['siswa_id' => $siswa->id, 'total_exp' => 100]);
    }
}
