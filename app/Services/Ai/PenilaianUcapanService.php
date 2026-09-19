<?php

namespace App\Services\Ai;

use App\Support\TextSimilarity;

/**
 * Penilaian ucapan Quiz Suara — fuzzy string matching, non-LLM (PRD §6C).
 *
 * Membandingkan hasil STT dengan kunci jawaban memakai Levenshtein
 * (dinormalisasi panjang) lalu memetakan ke kategori tetap.
 */
class PenilaianUcapanService
{
    public const BENAR = 'benar';

    public const HAMPIR_BENAR = 'hampir_benar';

    public const SALAH = 'salah';

    /**
     * Skor kemiripan 0.0 - 1.0 antara hasil STT dan kunci jawaban.
     */
    public function hitungKemiripan(string $hasilStt, string $kunciJawaban): float
    {
        return TextSimilarity::percent($hasilStt, $kunciJawaban);
    }

    /**
     * Tentukan kategori hasil berdasarkan skor.
     *
     * Threshold 0.85/0.6 adalah titik awal — sesuaikan setelah uji dengan
     * sampel suara nyata (lihat Bagian 5 & 9 setup-tts-stt-live.md).
     */
    public function kategorikan(float $skor): string
    {
        return match (true) {
            $skor >= 0.85 => self::BENAR,
            $skor >= 0.6 => self::HAMPIR_BENAR,
            default => self::SALAH,
        };
    }
}
