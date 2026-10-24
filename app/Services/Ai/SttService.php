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
        return filled(config('services.groq.api_key')) || filled(config('services.elevenlabs.api_key'));
    }

    /**
     * `mock_transcript` hanya untuk demo lokal & pengujian. Di produksi nilai
     * dari klien diabaikan agar siswa tidak bisa memalsukan hasil transkripsi.
     */
    private function resolveMockTranscript(?string $mockTranscript): ?string
    {
        return app()->environment('production') ? null : $mockTranscript;
    }

    /**
     * Transkripsi audio base64 (kompatibel dengan endpoint speech yang ada).
     *
     * @return array{mock: bool, language: string, transcript: ?string, text: ?string, confidence: ?float, error: ?string}
     */
    public function transcribe(string $audioBase64, ?string $language = null, ?string $mockTranscript = null): array
    {
        $language ??= config('services.elevenlabs.language', 'jav');
        $mockTranscript = $this->resolveMockTranscript($mockTranscript);

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
        $mockTranscript = $this->resolveMockTranscript($mockTranscript);

        if (! $this->isConfigured()) {
            return $this->mock($languageCode, $mockTranscript);
        }

        $driver = config('services.stt.driver', 'groq');
        $hasGroq = filled(config('services.groq.api_key'));
        $hasElevenLabs = filled(config('services.elevenlabs.api_key'));
        $lastError = null;

        if ($driver === 'groq' && $hasGroq) {
            $res = $this->transcribeWithGroq($audio, $languageCode, $mockTranscript);
            if (($res['success'] ?? false) === true) {
                unset($res['success']);
                return $res;
            }
            $lastError = $res['error'] ?? null;
            Log::info('Groq STT gagal, mencoba fallback ke ElevenLabs jika tersedia.');
        }

        if ($hasElevenLabs) {
            return $this->transcribeWithElevenLabs($audio, $languageCode, $mockTranscript);
        }

        return $this->mock($languageCode, $mockTranscript, $lastError ?? 'STT provider tidak merespons.');
    }

    /**
     * Transkripsi via Groq Whisper LPU (~250ms).
     */
    public function transcribeWithGroq(UploadedFile|string $audio, string $languageCode = 'jv', ?string $mockTranscript = null): array
    {
        $filePath = $audio instanceof UploadedFile ? $audio->getRealPath() : $audio;
        $fileName = $audio instanceof UploadedFile ? $audio->getClientOriginalName() : basename($audio);
        $groqLang = ($languageCode === 'jav' || $languageCode === 'jw') ? 'jv' : $languageCode;
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.groq.api_key'),
            ])->timeout(30)->attach(
                'file', fopen($filePath, 'r'), $fileName
            )->post('https://api.groq.com/openai/v1/audio/transcriptions', [
                'model' => config('services.groq.whisper_model', 'whisper-large-v3'),
                'language' => $groqLang,
                'temperature' => 0,
                'response_format' => 'json',
            ]);

            if ($response->failed()) {
                Log::warning('Groq Whisper STT gagal', ['status' => $response->status(), 'body' => $response->body()]);
                return [
                    'success' => false,
                    'error' => 'Groq Whisper gagal: ' . $response->status(),
                ];
            }

            $data = $response->json();
            $text = trim((string) ($data['text'] ?? ''));

            return [
                'success' => true,
                'mock' => false,
                'language' => $languageCode,
                'transcript' => $text,
                'text' => $text,
                'confidence' => 0.95,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::warning('Groq Whisper STT exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Transkripsi via ElevenLabs Scribe.
     */
    public function transcribeWithElevenLabs(UploadedFile|string $audio, string $languageCode = 'jav', ?string $mockTranscript = null): array
    {
        $mockTranscript = $this->resolveMockTranscript($mockTranscript);
        $filePath = $audio instanceof UploadedFile ? $audio->getRealPath() : $audio;
        $fileName = $audio instanceof UploadedFile ? $audio->getClientOriginalName() : basename($audio);
        $elLang = $languageCode === 'jw' ? 'jav' : $languageCode;

        try {
            $response = Http::withHeaders([
                'xi-api-key' => config('services.elevenlabs.api_key'),
            ])->timeout(60)->attach(
                'file', fopen($filePath, 'r'), $fileName
            )->post('https://api.elevenlabs.io/v1/speech-to-text', [
                'model_id' => config('services.elevenlabs.stt_model', 'scribe_v2'),
                'language_code' => $elLang,
            ]);

            if ($response->failed()) {
                Log::warning('ElevenLabs STT gagal', ['status' => $response->status()]);
                return $this->mock($languageCode, $mockTranscript, 'ElevenLabs STT gagal: ' . $response->status());
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
