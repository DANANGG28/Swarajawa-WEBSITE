<?php

namespace Tests\Unit;

use App\Services\Ai\EmbeddingService;
use App\Support\TextSimilarity;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EmbeddingServiceTest extends TestCase
{
    public function test_embed_returns_float_array_on_success(): void
    {
        config()->set('ai.gemini_api_key', 'test-api-key');
        config()->set('ai.embedding_model', 'text-embedding-004');
        config()->set('ai.embedding_dimension', 3);

        Http::fake([
            '*:embedContent*' => Http::response([
                'embedding' => [
                    'values' => [0.123, -0.456, 0.789],
                ],
            ]),
        ]);

        $service = app(EmbeddingService::class);
        $vector = $service->embed('tes teks basa Jawa');

        $this->assertIsArray($vector);
        $this->assertCount(3, $vector);
        $this->assertEquals([0.123, -0.456, 0.789], $vector);
    }

    public function test_embed_returns_null_when_api_key_empty(): void
    {
        config()->set('ai.gemini_api_key', null);

        $service = app(EmbeddingService::class);
        $vector = $service->embed('tes teks');

        $this->assertNull($vector);
    }

    public function test_embed_returns_null_on_http_failure(): void
    {
        config()->set('ai.gemini_api_key', 'test-api-key');

        Http::fake([
            '*:embedContent*' => Http::response('Server error', 500),
        ]);

        $service = app(EmbeddingService::class);
        $vector = $service->embed('tes teks');

        $this->assertNull($vector);
    }

    public function test_batch_embed_processes_chunks_correctly(): void
    {
        config()->set('ai.gemini_api_key', 'test-api-key');
        config()->set('ai.embedding_dimension', 2);

        Http::fake([
            '*:batchEmbedContents*' => Http::response([
                'embeddings' => [
                    ['values' => [1.0, 0.0]],
                    ['values' => [0.0, 1.0]],
                ],
            ]),
        ]);

        $service = app(EmbeddingService::class);
        $results = $service->batchEmbed(['kalimat satu', 'kalimat dua']);

        $this->assertCount(2, $results);
        $this->assertEquals([1.0, 0.0], $results[0]);
        $this->assertEquals([0.0, 1.0], $results[1]);
    }

    public function test_embed_returns_null_when_dimension_mismatch(): void
    {
        config()->set('ai.gemini_api_key', 'test-api-key');
        config()->set('ai.embedding_dimension', 768);

        Http::fake([
            '*:embedContent*' => Http::response([
                'embedding' => [
                    'values' => [0.1, 0.2, 0.3],
                ],
            ]),
        ]);

        $service = app(EmbeddingService::class);

        $this->assertNull($service->embed('tes teks'));
    }

    public function test_cosine_similarity_calculation(): void
    {
        // Identical vectors -> 1.0
        $this->assertEqualsWithDelta(1.0, TextSimilarity::cosine([1.0, 2.0, 3.0], [1.0, 2.0, 3.0]), 0.0001);

        // Orthogonal vectors -> 0.0
        $this->assertEqualsWithDelta(0.0, TextSimilarity::cosine([1.0, 0.0], [0.0, 1.0]), 0.0001);

        // Opposite vectors -> -1.0
        $this->assertEqualsWithDelta(-1.0, TextSimilarity::cosine([1.0, 0.0], [-1.0, 0.0]), 0.0001);

        // Empty / mismatched vectors -> 0.0
        $this->assertEquals(0.0, TextSimilarity::cosine([], []));
        $this->assertEquals(0.0, TextSimilarity::cosine([1.0], [1.0, 2.0]));
    }
}
