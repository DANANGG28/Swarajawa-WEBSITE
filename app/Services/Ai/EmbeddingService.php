<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmbeddingService
{
    private string $baseUrl;

    private ?string $apiKey;

    private string $model;

    private int $dimension;

    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('ai.embedding_base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $this->apiKey = config('ai.gemini_api_key');
        $this->model = (string) config('ai.embedding_model', 'text-embedding-004');
        $this->dimension = (int) config('ai.embedding_dimension', 768);
        $this->timeout = (int) config('ai.timeout', 30);
    }

    /**
     * Embed single text into vector.
     *
     * @return array<float>|null
     */
    public function embed(string $text): ?array
    {
        $text = trim($text);
        if ($text === '' || empty($this->apiKey)) {
            return null;
        }

        $url = "{$this->baseUrl}/models/{$this->model}:embedContent?key={$this->apiKey}";

        try {
            $response = Http::timeout($this->timeout)
                ->asJson()
                ->post($url, [
                    'model' => "models/{$this->model}",
                    'content' => [
                        'parts' => [
                            ['text' => $text],
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $values = $this->normalize($response->json('embedding.values'));

                if ($values !== null) {
                    return $values;
                }
            }

            Log::warning('EmbeddingService embed failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (Throwable $e) {
            Log::warning('EmbeddingService embed exception', [
                'message' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Embed multiple texts in batches.
     *
     * @param  array<string>  $texts
     * @return array<int, array<float>|null>
     */
    public function batchEmbed(array $texts, int $batchSize = 50): array
    {
        if (empty($texts) || empty($this->apiKey)) {
            return array_fill(0, count($texts), null);
        }

        $results = [];
        $chunks = array_chunk($texts, $batchSize, true);

        foreach ($chunks as $chunk) {
            $requests = [];
            foreach ($chunk as $text) {
                $requests[] = [
                    'model' => "models/{$this->model}",
                    'content' => [
                        'parts' => [
                            ['text' => trim($text)],
                        ],
                    ],
                ];
            }

            $url = "{$this->baseUrl}/models/{$this->model}:batchEmbedContents?key={$this->apiKey}";

            try {
                $response = Http::timeout($this->timeout * 2)
                    ->asJson()
                    ->post($url, [
                        'requests' => $requests,
                    ]);

                if ($response->successful()) {
                    $embeddings = $response->json('embeddings');
                    $chunkKeys = array_keys($chunk);
                    foreach ($chunkKeys as $idx => $origKey) {
                        $results[$origKey] = $this->normalize($embeddings[$idx]['values'] ?? null);
                    }

                    continue;
                }

                Log::warning('EmbeddingService batchEmbed failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            } catch (Throwable $e) {
                Log::warning('EmbeddingService batchEmbed exception', [
                    'message' => $e->getMessage(),
                ]);
            }

            foreach (array_keys($chunk) as $origKey) {
                $results[$origKey] = null;
            }
        }

        ksort($results);

        return $results;
    }

    /**
     * Validasi respons embedding: harus array angka dan sepanjang dimensi
     * kolom, supaya insert ke pgvector tidak gagal saat runtime.
     *
     * @return array<float>|null
     */
    private function normalize(mixed $values): ?array
    {
        if (! is_array($values) || count($values) !== $this->dimension) {
            return null;
        }

        return array_map('floatval', array_values($values));
    }
}
