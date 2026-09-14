<?php

namespace Tests\Unit;

use App\Models\Soal;
use App\Services\QuizScoringService;
use Tests\TestCase;

class QuizScoringServiceTest extends TestCase
{
    private QuizScoringService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new QuizScoringService;
    }

    public function test_pilihan_ganda_scoring(): void
    {
        $soal = new Soal([
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'opsi_jawaban' => [
                ['label' => 'A', 'teks' => 'Sugeng enjing'],
                ['label' => 'B', 'teks' => 'Sugeng dalu'],
            ],
            'kunci_jawaban' => ['jawaban' => 'A'],
        ]);

        $this->assertSame(100, $this->service->score($soal, 'A')['skor']);
        $this->assertTrue($this->service->score($soal, 'A')['benar']);
        $this->assertSame(0, $this->service->score($soal, 'B')['skor']);
    }

    public function test_susun_kalimat_scoring(): void
    {
        $soal = new Soal([
            'tipe_soal' => Soal::TIPE_SUSUN_KALIMAT,
            'kunci_jawaban' => ['susunan' => ['Aku', 'mangan', 'sega', 'goreng']],
        ]);

        $this->assertTrue($this->service->score($soal, ['Aku', 'mangan', 'sega', 'goreng'])['benar']);
        $this->assertFalse($this->service->score($soal, ['mangan', 'Aku', 'sega', 'goreng'])['benar']);
    }

    public function test_pencocokan_arti_scoring(): void
    {
        $soal = new Soal([
            'tipe_soal' => Soal::TIPE_PENCOCOKAN_ARTI,
            'kunci_jawaban' => ['pasangan' => ['mangan' => 'makan', 'turu' => 'tidur']],
        ]);

        $this->assertSame(100, $this->service->score($soal, ['mangan' => 'makan', 'turu' => 'tidur'])['skor']);
        $this->assertSame(50, $this->service->score($soal, ['mangan' => 'makan', 'turu' => 'salah'])['skor']);
    }

    public function test_puzzle_pakaian_adat_scoring(): void
    {
        $soal = new Soal([
            'tipe_soal' => Soal::TIPE_PUZZLE_PAKAIAN_ADAT,
            'kunci_jawaban' => ['urutan' => [1, 2, 3, 4]],
        ]);

        $this->assertTrue($this->service->score($soal, [1, 2, 3, 4])['benar']);
        $this->assertFalse($this->service->score($soal, [2, 1, 3, 4])['benar']);
    }

    public function test_menulis_aksara_perfect_match_scores_high(): void
    {
        $circle = $this->circlePath();
        $soal = new Soal([
            'tipe_soal' => Soal::TIPE_MENULIS_AKSARA,
            'kunci_jawaban' => ['paths' => [$circle]],
        ]);

        $hasil = $this->service->score($soal, ['strokes' => [$circle]]);

        $this->assertGreaterThanOrEqual(90, $hasil['skor']);
        $this->assertTrue($hasil['benar']);
    }

    public function test_kuis_suara_similarity_scoring(): void
    {
        $soal = new Soal([
            'tipe_soal' => Soal::TIPE_KUIS_SUARA,
            'kunci_jawaban' => ['teks' => 'kula badhe tindak sekolah'],
        ]);

        $this->assertTrue($this->service->score($soal, 'kula badhe tindak sekolah')['benar']);
        $this->assertFalse($this->service->score($soal, 'resep kue coklat')['benar']);
    }

    /**
     * @return array<int, array{0: float, 1: float}>
     */
    private function circlePath(): array
    {
        $points = [];
        for ($i = 0; $i <= 24; $i++) {
            $angle = 2 * M_PI * $i / 24;
            $points[] = [round(0.5 + 0.3 * cos($angle), 4), round(0.5 + 0.3 * sin($angle), 4)];
        }

        return $points;
    }
}
