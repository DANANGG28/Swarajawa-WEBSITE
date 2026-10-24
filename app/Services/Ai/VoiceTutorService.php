<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Service Voice Tutor Basa Jawa ("Si Swara") — Interaksi dua arah ala GoPrep.gg Preppy AI.
 * Menghasilkan balasan ucapan, ekspresi avatar, tantangan peniruan lafal, dan kosakata baru.
 */
class VoiceTutorService
{
    /**
     * Hasilkan respon tutor percakapan Basa Jawa.
     *
     * @param  string  $userText  Teks transkripsi ucapan siswa.
     * @param  array<int, array{role: string, content: string}>  $history
     * @return array{
     *     balasan_teks: string,
     *     expression: string,
     *     suggested_repeat: ?string,
     *     vocabulary: array<int, array{word: string, meaning: string, level: string}>
     * }
     */
    public function generateReply(string $userText, array $history = [], string $contextMode = 'free_talk'): array
    {
        $userText = trim($userText);
        if ($userText === '') {
            return [
                'balasan_teks' => 'Kula boten mireng swanten panjenengan. Coba wicara malih nggih!',
                'expression' => 'curious',
                'suggested_repeat' => null,
                'vocabulary' => [],
            ];
        }

        // 1. Coba panggil LLM eksternal (Groq Llama atau OpenAI/Gemini proxy)
        $llmResult = $this->callLlm($userText, $history, $contextMode);
        if ($llmResult !== null) {
            return $llmResult;
        }

        // 2. Fallback deterministik cerdas untuk Basa Jawa jika LLM tidak merespons
        return $this->smartJavaneseFallback($userText);
    }

