<?php

namespace App\Services\Ai;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Speech-to-Text Bahasa Jawa (ElevenLabs Scribe, `scribe_v2`) — FR-7.
 *
 * Kode bahasa Jawa di ElevenLabs adalah `jav` (ISO 639-3), bukan `jv-ID`
 * (format BCP-47) — jangan tertukar.
 *
 * Mode mock dipakai saat ELEVENLABS_API_KEY kosong / API gagal. Untuk demo
 * & pengujian, klien boleh mengirim `mock_transcript` agar alur penilaian
 * tetap teruji (PRD §9 mitigasi).
 */
class SttService
{
    public function isConfigured(): bool
    {
        return filled(config('services.elevenlabs.api_key'));
    }

    /**
     * Transkripsi audio base64 (kompatibel dengan endpoint speech yang ada).
     *
     * @return array{mock: bool, language: string, transcript: ?string, text: ?string, confidence: ?float, error: ?string}
     */
    public function transcribe(string $audioBase64, ?string $language = null, ?string $mockTranscript = null): array
    {
        $language ??= config('services.elevenlabs.language', 'jav');

        if (! $this->isConfigured()) {
            return $this->mock($language, $mockTranscript);
        }

        $audioBase64 = preg_replace('/^data:[^;]+;base64,/', '', $audioBase64) ?? $audioBase64;
        $decoded = base64_decode($audioBase64, true);

        if ($decoded === false || $decoded === '') {
            return $this->mock($language, $mockTranscript, 'Audio base64 tidak valid.');
        }

        $tmpPath = tempnam(sys_get_temp_dir(), 'sinau_stt_');

        if ($tmpPath === false) {
            return $this->mock($language, $mockTranscript, 'Gagal membuat berkas sementara.');
        }

        file_put_contents($tmpPath, $decoded);

        try {
            return $this->transcribeFile($tmpPath, $language, $mockTranscript);
        } finally {
            @unlink($tmpPath);
        }
    }

    /**
     * Transkripsi berkas audio (UploadedFile atau path lokal).
     *
     * @return array{mock: bool, language: string, transcript: ?string, text: ?string, confidence: ?float, error: ?string}
     */
    public function transcribeFile(UploadedFile|string $audio, string $languageCode = 'jav', ?string $mockTranscript = null): array
    {
        if (! $this->isConfigured()) {
            return $this->mock($languageCode, $mockTranscript);
        }

        $filePath = $audio instanceof UploadedFile ? $audio->getRealPath() : $audio;
        $fileName = $audio instanceof UploadedFile ? $audio->getClientOriginalName() : basename($audio);

        try {
            $response = Http::withHeaders([
                'xi-api-key' => config('services.elevenlabs.api_key'),
            ])->timeout(60)->attach(
                'file', fopen($filePath, 'r'), $fileName
            )->post('https://api.elevenlabs.io/v1/speech-to-text', [
                'model_id' => config('services.elevenlabs.stt_model', 'scribe_v2'),
                'language_code' => $languageCode,
            ]);

            if ($response->failed()) {
                Log::warning('ElevenLabs STT gagal', ['status' => $response->status()]);

                return $this->mock($languageCode, $mockTranscript, 'ElevenLabs STT gagal: '.$response->status());
            }

            $data = $response->json();
            $text = (string) ($data['text'] ?? '');

            return [
                'mock' => false,
                'language' => $data['language_code'] ?? $languageCode,
                'transcript' => $text,
                'text' => $text,
                'confidence' => isset($data['language_probability']) ? (float) $data['language_probability'] : null,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::warning('ElevenLabs STT exception', ['message' => $e->getMessage()]);

            return $this->mock($languageCode, $mockTranscript, $e->getMessage());
        }
    }

    /**
     * @return array{mock: bool, language: string, transcript: ?string, text: ?string, confidence: ?float, error: ?string}
     */
    private function mock(string $language, ?string $mockTranscript, ?string $error = null): array
    {
        return [
            'mock' => true,
            'language' => $language,
            'transcript' => $mockTranscript,
            'text' => $mockTranscript,
            'confidence' => $mockTranscript !== null ? 1.0 : null,
            'error' => $error,
        ];
    }
}
