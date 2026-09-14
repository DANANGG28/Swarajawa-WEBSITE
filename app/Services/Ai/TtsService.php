<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Text-to-Speech Bahasa Jawa (Azure AI Speech) — FR-6.
 *
 * Bila kredensial Azure kosong, service mengembalikan respons mock
 * deterministik agar demo tetap berjalan tanpa API key (PRD §9 mitigasi).
 */
class TtsService
{
    public function isConfigured(): bool
    {
        return filled(config('services.azure_speech.key'));
    }

    /**
     * @return array{mock: bool, voice: string, mime: string, text: string, audio_base64: ?string, error: ?string}
     */
    public function synthesize(string $text, ?string $voice = null, string $locale = 'jv-ID'): array
    {
        $voice ??= config('services.azure_speech.voice_default', 'jv-ID-SitiNeural');

        if (! $this->isConfigured()) {
            return $this->mock($text, $voice);
        }

        $region = config('services.azure_speech.region', 'southeastasia');
        $endpoint = "https://{$region}.tts.speech.microsoft.com/cognitiveservices/v1";

        try {
            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => config('services.azure_speech.key'),
                'Content-Type' => 'application/ssml+xml',
                'X-Microsoft-OutputFormat' => 'audio-24khz-48kbitrate-mono-mp3',
                'User-Agent' => 'SinauJowo',
            ])
                ->timeout(15)
                ->withBody($this->ssml($text, $voice, $locale), 'application/ssml+xml')
                ->post($endpoint);

            if ($response->failed()) {
                Log::warning('Azure TTS gagal', ['status' => $response->status()]);

                return $this->mock($text, $voice, 'Azure TTS gagal: '.$response->status());
            }

            return [
                'mock' => false,
                'voice' => $voice,
                'mime' => 'audio/mpeg',
                'text' => $text,
                'audio_base64' => base64_encode($response->body()),
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::warning('Azure TTS exception', ['message' => $e->getMessage()]);

            return $this->mock($text, $voice, $e->getMessage());
        }
    }

    private function ssml(string $text, string $voice, string $locale): string
    {
        $escaped = htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        return <<<SSML
        <speak version="1.0" xmlns="http://www.w3.org/2001/10/synthesis" xml:lang="{$locale}">
            <voice name="{$voice}">{$escaped}</voice>
        </speak>
        SSML;
    }

    /**
     * @return array{mock: bool, voice: string, mime: string, text: string, audio_base64: ?string, error: ?string}
     */
    private function mock(string $text, string $voice, ?string $error = null): array
    {
        return [
            'mock' => true,
            'voice' => $voice,
            'mime' => 'audio/mpeg',
            'text' => $text,
            'audio_base64' => null,
            'error' => $error,
        ];
    }
}
