<?php

namespace Tests\Unit;

use App\Models\Exp;
use App\Models\JawabanSiswa;
use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Strek;
use App\Services\BadgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BadgeServiceTest extends TestCase
{
    use RefreshDatabase;

    private BadgeService $badgeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->badgeService = new BadgeService();
    }

    public function test_new_student_starts_with_zero_earned_badges(): void
    {
        $siswa = Siswa::factory()->create();

        $result = $this->badgeService->getBadgesForSiswa($siswa);

        $this->assertIsArray($result);
        $this->assertSame(0, $result['earned_count']);
        $this->assertGreaterThan(0, $result['total_count']);
        $this->assertCount(0, $result['earned']);
        $this->assertCount($result['total_count'], $result['locked']);
        $this->assertSame(0, $result['completion_percentage']);
    }

    public function test_student_unlocks_wicara_badge_after_meeting_criteria(): void
    {
        $siswa = Siswa::factory()->create();
        $level = LevelMateri::create([
            'nama_materi' => 'Dasar',
            'deskripsi' => 'Level dasar',
            'reward_exp' => 100,
            'urutan' => 1,
        ]);

        // Create 3 Wicara questions and answer them successfully
        for ($i = 1; $i <= 3; $i++) {
            $soal = Soal::create([
                'level_materi_id' => $level->id,
                'tipe_soal' => Soal::TIPE_KUIS_SUARA,
                'pertanyaan' => "Kuis suara {$i}",
                'opsi_jawaban' => ['instruksi' => 'Bicara'],
                'kunci_jawaban' => ['teks' => 'sugeng enjing'],
                'bobot_exp' => 20,
            ]);

            JawabanSiswa::create([
                'siswa_id' => $siswa->id,
                'soal_id' => $soal->id,
                'skor_tertinggi' => 100,
                'exp_diberikan' => 20,
                'jumlah_percobaan' => 1,
            ]);
        }

        $result = $this->badgeService->getBadgesForSiswa($siswa);

        $earnedIds = array_column($result['earned'], 'id');
        $this->assertContains('wicara_prigel', $earnedIds);
        $this->assertGreaterThanOrEqual(1, $result['earned_count']);

        $wicaraBadge = collect($result['earned'])->firstWhere('id', 'wicara_prigel');
        $this->assertNotNull($wicaraBadge);
        $this->assertSame('Diraih', $wicaraBadge['earned_stat_right']);
        $this->assertStringNotContainsString('+', $wicaraBadge['earned_stat_right']);
    }

    public function test_student_unlocks_streak_badge(): void
    {
        $siswa = Siswa::factory()->create();
        Strek::create([
            'siswa_id' => $siswa->id,
            'current_streak' => 3,
            'highest_streak' => 3,
            'last_activity_date' => now(),
        ]);

        $result = $this->badgeService->getBadgesForSiswa($siswa);
        $earnedIds = array_column($result['earned'], 'id');

        $this->assertContains('gathutkaca_streak_master', $earnedIds);

        $streakBadge = collect($result['earned'])->firstWhere('id', 'gathutkaca_streak_master');
        $this->assertNotNull($streakBadge);
        $this->assertSame('Diraih', $streakBadge['earned_stat_right']);
        $this->assertStringNotContainsString('+', $streakBadge['earned_stat_right']);
    }

    public function test_student_unlocks_exp_badge(): void
    {
        $siswa = Siswa::factory()->create();
        Exp::create([
            'siswa_id' => $siswa->id,
            'total_exp' => 300,
        ]);

        $result = $this->badgeService->getBadgesForSiswa($siswa);
        $earnedIds = array_column($result['earned'], 'id');

        $this->assertContains('wasasis_utama', $earnedIds);

        $expBadge = collect($result['earned'])->firstWhere('id', 'wasasis_utama');
        $this->assertNotNull($expBadge);
        $this->assertSame('Diraih', $expBadge['earned_stat_right']);
        $this->assertStringNotContainsString('+', $expBadge['earned_stat_right']);
    }
}
