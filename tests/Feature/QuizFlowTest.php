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

    private function pilihanGanda(LevelMateri $level, int $bobot = 10): Soal
    {
        return Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Salam ing wayah esuk?',
            'opsi_jawaban' => [
                ['label' => 'A', 'teks' => 'Sugeng enjing'],
                ['label' => 'B', 'teks' => 'Sugeng dalu'],
            ],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => $bobot,
        ]);
    }

    public function test_siswa_can_answer_pilihan_ganda_and_gain_exp(): void
    {
        $level = $this->level(1);
        $soal = $this->pilihanGanda($level);
        // Soal kedua agar level belum otomatis rampung.
        $this->pilihanGanda($level);

        $siswa = $this->siswaDenganProgres($level);
        Sanctum::actingAs($siswa);

        $this->postJson('/api/kuis/jawab', ['soal_id' => $soal->id, 'jawaban' => 'A'])
            ->assertOk()
            ->assertJson(['benar' => true, 'skor' => 100, 'exp_didapat' => 10]);

        $this->assertDatabaseHas('exp', ['siswa_id' => $siswa->id, 'total_exp' => 10]);
        $this->assertDatabaseHas('strek', ['siswa_id' => $siswa->id, 'current_streak' => 1]);
    }

    public function test_retry_only_awards_incremental_exp_based_on_highest_score(): void
    {
        $level = $this->level(1);
        // Soal lain agar level belum rampung.
        $this->pilihanGanda($level);

        $soal = Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_SUSUN_KALIMAT,
            'pertanyaan' => 'Susun ukara',
            'opsi_jawaban' => ['aku', 'mangan'],
            'kunci_jawaban' => ['susunan' => ['aku', 'mangan']],
            'bobot_exp' => 100,
        ]);

        $siswa = $this->siswaDenganProgres($level);
        Sanctum::actingAs($siswa);

        // Percobaan 1: 1 dari 2 tembung trep -> skor 50, EXP 50.
        $this->postJson('/api/kuis/jawab', ['soal_id' => $soal->id, 'jawaban' => ['aku', 'salah']])
            ->assertOk()
            ->assertJson(['skor' => 50, 'exp_didapat' => 50, 'skor_tertinggi' => 50]);

        // Percobaan 2: bener kabeh -> skor 100, mung selisih 50 kang ditambahake.
        $this->postJson('/api/kuis/jawab', ['soal_id' => $soal->id, 'jawaban' => ['aku', 'mangan']])
            ->assertOk()
            ->assertJson(['skor' => 100, 'exp_didapat' => 50, 'total_exp' => 100]);

        // Percobaan 3: skor mudhun -> ora ana EXP tambahan.
        $this->postJson('/api/kuis/jawab', ['soal_id' => $soal->id, 'jawaban' => ['salah', 'salah']])
            ->assertOk()
            ->assertJson(['skor' => 0, 'exp_didapat' => 0]);

        $this->assertDatabaseHas('jawaban_siswa', [
            'siswa_id' => $siswa->id,
            'soal_id' => $soal->id,
            'skor_tertinggi' => 100,
            'exp_diberikan' => 100,
            'jumlah_percobaan' => 3,
        ]);
        $this->assertDatabaseHas('exp', ['siswa_id' => $siswa->id, 'total_exp' => 100]);
    }

    public function test_completing_all_soal_grants_reward_and_unlocks_next_level(): void
    {
        $level1 = $this->level(1);
        $level2 = $this->level(2);
        $soal = $this->pilihanGanda($level1);

        $siswa = $this->siswaDenganProgres($level1);
        ProgresSiswa::create([
            'siswa_id' => $siswa->id,
            'level_materi_id' => $level2->id,
            'status' => ProgresSiswa::STATUS_TERKUNCI,
        ]);

        Sanctum::actingAs($siswa);

        $this->postJson('/api/kuis/jawab', ['soal_id' => $soal->id, 'jawaban' => 'A'])
            ->assertOk()
            ->assertJson([
                'benar' => true,
                'exp_didapat' => 10,
                'reward_exp' => 100,
                'level_selesai' => true,
                'level_berikutnya' => 'Level 2',
            ]);

        $this->assertDatabaseHas('progres_siswa', [
            'siswa_id' => $siswa->id,
            'level_materi_id' => $level1->id,
            'status' => ProgresSiswa::STATUS_SELESAI,
        ]);
        $this->assertDatabaseHas('progres_siswa', [
            'siswa_id' => $siswa->id,
            'level_materi_id' => $level2->id,
            'status' => ProgresSiswa::STATUS_BERJALAN,
        ]);
        $this->assertDatabaseHas('exp', ['siswa_id' => $siswa->id, 'total_exp' => 110]);
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

    public function test_locked_level_blocks_answering_soal(): void
    {
        $level = $this->level(1);
        $soal = $this->pilihanGanda($level);
        $siswa = $this->siswaDenganProgres($level, ProgresSiswa::STATUS_TERKUNCI);
        Sanctum::actingAs($siswa);

        $this->postJson('/api/kuis/jawab', ['soal_id' => $soal->id, 'jawaban' => 'A'])
            ->assertStatus(403)
            ->assertJsonFragment(['message' => 'Materi belum tercapai.']);
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
