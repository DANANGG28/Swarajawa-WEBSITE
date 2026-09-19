<?php

namespace App\Services;

use App\Services\Ai\TtsService as EdgeTtsService;
use Illuminate\Support\Facades\Log;

/**
 * Text-to-Speech untuk media soal (guru/superadmin) — FR-6.
 *
 * Diteruskan ke provider edge-tts yang sama dengan kuis suara; menyimpan
 * hasil di `soal_tts/` lalu mengembalikan path relatif disk `public`.
 */
class TtsService
{
    public function __construct(private readonly EdgeTtsService $tts) {}

    /**
     * @return string|null Path relatif file audio, atau null jika gagal.
     */
    public function generate(string $text): ?string
    {
        try {
            $result = $this->tts->synthesize($text, directory: 'soal_tts');

            return $result['path'] ?? null;
        } catch (\Throwable $e) {
            Log::warning('TTS media soal gagal', ['message' => $e->getMessage()]);

            return null;
        }
    }
}
