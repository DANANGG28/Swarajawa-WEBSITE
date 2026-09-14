<?php

namespace App\Services\Ai;

use App\Support\TextSimilarity;

/**
 * Speech-to-Speech Bahasa Jawa — FR-8 (Opsi A: STT → Modul Pemrosesan → TTS).
 *
 * Modul pemrosesan pada MVP sengaja tidak memakai LLM generatif bebas,
 * melainkan mencocokkan hasil STT dengan kunci jawaban lalu memilih respons
 * terstruktur (benar/salah/koreksi) — sesuai PRD §6C.
 */
class StsService
{
    public function __construct(
        private readonly SttService $stt,
        private readonly TtsService $tts,
    ) {}

    /**
     * @return array{
     *     mock: bool,
     *     transcript: ?string,
     *     skor: ?int,
     *     benar: bool,
     *     balasan_teks: string,
     *     balasan_audio_base64: ?string,
     *     voice: string
     * }
     */
    public function respond(string $audioBase64, string $expectedText, ?string $mockTranscript = null): array
    {
        $stt = $this->stt->transcribe($audioBase64, mockTranscript: $mockTranscript);
        $transcript = $stt['transcript'];

        $skor = $transcript !== null ? (int) round(TextSimilarity::percent($transcript, $expectedText) * 100) : null;
        $benar = $skor !== null && $skor >= 70;

        $balasan = match (true) {
            $transcript === null => 'Nyuwun pangapunten, swantenipun dereng saged dipunmirengaken. Sumangga dipuncobi malih.',
            $benar => 'Sae sanget! Pangucapan panjenengan sampun leres.',
            default => 'Dereng leres, sumangga dipunulang malih kanthi lafal ingkang cetha.',
        };

        $tts = $this->tts->synthesize($balasan);

        return [
            'mock' => $stt['mock'] || $tts['mock'],
            'transcript' => $transcript,
            'skor' => $skor,
            'benar' => $benar,
            'balasan_teks' => $balasan,
            'balasan_audio_base64' => $tts['audio_base64'],
            'voice' => $tts['voice'],
        ];
    }
}
