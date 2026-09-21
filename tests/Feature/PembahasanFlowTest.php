<?php

namespace Tests\Feature;

use App\Models\LevelMateri;
use App\Models\Pembahasan;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Models\Soal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembahasanFlowTest extends TestCase
{
    use RefreshDatabase;

    private function level(): LevelMateri
    {
        return LevelMateri::create([
            'nama_materi' => 'Dasar',
            'deskripsi' => 'Deskripsi',
            'reward_exp' => 100,
            'urutan' => 1,
        ]);
    }

    private function pembahasan(LevelMateri $level, string $nama, int $urutan): Pembahasan
    {
        return Pembahasan::create([
            'level_materi_id' => $level->id,
            'nama' => $nama,
            'urutan' => $urutan,
        ]);
    }

    private function soal(LevelMateri $level, Pembahasan $pembahasan, string $pertanyaan): Soal
    {
        return Soal::create([
            'level_materi_id' => $level->id,
            'pembahasan_id' => $pembahasan->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => $pertanyaan,
            'opsi_jawaban' => [
                ['label' => 'A', 'teks' => 'Sugeng enjing'],
                ['label' => 'B', 'teks' => 'Sugeng dalu'],
            ],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
        ]);
    }

    private function siswaDenganProgres(LevelMateri $level): Siswa
    {
        $siswa = Siswa::factory()->create(['nama_lengkap' => 'Uji Pembahasan', 'kelas' => '7A']);
        $siswa->exp()->create(['total_exp' => 0]);
        $siswa->strek()->create(['current_streak' => 0, 'highest_streak' => 0]);
        ProgresSiswa::create([
            'siswa_id' => $siswa->id,
            'level_materi_id' => $level->id,
            'status' => ProgresSiswa::STATUS_BERJALAN,
        ]);

        return $siswa;
    }

    public function test_dashboard_menampilkan_level_dan_pembahasan(): void
    {
        $level = $this->level();
        $p1 = $this->pembahasan($level, 'Salam & Sapaan', 1);
        $p2 = $this->pembahasan($level, 'Tembung lan Ukara Dasar', 2);
        $this->soal($level, $p1, 'Pitakon salam?');
        $this->soal($level, $p2, 'Pitakon tembung?');

        $siswa = $this->siswaDenganProgres($level);

        $this->actingAs($siswa, 'siswa')->get('/')
            ->assertOk()
            ->assertSee('Uji Pembahasan')
            ->assertSee('Dasar')
            ->assertSee('Salam & Sapaan')
            ->assertSee('Tembung lan Ukara Dasar');
    }

    public function test_mulai_level_dengan_pembahasan_mengarah_ke_soal_pembahasan(): void
    {
        $level = $this->level();
        $p1 = $this->pembahasan($level, 'Salam & Sapaan', 1);
        $p2 = $this->pembahasan($level, 'Tembung lan Ukara Dasar', 2);
        $this->soal($level, $p1, 'Pitakon salam?');
        $soalP2 = $this->soal($level, $p2, 'Pitakon tembung?');

        $siswa = $this->siswaDenganProgres($level);

        $this->actingAs($siswa, 'siswa')
            ->get("/kuis/mulai/{$level->id}?pembahasan_id={$p2->id}")
            ->assertRedirect()
            ->assertRedirectContains('soal_id='.$soalP2->id);
    }

    public function test_soal_berikutnya_tetap_dalam_pembahasan_yang_sama(): void
    {
        $level = $this->level();
        $p1 = $this->pembahasan($level, 'Salam & Sapaan', 1);
        $p2 = $this->pembahasan($level, 'Tembung lan Ukara Dasar', 2);
        $soalP1a = $this->soal($level, $p1, 'Pitakon salam 1?');
        $soalP1b = $this->soal($level, $p1, 'Pitakon salam 2?');
        $this->soal($level, $p2, 'Pitakon tembung?');

        $siswa = $this->siswaDenganProgres($level);

        // Selesaikan soal pertama di pembahasan 1 -> lanjut ke soal kedua pembahasan 1.
        $this->actingAs($siswa, 'siswa')->postJson('/kuis/jawab', [
            'soal_id' => $soalP1a->id,
            'jawaban' => 'A',
        ])->assertOk()->assertJson(['next_soal_id' => $soalP1b->id]);

        // Selesaikan soal kedua -> pembahasan 1 rampung, tidak lompat ke pembahasan lain.
        $this->actingAs($siswa, 'siswa')->postJson('/kuis/jawab', [
            'soal_id' => $soalP1b->id,
            'jawaban' => 'A',
        ])->assertOk()->assertJson(['next_soal_id' => null]);
    }
}
