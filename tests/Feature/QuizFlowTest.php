<?php

namespace Tests\Feature;

use App\Models\JawabanSiswa;
use App\Models\LevelMateri;
use App\Models\Pembahasan;
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

    public function test_web_kuis_replay_still_returns_next_url_to_next_soal(): void
    {
        $level = $this->level(1);
        $soal1 = $this->pilihanGanda($level);
        $soal2 = $this->pilihanGanda($level);

        $siswa = $this->siswaDenganProgres($level);

        // Anggap kedua soal sudah pernah diselesaikan sebelumnya dengan skor 100
        JawabanSiswa::create([
            'siswa_id' => $siswa->id,
            'soal_id' => $soal1->id,
            'skor_tertinggi' => 100,
            'exp_diberikan' => 10,
        ]);
        JawabanSiswa::create([
            'siswa_id' => $siswa->id,
            'soal_id' => $soal2->id,
            'skor_tertinggi' => 100,
            'exp_diberikan' => 10,
        ]);

        // Siswa mengulang soal 1 lewat web kuis
        $response = $this->actingAs($siswa, 'siswa')
            ->postJson('/kuis/jawab', ['soal_id' => $soal1->id, 'jawaban' => 'A']);

        $response->assertOk()
            ->assertJsonPath('benar', true)
            ->assertJsonPath('next_soal_id', $soal2->id);

        $this->assertNotNull($response->json('next_url'));
        $this->assertStringContainsString('soal_id='.$soal2->id, $response->json('next_url'));
    }

    public function test_web_kuis_transitions_from_pembahasan_1_to_pembahasan_2(): void
    {
        $level = $this->level(1);

        $pembahasan1 = Pembahasan::create([
            'level_materi_id' => $level->id,
            'urutan' => 1,
            'nama' => 'Pembahasan 1',
        ]);
        $pembahasan2 = Pembahasan::create([
            'level_materi_id' => $level->id,
            'urutan' => 2,
            'nama' => 'Pembahasan 2',
        ]);

        $soal1 = Soal::create([
            'level_materi_id' => $level->id,
            'pembahasan_id' => $pembahasan1->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Soal 1 di Pembahasan 1',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'A']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
        ]);

        $soal2 = Soal::create([
            'level_materi_id' => $level->id,
            'pembahasan_id' => $pembahasan2->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Soal 1 di Pembahasan 2',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'A']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
        ]);

        $siswa = $this->siswaDenganProgres($level);

        // Siswa menyelesaikan soal 1 (soal terakhir di pembahasan 1)
        $response = $this->actingAs($siswa, 'siswa')
            ->postJson('/kuis/jawab', ['soal_id' => $soal1->id, 'jawaban' => 'A']);

        $response->assertOk()
            ->assertJsonPath('next_soal_id', $soal2->id);

        $this->assertNotNull($response->json('next_url'));
        $this->assertStringContainsString('soal_id='.$soal2->id, $response->json('next_url'));
    }

    public function test_web_kuis_provides_next_level_url_when_level_is_finished(): void
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

        // Siswa menjawab satu-satunya soal di level 1 sehingga level 1 selesai
        $response = $this->actingAs($siswa, 'siswa')
            ->postJson('/kuis/jawab', ['soal_id' => $soal->id, 'jawaban' => 'A']);

        $response->assertOk()
            ->assertJsonPath('level_selesai', true)
            ->assertJsonPath('next_level_nama', 'Level 2');

        $this->assertNotNull($response->json('next_level_url'));
        $this->assertStringContainsString('/kuis/mulai/'.$level2->id, $response->json('next_level_url'));
    }

    public function test_mulai_level_redirects_to_unfinished_soal_in_next_pembahasan_not_repeating_completed_soal(): void
    {
        $level = $this->level(1);

        $pembahasan1 = Pembahasan::create([
            'level_materi_id' => $level->id,
            'urutan' => 1,
            'nama' => 'Pembahasan 1',
        ]);
        $pembahasan2 = Pembahasan::create([
            'level_materi_id' => $level->id,
            'urutan' => 2,
            'nama' => 'Pembahasan 2',
        ]);

        $soal1 = Soal::create([
            'level_materi_id' => $level->id,
            'pembahasan_id' => $pembahasan1->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Soal 1 di Pembahasan 1',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'A']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
        ]);

        $soal2 = Soal::create([
            'level_materi_id' => $level->id,
            'pembahasan_id' => $pembahasan2->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Soal 1 di Pembahasan 2',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'A']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
        ]);

        $siswa = $this->siswaDenganProgres($level);

        // Anggap soal 1 di Pembahasan 1 sudah tuntas
        JawabanSiswa::create([
            'siswa_id' => $siswa->id,
            'soal_id' => $soal1->id,
            'skor_tertinggi' => 100,
            'exp_diberikan' => 10,
        ]);

        // Siswa klik mulai level dengan pembahasan_id = 1
        $response = $this->actingAs($siswa, 'siswa')
            ->get("/kuis/mulai/{$level->id}?pembahasan_id={$pembahasan1->id}");

        // Harus diarahkan ke soal 2 di pembahasan 2, BUKAN soal 1 lagi!
        $response->assertRedirect()
            ->assertRedirectContains('soal_id='.$soal2->id);

        // Siswa jawab soal 2 -> harus dapat EXP penuh karena soal baru
        $jawabRes = $this->actingAs($siswa, 'siswa')
            ->postJson('/kuis/jawab', ['soal_id' => $soal2->id, 'jawaban' => 'A']);

        $jawabRes->assertOk()
            ->assertJsonPath('benar', true)
            ->assertJsonPath('exp_didapat', 10);
    }

    public function test_pengerjaan_soal_advances_sequentially_without_repeating_completed_soal(): void
    {
        $level = $this->level(1);
        $pembahasan = Pembahasan::create([
            'level_materi_id' => $level->id,
            'urutan' => 1,
            'nama' => 'Pembahasan 1',
        ]);

        $soal1 = Soal::create([
            'level_materi_id' => $level->id,
            'pembahasan_id' => $pembahasan->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Soal 1',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'A']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
        ]);
        $soal2 = Soal::create([
            'level_materi_id' => $level->id,
            'pembahasan_id' => $pembahasan->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Soal 2',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'A']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
        ]);
        $soal3 = Soal::create([
            'level_materi_id' => $level->id,
            'pembahasan_id' => $pembahasan->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Soal 3',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'A']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
        ]);

        $siswa = $this->siswaDenganProgres($level);

        // Siswa menjawab Soal 1
        $res1 = $this->actingAs($siswa, 'siswa')
            ->postJson('/kuis/jawab', ['soal_id' => $soal1->id, 'jawaban' => 'A']);
        $res1->assertOk()
            ->assertJsonPath('next_soal_id', $soal2->id);

        // Siswa menjawab Soal 2
        $res2 = $this->actingAs($siswa, 'siswa')
            ->postJson('/kuis/jawab', ['soal_id' => $soal2->id, 'jawaban' => 'A']);
        $res2->assertOk()
            ->assertJsonPath('next_soal_id', $soal3->id);

        // Pastikan next_url mengarah ke Soal 3, BUKAN Soal 2 lagi!
        $this->assertStringContainsString('soal_id='.$soal3->id, $res2->json('next_url'));

        // Saat mengakses URL Soal 3, progress harus nomor 3 dari 3
        $pageRes = $this->actingAs($siswa, 'siswa')
            ->get($res2->json('next_url'));
        $pageRes->assertOk();
        $pageRes->assertViewHas('progress', ['nomor' => 3, 'total' => 3]);
        $pageRes->assertViewHas('soal.id', $soal3->id);
    }

    public function test_completing_level_even_with_wrong_answers_unlocks_next_level(): void
    {
        $level1 = $this->level(1);
        $level2 = $this->level(2);

        $soal1 = $this->pilihanGanda($level1, 10);
        $soal2 = $this->pilihanGanda($level1, 10);

        $siswa = $this->siswaDenganProgres($level1);
        ProgresSiswa::create([
            'siswa_id' => $siswa->id,
            'level_materi_id' => $level2->id,
            'status' => ProgresSiswa::STATUS_TERKUNCI,
        ]);

        // Soal 1 dijawab SALAH ('B')
        $res1 = $this->actingAs($siswa, 'siswa')
            ->postJson('/kuis/jawab', ['soal_id' => $soal1->id, 'jawaban' => 'B']);
        $res1->assertOk()
            ->assertJsonPath('benar', false)
            ->assertJsonPath('next_soal_id', $soal2->id);

        // Soal 2 dijawab BENAR ('A') -> soal terakhir di level 1
        $res2 = $this->actingAs($siswa, 'siswa')
            ->postJson('/kuis/jawab', ['soal_id' => $soal2->id, 'jawaban' => 'A']);
        $res2->assertOk()
            ->assertJsonPath('benar', true)
            ->assertJsonPath('level_selesai', true)
            ->assertJsonPath('next_level_nama', 'Level 2');

        // Pastikan level 1 status selesai dan level 2 status berjalan (terbuka)
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
    }

    public function test_retrying_completed_level_with_some_wrong_answers_only_serves_wrong_questions(): void
    {
        $level = $this->level(1);
        $soal1 = $this->pilihanGanda($level, 10);
        $soal2 = $this->pilihanGanda($level, 10);
        $soal3 = $this->pilihanGanda($level, 10);

        $siswa = $this->siswaDenganProgres($level, ProgresSiswa::STATUS_SELESAI);

        // Riwayat: Soal 1 salah (skor 0), Soal 2 benar (skor 100), Soal 3 salah (skor 0)
        JawabanSiswa::create(['siswa_id' => $siswa->id, 'soal_id' => $soal1->id, 'skor_tertinggi' => 0, 'exp_diberikan' => 0, 'jumlah_percobaan' => 1]);
        JawabanSiswa::create(['siswa_id' => $siswa->id, 'soal_id' => $soal2->id, 'skor_tertinggi' => 100, 'exp_diberikan' => 10, 'jumlah_percobaan' => 1]);
        JawabanSiswa::create(['siswa_id' => $siswa->id, 'soal_id' => $soal3->id, 'skor_tertinggi' => 0, 'exp_diberikan' => 0, 'jumlah_percobaan' => 1]);

        // Siswa klik mulai level 1 lagi untuk memperbaiki nilai
        $resStart = $this->actingAs($siswa, 'siswa')
            ->get("/kuis/mulai/{$level->id}");

        // Harus diarahkan ke Soal 1 (soal salah pertama)
        $resStart->assertRedirect()
            ->assertRedirectContains('soal_id='.$soal1->id);

        // Siswa jawab Soal 1 dengan BENAR
        $resJawab1 = $this->actingAs($siswa, 'siswa')
            ->postJson('/kuis/jawab', ['soal_id' => $soal1->id, 'jawaban' => 'A']);

        // Harus melompati Soal 2 (karena sudah benar) dan langsung ke Soal 3!
        $resJawab1->assertOk()
            ->assertJsonPath('next_soal_id', $soal3->id);
        $this->assertStringContainsString('soal_id='.$soal3->id, $resJawab1->json('next_url'));

        // Siswa jawab Soal 3 dengan BENAR
        $resJawab3 = $this->actingAs($siswa, 'siswa')
            ->postJson('/kuis/jawab', ['soal_id' => $soal3->id, 'jawaban' => 'A']);

        // Karena semua soal sekarang sudah tuntas 100%, next_soal_id menjadi null
        $resJawab3->assertOk()
            ->assertJsonPath('next_soal_id', null);
    }

    public function test_incomplete_profile_blocks_quiz_and_redirects_with_alert(): void
    {
        $level = $this->level(1);
        $soal = $this->pilihanGanda($level);

        // Siswa yang mendaftar via Google: kelas kosong dan NIS berawalan 'G'
        $siswa = Siswa::factory()->create([
            'kelas' => null,
            'nis' => 'G260928ABCD',
        ]);
        $this->siswaDenganProgres($level); // pastikan progres siap
        ProgresSiswa::create([
            'siswa_id' => $siswa->id,
            'level_materi_id' => $level->id,
            'status' => ProgresSiswa::STATUS_BERJALAN,
        ]);

        // Coba mulai kuis lewat web
        $response = $this->actingAs($siswa, 'siswa')
            ->get("/kuis/mulai/{$level->id}");

        // Harus diredirect kembali ke dashboard dengan flag perlu_lengkapi_profil
        $response->assertRedirect(route('siswa.dashboard'))
            ->assertSessionHas('perlu_lengkapi_profil', true);

        // Coba jawab kuis lewat API
        $apiResponse = $this->actingAs($siswa, 'siswa')
            ->postJson('/kuis/jawab', ['soal_id' => $soal->id, 'jawaban' => 'A']);

        // Harus diblokir dengan status 403
        $apiResponse->assertStatus(403)
            ->assertJsonPath('perlu_lengkapi_profil', true);
    }
}
