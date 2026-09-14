<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\LevelMateri;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Superadmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    private function level(): LevelMateri
    {
        return LevelMateri::create(['nama_materi' => 'Dasar', 'deskripsi' => 'x', 'reward_exp' => 100, 'urutan' => 1]);
    }

    private function soalMilik(Guru $guru, LevelMateri $level): Soal
    {
        return Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Pitakon?',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'A']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
            'guru_id' => $guru->id,
        ]);
    }

    public function test_guru_cannot_update_another_gurus_soal(): void
    {
        $guruA = Guru::factory()->create();
        $guruB = Guru::factory()->create();
        $soal = $this->soalMilik($guruA, $this->level());

        Sanctum::actingAs($guruB);

        $this->putJson("/api/soal/{$soal->id}", [
            'level_materi_id' => $soal->level_materi_id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Diowahi',
            'kunci_jawaban' => ['jawaban' => 'A'],
        ])->assertForbidden();
    }

    public function test_superadmin_can_update_any_soal(): void
    {
        $guru = Guru::factory()->create();
        $admin = Superadmin::factory()->create();
        $soal = $this->soalMilik($guru, $this->level());

        Sanctum::actingAs($admin);

        $this->putJson("/api/soal/{$soal->id}", [
            'level_materi_id' => $soal->level_materi_id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Diowahi superadmin',
            'kunci_jawaban' => ['jawaban' => 'A'],
        ])->assertOk();

        $this->assertDatabaseHas('soal', ['id' => $soal->id, 'pertanyaan' => 'Diowahi superadmin']);
    }

    public function test_guru_dashboard_only_shows_attached_students(): void
    {
        $guru = Guru::factory()->create();
        $diampu = Siswa::factory()->create(['kelas' => '7A']);
        $oraDiampu = Siswa::factory()->create(['kelas' => '7B']);

        $guru->siswa()->attach($diampu->id, ['kelas' => '7A', 'mata_pelajaran' => 'Bahasa Jawa']);

        Sanctum::actingAs($guru);

        $response = $this->getJson('/api/guru/siswa')->assertOk();
        $response->assertJsonCount(1, 'data');
        $this->assertSame($diampu->id, $response->json('data.0.siswa_id'));
    }

    public function test_siswa_cannot_access_soal_management(): void
    {
        Sanctum::actingAs(Siswa::factory()->create());

        $this->getJson('/api/soal')->assertForbidden();
    }
}
