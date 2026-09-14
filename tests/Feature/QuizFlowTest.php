<?php

namespace Tests\Feature;

use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Models\Soal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class QuizFlowTest extends TestCase
{
    use RefreshDatabase;

    private function level(int $urutan = 1): LevelMateri
    {
        return LevelMateri::create([
            'nama_materi' => 'Level '.$urutan,
            'deskripsi' => 'Deskripsi',
            'reward_exp' => 100,
            'urutan' => $urutan,
        ]);
    }

    private function siswaDenganProgres(LevelMateri $level, string $status = ProgresSiswa::STATUS_BERJALAN): Siswa
    {
        $siswa = Siswa::factory()->create();
        $siswa->exp()->create(['total_exp' => 0]);
        $siswa->strek()->create(['current_streak' => 0, 'highest_streak' => 0]);
        ProgresSiswa::create([
            'siswa_id' => $siswa->id,
            'level_materi_id' => $level->id,
            'status' => $status,
        ]);

        return $siswa;
    }

    public function test_siswa_can_answer_pilihan_ganda_and_gain_exp(): void
    {
        $level = $this->level(1);
        $soal = Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Salam ing wayah esuk?',
            'opsi_jawaban' => [
                ['label' => 'A', 'teks' => 'Sugeng enjing'],
                ['label' => 'B', 'teks' => 'Sugeng dalu'],
            ],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
        ]);

        $siswa = $this->siswaDenganProgres($level);
        Sanctum::actingAs($siswa);

        $this->postJson('/api/kuis/jawab', ['soal_id' => $soal->id, 'jawaban' => 'A'])
            ->assertOk()
            ->assertJson(['benar' => true, 'skor' => 100, 'exp_didapat' => 10]);

        $this->assertDatabaseHas('exp', ['siswa_id' => $siswa->id, 'total_exp' => 10]);
        $this->assertDatabaseHas('strek', ['siswa_id' => $siswa->id, 'current_streak' => 1]);
    }

    public function test_locked_level_blocks_starting_quiz(): void
    {
        $level = $this->level(1);
        $siswa = $this->siswaDenganProgres($level, ProgresSiswa::STATUS_TERKUNCI);
        Sanctum::actingAs($siswa);

        $this->postJson("/api/materi/{$level->id}/mulai")
            ->assertStatus(403)
            ->assertJsonFragment(['message' => 'Materi belum tercapai. Selesaikan materi prasyarat terlebih dahulu.']);
    }

    public function test_materi_index_returns_progress_status(): void
    {
        $level = $this->level(1);
        $siswa = $this->siswaDenganProgres($level);
        Sanctum::actingAs($siswa);

        $this->getJson('/api/materi')
            ->assertOk()
            ->assertJsonPath('data.0.status', ProgresSiswa::STATUS_BERJALAN);
    }

    public function test_finishing_level_marks_progress_and_unlocks_next(): void
    {
        $level1 = $this->level(1);
        $level2 = $this->level(2);
        $siswa = $this->siswaDenganProgres($level1);
        ProgresSiswa::create(['siswa_id' => $siswa->id, 'level_materi_id' => $level2->id, 'status' => ProgresSiswa::STATUS_TERKUNCI]);

        Sanctum::actingAs($siswa);

        $this->postJson('/api/kuis/selesai', ['level_materi_id' => $level1->id])->assertOk();

        $this->assertDatabaseHas('progres_siswa', ['siswa_id' => $siswa->id, 'level_materi_id' => $level1->id, 'status' => ProgresSiswa::STATUS_SELESAI]);
        $this->assertDatabaseHas('progres_siswa', ['siswa_id' => $siswa->id, 'level_materi_id' => $level2->id, 'status' => ProgresSiswa::STATUS_BERJALAN]);
    }
}
