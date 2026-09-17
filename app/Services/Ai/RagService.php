<?php

namespace App\Services\Ai;

use App\Models\Korpus;
use App\Support\TextSimilarity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Chatbot RAG dengan guardrail pasinaon & penerjemahan basa Jawa (FR-9).
 *
 * Alur:
 * 1. Retrieval mencari konteks dari basis korpus (tata basa, unggah-ungguh, penerjemahan).
 * 2. Konteks dikirim ke LLM (Gemini) dengan System Prompt fleksibel:
 *    - Melayani pitakon tata basa, unggah-ungguh, aksara, paribasan, lan budaya Jawa.
 *    - Melayani penerjemahan (translasi) ukara/tembung saking basa Indonesia/Inggris dhateng basa Jawa.
 *    - Menolak eksplisit (NANG_NJABA_KORPUS) bila pertanyaan murni di luar topik Jawa (mis. sains, resep, matematika, dsb).
 * 3. Bila LLM provider offline/gagal, sistem fallback ke jawaban mentah korpus bila skor kecocokan memadai.
 */
class RagService
{
    public const THRESHOLD = 0.3;

    public const FALLBACK_THRESHOLD = 0.75;

    public const REFUSAL_TOKEN = 'NANG_NJABA_KORPUS';

    private const REFUSAL_MESSAGE = 'Nyuwun pangapunten, pitakon menika wonten ing sanjabane korpus pasinaon ingkang kasedhiya. Kula namung saged mbiyantu babagan basa lan budaya Jawa ingkang wonten ing materi.';

    /**
     * Kata umum yang tidak dihitung sebagai kata kunci utama.
     *
     * @var array<int, string>
     */
    private const STOPWORDS = [
        'apa', 'itu', 'ini', 'yang', 'dan', 'atau', 'dengan', 'untuk', 'pada',
        'ke', 'di', 'dari', 'sama', 'saya', 'aku', 'kamu', 'kowe', 'kula',
        'gimana', 'bagaimana', 'kenapa', 'mengapa', 'kalau', 'jika',
        'adalah', 'ada', 'bisa', 'dapat', 'tolong', 'dong', 'sih', 'ya', 'yaiku',
        'punapa', 'menapa', 'pripun', 'kepriye', 'kadospundi', 'pundi', 'menika',
        'punika', 'iku', 'iki', 'kang', 'sing', 'lan', 'utawa', 'kaliyan', 'karo',
        'ing', 'wonten', 'ana', 'saka', 'saking', 'marang', 'dhateng', 'kanggo',
        'kalimat', 'indonesia', 'english', 'inggris',
    ];

    /**
     * Kelompok sinonim (Indonesia <-> Jawa).
     *
     * @var array<int, array<int, string>>
     */
    private const SYNONYM_GROUPS = [
        ['bahasa', 'basa', 'boso'],
        ['bicara', 'omong', 'ngomong', 'gunem', 'matur', 'wicara', 'ngendika', 'catur'],
        ['kata', 'tembung'],
        ['arti', 'artinya', 'teges', 'tegese', 'makna'],
        ['tidur', 'turu', 'tilem', 'sare'],
        ['makan', 'mangan', 'nedha', 'dhahar'],
        ['rumah', 'omah', 'griya', 'dalem'],
        ['pergi', 'lunga', 'tindak', 'kesah', 'mangkat'],
        ['lihat', 'ndeleng', 'mirsani', 'tingal'],
        ['nama', 'jeneng', 'asma'],
        ['tua', 'sepuh', 'tuwa', 'sesepuh'],
        ['muda', 'enom', 'nem'],
        ['benar', 'bener', 'leres'],
        ['salah', 'lepat'],
        ['hormat', 'krama', 'ngajeni', 'ngurmati', 'pakurmatan'],
        ['sopan', 'alus', 'krama'],
        ['tulis', 'nulis', 'nyerat', 'serat', 'aksara'],
        ['murid', 'siswa'],
        ['bapak', 'rama'],
        ['ibu', 'biyung'],
    ];

