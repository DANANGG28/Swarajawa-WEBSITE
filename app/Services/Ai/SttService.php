<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Speech-to-Text Bahasa Jawa (Google Cloud Speech-to-Text, `jv-ID`) — FR-7.
 *
 * Mode mock dipakai saat GOOGLE_SPEECH_KEY kosong. Untuk kebutuhan demo/uji,
 * klien boleh mengirim `mock_transcript` agar alur penilaian tetap teruji.
 */
class SttService
{
    public function isConfigured(): bool
    {
        return filled(config('services.google_speech.key'));
    }

    /**
     * @return array{mock: bool, language: string, transcript: ?string, confidence: ?float, error: ?string}
     */
    public function transcribe(string $audioBase64, ?string $language = null, ?string $mockTranscript = null): array
    {
        $language ??= config('services.google_speech.language', 'jv-ID');

        if (! $this->isConfigured()) {
            return [
                'mock' => true,
                'language' => $language,
                'transcript' => $mockTranscript,
                'confidence' => $mockTranscript !== null ? 1.0 : null,
                'error' => null,
            ];
        }

        $key = config('services.google_speech.key');
        $endpoint = "https://speech.googleapis.com/v1/speech:recognize?key={$key}";

        try {
            $response = Http::timeout(20)->post($endpoint, [
                'config' => [
                    'languageCode' => $language,
                    'enableAutomaticPunctuation' => true,
                ],
                'audio' => [
                    'content' => $audioBase64,
                ],
            ]);

            if ($response->failed()) {
                Log::warning('Google STT gagal', ['status' => $response->status()]);

                return [
                    'mock' => true,
                    'language' => $language,
                    'transcript' => $mockTranscript,
                    'confidence' => null,
                    'error' => 'Google STT gagal: '.$response->status(),
                ];
            }

            $alternative = $response->json('results.0.alternatives.0');

            return [
                'mock' => false,
                'language' => $language,
                'transcript' => $alternative['transcript'] ?? null,
                'confidence' => isset($alternative['confidence']) ? (float) $alternative['confidence'] : null,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::warning('Google STT exception', ['message' => $e->getMessage()]);

            return [
                'mock' => true,
                'language' => $language,
                'transcript' => $mockTranscript,
                'confidence' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
}
