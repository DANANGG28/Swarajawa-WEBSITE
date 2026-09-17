<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TtsService
{
    /**
     * Generate TTS using Azure Cognitive Services.
     *
     * @param string $text
     * @return string|null Path ke file audio yang dihasilkan, atau null jika gagal.
     */
    public function generate(string $text): ?string
    {
        $key = config('services.azure_speech.key');
        $region = config('services.azure_speech.region');
        $voice = config('services.azure_speech.voice_default');
        
        if (!$key || !$region) {
            // PRD §6C, §10.2: Mode mock deterministik jika kredensial kosong
            $filename = 'tts_mock_' . Str::random(10) . '.mp3';
            $path = 'soal_tts/' . $filename;
            
            // Dummy MP3 content (1 frame of silence)
            $dummyMp3 = base64_decode('SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjYwLjE2LjEwMAAAAAAAAAAAAAAA//OEAAAAAAAAAAAAAAAAAAAAAAAASW5mbwAAAA8AAAAEAAABIQAwf/8AAAAAT1hn1P//1L/Q8O/j/////////+f88u/f/////////6/p5d23/////////9Pz8u7j/////////8/34+H/////////9vzr4sD/////////2fHiyrT/////////2PLqybb/////////1fLpw6v/////////0fLo/QAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD/+cSAAAAAAAAAAAAAAABXWFhYAAAAAAf884oAERwYAAIAAAAD/Z/UGA0/D6IADyAA8gAB+s/aB1/D7wAPgAA+AAB/h/kC1/C+gAPgAA+AAAB8/6A1/DAwAQ/AAEPwAP/z/wDX8MDAAw8AAMMACP6/4B1/DAwAIPAAAIPAA//N/ANfwwMACDwAAIPAAf/O/AVfwwMACAwAAMDAAP//wBX8MDAAgMAADwwAA//M/AVfwwAACDwAAIPAA//N/AdfwwIAEAwAACAQAB/858Bl/DAQAICAAAgIAAP/zfwFX8MBAAgEAAOAQAD/8z8A1/DAQAYDAAIAAwAA//O/ARfwwcAAAAAAAABwAAf/O/AVfwwEAAgEAAAAQAAP/zfwEX8MDAAAAAAAAAAAAP/zfADX8MDAAAAAAAAAAAA//N/ARfwwMABAMAACAQAA//N/AFfwwEAAAAAAAAAAAAD/858A1/DAQAAAAAAAABgAA//N/AQ/wwYABAEAAIAQAAP/zXwAX8MEAAAAAAAAAIAAA//O/AVfwwcAEAwAAIAQAAH/zvAEH8MEAAAAAAAAAAAD/838BF/DBgAEAQAAgBAAA//NfABfwwQAAAAAAAAAYAA//O/AQfwwcABAMAAAAMAAH/zXwAX8MEAAAAAAAABAAA//N/AhfwwgAAAAAAAAACAAD/8z8AV/DAgAAAAAAAAAgAA//M/ABfwwIAAAABAAAAAAA==');
            Storage::disk('public')->put($path, $dummyMp3);
            
            return $path;
        }

        $url = "https://{$region}.tts.speech.microsoft.com/cognitiveservices/v1";
        
        $ssml = <<<XML
<speak version='1.0' xml:lang='jv-ID'>
    <voice xml:lang='jv-ID' xml:gender='Female' name='{$voice}'>
        {$text}
    </voice>
</speak>
XML;

        try {
            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => $key,
                'Content-Type' => 'application/ssml+xml',
                'X-Microsoft-OutputFormat' => 'audio-16khz-128kbitrate-mono-mp3',
            ])->send('POST', $url, ['body' => $ssml]);

            if ($response->successful()) {
                $filename = 'tts_' . Str::random(10) . '.mp3';
                $path = 'soal_tts/' . $filename;
                
                // Simpan ke storage public
                Storage::disk('public')->put($path, $response->body());
                
                return $path;
            }
        } catch (\Exception $e) {
            // Log::error('TTS Generate Error: ' . $e->getMessage());
        }

        return null;
    }
}