    /** @var array<string, array<int, string>>|null */
    private static ?array $synonymIndex = null;

    /**
     * @return array{dijawab: bool, jawaban: string, sumber: array<int, array<string, ?string>>, skor_tertinggi: float}
     */
    public function ask(string $question, int $limit = 3): array
    {
        if (trim($question) === '') {
            return $this->refuse();
        }

        $tokens = $this->keywords($question);
        $relevant = $this->retrieve($tokens, $limit);

        $answer = $this->generate($question, $relevant);

        if ($answer === null) {
            $topScore = (float) ($relevant->first()['score'] ?? 0.0);
            if ($relevant->isEmpty() || $topScore < self::FALLBACK_THRESHOLD) {
                return $this->refuse();
            }

            return $this->fallback($relevant);
        }

        if (trim($answer) === self::REFUSAL_TOKEN) {
            return $this->refuse();
        }

        $validSources = $relevant->filter(fn (array $row): bool => ($row['score'] ?? 0.0) > 0);

        return [
            'dijawab' => true,
            'jawaban' => trim($answer),
            'sumber' => $this->sources($validSources->isNotEmpty() ? $validSources : $relevant),
            'skor_tertinggi' => round((float) ($relevant->first()['score'] ?? 0.0), 3),
        ];
    }

    /**
     * Kata kunci pertanyaan setelah normalisasi & pembuangan stopword.
     *
     * @return array<int, string>
     */
    private function keywords(string $question): array
    {
        $tokens = array_values(array_unique(TextSimilarity::tokens($question)));
        $tokens = array_values(array_diff($tokens, self::STOPWORDS));

        return array_values(array_filter($tokens, fn (string $t): bool => mb_strlen($t) > 1));
    }

    /**
     * @param  array<int, string>  $tokens
     * @return Collection<int, array{item: Korpus, score: float}>
     */
    private function retrieve(array $tokens, int $limit): Collection
    {
        $synonyms = self::synonymIndex();

        $scored = Korpus::query()
            ->get()
            ->map(function (Korpus $item) use ($tokens, $synonyms): array {
                $docTokens = array_flip(array_unique(array_merge(
                    TextSimilarity::tokens($item->judul),
                    TextSimilarity::tokens($item->kategori),
                    TextSimilarity::tokens($item->konten),
                )));

                $matched = 0;

                foreach ($tokens as $token) {
                    if (isset($docTokens[$token])) {
                        $matched++;

                        continue;
                    }

                    foreach ($synonyms[$token] ?? [] as $synonym) {
                        if (isset($docTokens[$synonym])) {
                            $matched++;
                            break;
                        }
                    }
                }

                $coverage = count($tokens) > 0 ? ($matched / count($tokens)) : 0.0;

                return ['item' => $item, 'score' => $coverage];
            })
            ->sortByDesc('score')
            ->values();

        $matchedDocs = $scored->filter(fn (array $row): bool => $row['score'] > 0);

        if ($matchedDocs->isNotEmpty()) {
            return $matchedDocs->take($limit)->values();
        }

        return $scored->take($limit)->values();
    }

    /**
     * Peta kata -> daftar sinonim (dibangun sekali per proses).
     *
     * @return array<string, array<int, string>>
     */
    private static function synonymIndex(): array
    {
        if (self::$synonymIndex !== null) {
            return self::$synonymIndex;
        }

        $index = [];

        foreach (self::SYNONYM_GROUPS as $group) {
            foreach ($group as $term) {
                foreach ($group as $other) {
                    if ($other !== $term) {
                        $index[$term][$other] = true;
                    }
                }
            }
        }

        return self::$synonymIndex = array_map('array_keys', $index);
    }

