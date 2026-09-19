<?php

namespace Tests\Feature;

use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Models\Soal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SpeakingExerciseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Paksa mode mock STT + tanpa panggilan LLM agar tes deterministik.
        config([
            'services.elevenlabs.api_key' => null,
            'services.edge_tts.binary' => 'edge-tts',
            'ai.base_url' => null,
        ]);

        Storage::fake('public');
    }

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

    private function soalKuisSuara(LevelMateri $level, int $bobot = 20): Soal
    {
        return Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_KUIS_SUARA,
            'pertanyaan' => 'Unenana "Roro Jonggrang".',
            'opsi_jawaban' => [
                'instruksi' => 'Unenana kanthi cetha.',
                'respons_benar' => 'Pinter! Pangucapanmu wis bener.',
                'respons_hampir_benar' => 'Hampir bener, dibaleni maneh ya.',
                'respons_salah' => 'Durung pas. Sing bener yaiku "Roro Jonggrang".',
            ],
            'kunci_jawaban' => ['teks' => 'roro jonggrang'],
            'bobot_exp' => $bobot,
        ]);
    }

    private function dummySoal(LevelMateri $level): Soal
    {
        return Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Salam esuk?',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'Sugeng enjing']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
        ]);
    }

    private function audio(): UploadedFile
    {
        return UploadedFile::fake()->create('rekaman.mp3', 50, 'audio/mpeg');
    }

    public function test_quiz_suara_scores_and_awards_exp(): void
    {
        $level = $this->level(1);
        $soal = $this->soalKuisSuara($level);
        $this->dummySoal($level);

        $siswa = $this->siswaDenganProgres($level);
        Sanctum::actingAs($siswa);

        $this->post("/api/soal/{$soal->id}/quiz-suara", [
            'audio' => $this->audio(),
            'mock_transcript' => 'roro jonggrang',
        ])
            ->assertOk()
            ->assertJson([
                'kategori' => 'benar',
                'skor' => 100,
                'benar' => true,
                'exp_didapat' => 20,
                'teks_respons' => 'Pinter! Pangucapanmu wis bener.',
            ]);

        $this->assertDatabaseHas('exp', ['siswa_id' => $siswa->id, 'total_exp' => 20]);
        $this->assertDatabaseHas('strek', ['siswa_id' => $siswa->id, 'current_streak' => 1]);
        $this->assertDatabaseHas('jawaban_siswa', [
            'siswa_id' => $siswa->id,
            'soal_id' => $soal->id,
            'skor_tertinggi' => 100,
        ]);
    }

    public function test_quiz_suara_wrong_utterance_uses_salah_response(): void
    {
        $level = $this->level(1);
        $soal = $this->soalKuisSuara($level);
        $this->dummySoal($level);

        $siswa = $this->siswaDenganProgres($level);
        Sanctum::actingAs($siswa);

        $this->post("/api/soal/{$soal->id}/quiz-suara", [
            'audio' => $this->audio(),
            'mock_transcript' => 'resep kue coklat',
        ])
            ->assertOk()
            ->assertJson(['kategori' => 'salah', 'benar' => false]);
    }

    public function test_quiz_suara_rejects_non_voice_soal(): void
    {
        $level = $this->level(1);
        $soal = $this->dummySoal($level);
        $siswa = $this->siswaDenganProgres($level);
        Sanctum::actingAs($siswa);

        $this->post("/api/soal/{$soal->id}/quiz-suara", ['audio' => $this->audio()])
            ->assertStatus(422);
    }

    public function test_quiz_suara_blocked_when_level_locked(): void
    {
        $level = $this->level(1);
        $soal = $this->soalKuisSuara($level);
        $siswa = $this->siswaDenganProgres($level, ProgresSiswa::STATUS_TERKUNCI);
        Sanctum::actingAs($siswa);

        $this->post("/api/soal/{$soal->id}/quiz-suara", ['audio' => $this->audio()])
            ->assertStatus(403)
            ->assertJsonFragment(['message' => 'Materi belum tercapai.']);
    }

    public function test_latihan_ngomong_returns_feedback_without_exp(): void
    {
        $level = $this->level(1);
        $soal = $this->soalKuisSuara($level);
        $siswa = $this->siswaDenganProgres($level);
        Sanctum::actingAs($siswa);

        $this->post("/api/soal/{$soal->id}/latihan-ngomong", [
            'audio' => $this->audio(),
            'mock_transcript' => 'roro jonggrang',
        ])
            ->assertOk()
            ->assertJsonStructure(['transkripsi', 'feedback_text', 'audio_url'])
            ->assertJsonMissing(['exp_didapat']);

        $this->assertDatabaseHas('exp', ['siswa_id' => $siswa->id, 'total_exp' => 0]);
    }
}