    /**
     * Memanggil LLM provider cepat (Groq / Gemini proxy).
     */
    private function callLlm(string $userText, array $history, string $contextMode): ?array
    {
        $systemPrompt = <<<'PROMPT'
Panjenengan menika "Si Swara", tutor pacelathon Basa Jawa ingkang grapyak, sabar, lan pinter ing aplikasi SwaraJawa.
Tugas panjenengan yaiku ngobrol interaktif kaliyan siswa, mbiyantu siswa sinau pacelathon (percakapan), tembung, lan unggah-ungguh basa Jawa (Ngoko, Krama Madya, Krama Alus/Inggil).

Pandhuan wangsulan:
1. Wangsulana kanthi ramah, sopan, lan ringkes (maksimal 2-3 ukara cekak).
2. Gunakake Basa Jawa (utawa campuran Basa Indonesia ingkang trep menawa siswa takon teges/terjemahan).
3. Yen siswa takon carane ngomong ing kahanan tartamtu, wenehi ukara Basa Jawa sing bener, banjur tantang siswa supaya nurokake lafal kasebut: "Coba saiki tirukna: [ukara target]".
4. Yen siswa lagi nurokake utawa mangsuli, wenehi apresiasi ("Pinter!", "Saiki wis leres!").
5. Format respon kudu JSON valid persis kaya iki:
{
  "balasan_teks": "teks wangsulan kanggo siswa",
  "expression": "happy" | "neutral" | "thinking" | "speaking" | "curious",
  "suggested_repeat": "ukara Basa Jawa cekak sing kudu diturokake siswa (utawa null yen ora ana tantangan tirukan)",
  "vocabulary": [
    {"word": "tembung", "meaning": "arti dalam bahasa Indonesia", "level": "Krama/Ngoko"}
  ]
}
PROMPT;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Ambil 4 riwayat obrolan terakhir
        $recentHistory = array_slice($history, -4);
        foreach ($recentHistory as $msg) {
            if (isset($msg['role'], $msg['content'])) {
                $messages[] = [
                    'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                    'content' => (string) $msg['content'],
                ];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $userText];

        // Opsi A: Groq Chat Completion (Llama 3.3 / 3.1 Instant ~250ms)
        $groqKey = config('services.groq.api_key');
        if (filled($groqKey)) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $groqKey,
                ])->timeout(8)->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'qwen/qwen3.8-27b',
                    'messages' => $messages,
                    'temperature' => 0.3,
                    'max_tokens' => 200,
                    'response_format' => ['type' => 'json_object'],
                ]);

                if ($response->successful()) {
                    $jsonStr = $response->json('choices.0.message.content');
                    $parsed = json_decode((string) $jsonStr, true);
                    if (is_array($parsed) && isset($parsed['balasan_teks'])) {
                        return [
                            'balasan_teks' => (string) $parsed['balasan_teks'],
                            'expression' => in_array($parsed['expression'] ?? '', ['happy', 'neutral', 'thinking', 'speaking', 'curious']) ? $parsed['expression'] : 'happy',
                            'suggested_repeat' => ! empty($parsed['suggested_repeat']) ? (string) $parsed['suggested_repeat'] : null,
                            'vocabulary' => is_array($parsed['vocabulary'] ?? null) ? $parsed['vocabulary'] : [],
                        ];
                    }
                }
            } catch (Throwable $e) {
                Log::warning('VoiceTutor Groq LLM failed: ' . $e->getMessage());
            }
        }

        // Opsi B: Custom AI Provider / Gemini Proxy
        $aiKey = config('ai.api_key');
        $baseUrl = config('ai.base_url');
        if (filled($baseUrl)) {
            try {
                $client = filled($aiKey) ? Http::withToken((string) $aiKey) : Http::asJson();
                $response = $client->timeout(8)->post(rtrim((string) $baseUrl, '/') . '/chat/completions', [
                    'model' => config('ai.model', 'gemini-3.8-flash'),
                    'messages' => $messages,
                    'temperature' => 0.3,
                    'max_tokens' => 200,
                ]);

                if ($response->successful()) {
                    $raw = (string) $response->json('choices.0.message.content');
                    // Bersihkan formatting markdown json jika ada
                    $clean = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($raw)) ?? $raw;
                    $parsed = json_decode($clean, true);
                    if (is_array($parsed) && isset($parsed['balasan_teks'])) {
                        return [
                            'balasan_teks' => (string) $parsed['balasan_teks'],
                            'expression' => in_array($parsed['expression'] ?? '', ['happy', 'neutral', 'thinking', 'speaking', 'curious']) ? $parsed['expression'] : 'happy',
                            'suggested_repeat' => ! empty($parsed['suggested_repeat']) ? (string) $parsed['suggested_repeat'] : null,
                            'vocabulary' => is_array($parsed['vocabulary'] ?? null) ? $parsed['vocabulary'] : [],
                        ];
                    }
                }
            } catch (Throwable $e) {
                Log::warning('VoiceTutor AI Provider failed: ' . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Fallback cerdas Basa Jawa dengan pola umum percakapan & unggah-ungguh.
     */
    private function smartJavaneseFallback(string $text): array
    {
        $lower = mb_strtolower($text);

        // Kasus: Terima kasih
        if (str_contains($lower, 'terima kasih') || str_contains($lower, 'makasih') || str_contains($lower, 'matur nuwun')) {
            return [
                'balasan_teks' => 'Kagem matur nuwun ingkang leres dhateng tiyang sepuh, ucapaken: "Matur sembah nuwun sanget". Coba saiki tirukna lafal iki ya!',
                'expression' => 'happy',
                'suggested_repeat' => 'Matur sembah nuwun sanget',
                'vocabulary' => [
                    ['word' => 'Matur nuwun', 'meaning' => 'Terima kasih', 'level' => 'Krama Madya'],
                    ['word' => 'Sembah nuwun', 'meaning' => 'Terima kasih sebesar-besarnya', 'level' => 'Krama Alus'],
                ],
            ];
        }

        // Kasus: Maaf / Nyuwun sewu
        if (str_contains($lower, 'maaf') || str_contains($lower, 'permisi') || str_contains($lower, 'nyuwun sewu') || str_contains($lower, 'pangapunten')) {
            return [
                'balasan_teks' => 'Yen nyuwun sewu utawa njaluk ngapura kanthi sopan, aturaken: "Nyuwun pangapunten lahir batin". Ayo coba tirukna!',
                'expression' => 'neutral',
                'suggested_repeat' => 'Nyuwun pangapunten lahir batin',
                'vocabulary' => [
                    ['word' => 'Nyuwun sewu', 'meaning' => 'Permisi', 'level' => 'Krama'],
                    ['word' => 'Pangapunten', 'meaning' => 'Permohonan maaf', 'level' => 'Krama Alus'],
                ],
            ];
        }

        // Kasus: Salam / Selamat pagi / Sapaan
        if (str_contains($lower, 'halo') || str_contains($lower, 'hai') || str_contains($lower, 'pagi') || str_contains($lower, 'sugeng')) {
            return [
                'balasan_teks' => 'Sugeng enjang! Kula Si Swara, kanca sinau Basa Jawa panjenengan. Coba sapa kula nganggo: "Sugeng enjang, Mas".',
                'expression' => 'happy',
                'suggested_repeat' => 'Sugeng enjang, Mas',
                'vocabulary' => [
                    ['word' => 'Sugeng enjang', 'meaning' => 'Selamat pagi', 'level' => 'Krama'],
                    ['word' => 'Kanca', 'meaning' => 'Teman / Kawan', 'level' => 'Ngoko'],
                ],
            ];
        }

        // Kasus: Makan / Nedha / Dhahar
        if (str_contains($lower, 'makan') || str_contains($lower, 'mangan') || str_contains($lower, 'lapar') || str_contains($lower, 'dhahar')) {
            return [
                'balasan_teks' => 'Basa Jawa mangan kuwi ana undha-usuke: mangan (ngoko), nedha (krama madya), lan dhahar (krama alus). Coba tirukna ukara iki: "Bapak sampun dhahar".',
                'expression' => 'curious',
                'suggested_repeat' => 'Bapak sampun dhahar',
                'vocabulary' => [
                    ['word' => 'Dhahar', 'meaning' => 'Makan (untuk orang tua/dihormati)', 'level' => 'Krama Alus'],
                    ['word' => 'Sampun', 'meaning' => 'Sudah', 'level' => 'Krama'],
                ],
            ];
        }

        // Kasus: Menirukan atau jawaban pendek
        return [
            'balasan_teks' => 'Leres sanget! Pangucapan lan tata basamu sansaya sae. Coba saiki wicara babagan apa sing koksenengi dina iki.',
            'expression' => 'happy',
            'suggested_repeat' => 'Kula remen sinau Basa Jawa',
            'vocabulary' => [
                ['word' => 'Remen', 'meaning' => 'Suka / Gemar', 'level' => 'Krama'],
                ['word' => 'Sansaya sae', 'meaning' => 'Semakin baik', 'level' => 'Krama'],
            ],
        ];
    }
}
