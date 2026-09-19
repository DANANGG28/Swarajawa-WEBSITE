<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Text-to-Speech Bahasa Jawa (edge-tts, voice `jv-ID-DimasNeural`) — FR-6.
 *
 * edge-tts adalah endpoint gratis "Read Aloud" Microsoft Edge (mesin Azure
 * Neural TTS yang sama), dipanggil lewat `exec()`. Tidak butuh API key.
 *
 * Mode mock deterministik (audio hening) dipakai bila edge-tts gagal/tidak
 * tersedia agar demo tetap berjalan (PRD §9 mitigasi).
 */
class TtsService
{
    /**
     * Frame MP3 hening (1 frame) sebagai fallback demo.
     */
    private const SILENT_MP3_BASE64 = 'SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjYwLjE2LjEwMAAAAAAAAAAAAAAA//OEAAAAAAAAAAAAAAAAAAAAAAAASW5mbwAAAA8AAAAEAAABIQAwf/8AAAAAT1hn1P//1L/Q8O/j/////////+f88u/f/////////6/p5d23/////////9Pz8u7j/////////8/34+H/////////9vzr4sD/////////2fHiyrT/////////2PLqybb/////////1fLpw6v/////////0fLo/QAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD/+cSAAAAAAAAAAAAAAABXWFhYAAAAAAf884oAERwYAAIAAAAD/Z/UGA0/D6IADyAA8gAB+s/aB1/D7wAPgAA+AAB/h/kC1/C+gAPgAA+AAAB8/6A1/DAwAQ/AAEPwAP/z/wDX8MDAAw8AAMMACP6/4B1/DAwAIPAAAIPAA//N/ANfwwMACDwAAIPAAf/O/AVfwwMACAwAAMDAAP//wBX8MDAAgMAADwwAA//M/AVfwwAACDwAAIPAA//N/AdfwwIAEAwAACAQAB/858Bl/DAQAICAAAgIAAP/zfwFX8MBAAgEAAOAQAD/8z8A1/DAQAYDAAIAAwAA//O/ARfwwcAAAAAAAABwAAf/O/AVfwwEAAgEAAAAQAAP/zfwEX8MDAAAAAAAAAAAAP/zfADX8MDAAAAAAAAAAAA//N/ARfwwMABAMAACAQAA//N/AFfwwEAAAAAAAAAAAAD/858A1/DAQAAAAAAAABgAA//N/AQ/wwYABAEAAIAQAAP/zXwAX8MEAAAAAAAAAIAAA//O/AVfwwcAEAwAAIAQAAH/zvAEH8MEAAAAAAAAAAAD/838BF/DBgAEAQAAgBAAA//NfABfwwQAAAAAAAAAYAA//O/AQfwwcABAMAAAAMAAH/zXwAX8MEAAAAAAAABAAA//N/AhfwwgAAAAAAAAACAAD/8z8AV/DAgAAAAAAAAAgAA//M/ABfwwIAAAABAAAAAAA==';

    public function isConfigured(): bool
    {
        return filled(config('services.edge_tts.binary'));
    }

    /**
     * Generate audio dari teks, simpan ke storage, dan kembalikan path relatif.
     */
    public function synthesizeToPath(string $text, ?string $voice = null, string $directory = 'tts'): string
    {
        $text = trim($text);

        if ($text === '') {
            throw new RuntimeException('Teks TTS kosong.');
        }

        if (mb_strlen($text) > 1000) {
            $text = mb_substr($text, 0, 1000);
        }

        $voice ??= config('services.edge_tts.voice_default', 'jv-ID-DimasNeural');

        $directory = trim($directory, '/');
        $relative = $directory.'/'.Str::uuid().'.mp3';

        Storage::disk('public')->makeDirectory($directory, 0775, true, true);
        $absolute = Storage::disk('public')->path($relative);
        @chmod(dirname($absolute), 0775);

        $command = sprintf(
            '%s --voice %s --text %s --write-media %s 2>&1',
            (string) config('services.edge_tts.binary', 'edge-tts'),
            escapeshellarg((string) $voice),
            escapeshellarg($text),
            escapeshellarg($absolute),
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || ! is_file($absolute) || filesize($absolute) === 0) {
            @unlink($absolute);

            throw new RuntimeException('edge-tts gagal generate audio: '.implode("\n", $output));
        }

        return $relative;
    }

    /**
     * Generate audio (base64 + URL) untuk konsumsi web/mobile.
     *
     * @return array{mock: bool, voice: string, mime: string, text: string, audio_base64: ?string, path: ?string, audio_url: ?string, error: ?string}
     */
    public function synthesize(string $text, ?string $voice = null, string $locale = 'jv-ID', string $directory = 'tts'): array
    {
        $voice ??= config('services.edge_tts.voice_default', 'jv-ID-DimasNeural');

        try {
            $relative = $this->synthesizeToPath($text, $voice, $directory);
            $audio = (string) Storage::disk('public')->get($relative);

            return [
                'mock' => false,
                'voice' => $voice,
                'mime' => 'audio/mpeg',
                'text' => $text,
                'audio_base64' => base64_encode($audio),
                'path' => $relative,
                'audio_url' => Storage::disk('public')->url($relative),
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::warning('edge-tts gagal', ['message' => $e->getMessage()]);

            return $this->mock($text, $voice, $e->getMessage(), $directory);
        }
    }

    /**
     * @return array{mock: bool, voice: string, mime: string, text: string, audio_base64: ?string, path: ?string, audio_url: ?string, error: ?string}
     */
    private function mock(string $text, string $voice, ?string $error = null, string $directory = 'tts'): array
    {
        $directory = trim($directory, '/');
        $relative = $directory.'/mock_'.Str::uuid().'.mp3';
        Storage::disk('public')->makeDirectory($directory, 0775, true, true);
        Storage::disk('public')->put($relative, base64_decode(self::SILENT_MP3_BASE64));

        if (! Storage::disk('public')->exists($relative)) {
            throw new RuntimeException('Gagal menyimpan audio TTS — cek izin tulis storage/app/public.');
        }

        $audio = (string) Storage::disk('public')->get($relative);

        return [
            'mock' => true,
            'voice' => $voice,
            'mime' => 'audio/mpeg',
            'text' => $text,
            'audio_base64' => base64_encode($audio),
            'path' => $relative,
            'audio_url' => Storage::disk('public')->url($relative),
            'error' => $error,
        ];
    }
}
