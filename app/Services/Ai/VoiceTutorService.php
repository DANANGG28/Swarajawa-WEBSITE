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
Kamu adalah "Si Swara", tutor percakapan dan koreksi Bahasa Jawa di aplikasi SwaraJawa.
Tugasmu adalah membimbing siswa SMP/SMA belajar percakapan, kosakata, dan unggah-ungguh Bahasa Jawa (Ngoko, Krama Madya, Krama Alus).

Panduan Berbicara & Menjawab:
1. Sampaikan evaluasi, koreksi pelafalan, atau penjelasan cara berbicara dalam Bahasa Indonesia yang ramah, santun, dan singkat (maksimal 2 kalimat).
2. Jika siswa bertanya atau perlu contoh kalimat Bahasa Jawa, berikan kalimat yang benar, lalu tantang siswa untuk menirukan: "Coba sekarang tirukan: [kalimat target Jawa]".
3. Jika ucapan siswa sudah benar, berikan apresiasi ("Pintar! Sudah benar.") dalam Bahasa Indonesia.
4. Format respon WAJIB JSON valid:
{
  "balasan_teks": "teks koreksi atau penjelasan dalam Bahasa Indonesia ramah",
  "voice": "id-ID-ArdiNeural",
  "expression": "happy" | "neutral" | "thinking" | "speaking" | "curious",
  "suggested_repeat": "kalimat Jawa pendek yang harus ditirukan siswa (atau null jika tidak ada tantangan)",
  "vocabulary": [
    {"word": "kata Jawa", "meaning": "arti dalam Bahasa Indonesia", "level": "Krama/Ngoko"}
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
                            'voice' => ! empty($parsed['voice']) ? (string) $parsed['voice'] : 'id-ID-ArdiNeural',
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
                            'voice' => ! empty($parsed['voice']) ? (string) $parsed['voice'] : 'id-ID-ArdiNeural',
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
                'balasan_teks' => 'Untuk berterima kasih secara sopan kepada orang tua atau guru, sampaikan: "Matur sembah nuwun sanget". Coba sekarang tirukan ya!',
                'voice' => 'id-ID-ArdiNeural',
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
                'balasan_teks' => 'Kalau mau permisi lewat atau minta maaf secara sopan, ucapkan: "Nyuwun pangapunten lahir batin". Ayo coba tirukan!',
                'voice' => 'id-ID-ArdiNeural',
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
                'balasan_teks' => 'Selamat pagi! Saya Si Swara, teman belajar Bahasa Jawa kamu. Coba sapa saya dengan: "Sugeng enjang, Mas".',
                'voice' => 'id-ID-ArdiNeural',
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
                'balasan_teks' => 'Bahasa Jawa makan memiliki tingkatan: mangan untuk ngoko, nedha untuk krama madya, dan dhahar untuk orang tua. Coba tirukan: "Bapak sampun dhahar".',
                'voice' => 'id-ID-ArdiNeural',
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
            'balasan_teks' => 'Pintar! Pengucapan dan tata bahasamu sudah semakin tepat. Coba sekarang kamu tirukan kalimat ini ya.',
            'voice' => 'id-ID-ArdiNeural',
            'expression' => 'happy',
            'suggested_repeat' => 'Kula remen sinau Basa Jawa',
            'vocabulary' => [
                ['word' => 'Remen', 'meaning' => 'Suka / Gemar', 'level' => 'Krama'],
                ['word' => 'Sansaya sae', 'meaning' => 'Semakin baik', 'level' => 'Krama'],
            ],
        ];
    }
}
