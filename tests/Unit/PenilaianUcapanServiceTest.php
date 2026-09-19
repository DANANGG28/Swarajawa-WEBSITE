<?php

namespace Tests\Unit;

use App\Services\Ai\PenilaianUcapanService;
use Tests\TestCase;

class PenilaianUcapanServiceTest extends TestCase
{
    private PenilaianUcapanService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PenilaianUcapanService;
    }

    public function test_identical_text_scores_one_and_is_benar(): void
    {
        $skor = $this->service->hitungKemiripan('Roro Jonggrang', 'roro jonggrang');

        $this->assertSame(1.0, $skor);
        $this->assertSame(PenilaianUcapanService::BENAR, $this->service->kategorikan($skor));
    }

    public function test_close_match_is_hampir_benar(): void
    {
        $skor = $this->service->hitungKemiripan('roro jonggrang', 'jonggrang');

        $this->assertGreaterThanOrEqual(0.6, $skor);
        $this->assertLessThan(0.85, $skor);
        $this->assertSame(PenilaianUcapanService::HAMPIR_BENAR, $this->service->kategorikan($skor));
    }

    public function test_unrelated_text_is_salah(): void
    {
        $skor = $this->service->hitungKemiripan('resep kue coklat', 'roro jonggrang');

        $this->assertLessThan(0.6, $skor);
        $this->assertSame(PenilaianUcapanService::SALAH, $this->service->kategorikan($skor));
    }

    public function test_threshold_boundaries_map_to_categories(): void
    {
        $this->assertSame(PenilaianUcapanService::BENAR, $this->service->kategorikan(0.85));
        $this->assertSame(PenilaianUcapanService::HAMPIR_BENAR, $this->service->kategorikan(0.6));
        $this->assertSame(PenilaianUcapanService::SALAH, $this->service->kategorikan(0.59));
    }
}