    /**
     * Panggil LLM untuk merangkai jawaban atau melakukan penerjemahan.
     *
     * @param  Collection<int, array{item: Korpus, score: float}>  $docs
     */
    private function generate(string $question, Collection $docs): ?string
    {
        $baseUrl = config('ai.base_url');

        if (blank($baseUrl)) {
            return null;
        }

        try {
            $response = Http::withToken((string) config('ai.api_key'))
                ->acceptJson()
                ->timeout((int) config('ai.timeout', 30))
                ->post(rtrim((string) $baseUrl, '/').'/chat/completions', [
                    'model' => config('ai.model'),
                    'temperature' => 0.2,
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        ['role' => 'user', 'content' => $this->userPrompt($question, $docs)],
                    ],
                ]);

            if (! $response->successful()) {
                return null;
            }

            $content = $response->json('choices.0.message.content');

            return is_string($content) && trim($content) !== '' ? $content : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
        Panjenengan menika "Kanca Sinau Jawa", asisten pasinaon basa lan budaya Jawa kanggo siswa SMP/SMA.

        Tugas lan Kapabilitas Utama:
        1. Nggarap pitakon babagan tata basa Jawa, unggah-ungguh (Ngoko, Ngoko Alus, Krama, Krama Alus), aksara Jawa, paribasan, lan budaya Jawa.
        2. Nindakake penerjemahan (penterjemahan) tembung utawa ukara saking basa Indonesia utawi Inggris dhateng basa Jawa (ngandharaken ing undha-usuk Ngoko lan Krama ingkang trep), utawi njelasaken basa Jawanipun sawijining ukara ingkang dipun-nyuwun dening siswa.
        3. Njelasaken teges lan panggunaaning tembung basa Jawa.

        Aturan Guardrail:
        1. Gunakaken KONTEKS ingkang dipun-paringi minangka acuan utama paugeran tata basa, tata krama, lan acuan kosakata.
        2. Menawi pitakon siswa MURNI wonten ing sanjabane pasinaon basa Jawa, penerjemahan basa Jawa, utawi budaya Jawa (tuladhanipun: ilmu sains/fotosintesis, resep masakan umum, matematika/koding, warta politik/internasional), wangsulana kanthi PERSIS token punika lan boten sanes: NANG_NJABA_KORPUS
        3. Wangsulana kanthi ramah, cetha, lan ngginakaken basa Indonesia minangka pengantar dipun-jangkepi tuladha/ukara ing basa Jawa (Ngoko & Krama).
        PROMPT;
    }

    /**
     * @param  Collection<int, array{item: Korpus, score: float}>  $docs
     */
    private function userPrompt(string $question, Collection $docs): string
    {
        $context = $docs->values()->map(function (array $row, int $i): string {
            $item = $row['item'];

            return sprintf(
                "[Sumber %d | %s | kategori: %s]\n%s",
                $i + 1,
                $item->judul,
                $item->kategori,
                $item->konten,
            );
        })->implode("\n\n");

        return "KONTEKS KORPUS ACUAN:\n{$context}\n\nPITAKON SISWA:\n{$question}";
    }

    /**
     * Jawaban fallback tanpa LLM.
     *
     * @param  Collection<int, array{item: Korpus, score: float}>  $docs
     * @return array{dijawab: bool, jawaban: string, sumber: array<int, array<string, ?string>>, skor_tertinggi: float}
     */
    private function fallback(Collection $docs): array
    {
        return [
            'dijawab' => true,
            'jawaban' => $docs->map(fn (array $row): string => $row['item']->konten)->implode("\n\n"),
            'sumber' => $this->sources($docs),
            'skor_tertinggi' => round((float) ($docs->first()['score'] ?? 0.0), 3),
        ];
    }

    /**
     * @param  Collection<int, array{item: Korpus, score: float}>  $docs
     * @return array<int, array<string, ?string>>
     */
    private function sources(Collection $docs): array
    {
        return $docs->map(fn (array $row): array => [
            'judul' => $row['item']->judul,
            'kategori' => $row['item']->kategori,
            'sumber' => $row['item']->sumber,
        ])->all();
    }

    /**
     * @return array{dijawab: bool, jawaban: string, sumber: array<int, mixed>, skor_tertinggi: float}
     */
    private function refuse(): array
    {
        return [
            'dijawab' => false,
            'jawaban' => self::REFUSAL_MESSAGE,
            'sumber' => [],
            'skor_tertinggi' => 0.0,
        ];
    }
}
