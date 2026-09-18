<?php

namespace Tests\Feature;

use App\Models\Korpus;
use App\Services\Ai\RagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RagServiceTest extends TestCase
{
    use RefreshDatabase;

    private function seedKorpus(): void
    {
        Korpus::create([
            'judul' => 'Unggah-ungguh mangan nedha dhahar',
            'kategori' => 'unggah-ungguh',
            'konten' => 'Mangan kalebu ngoko, nedha krama, dhahar krama inggil kanggo wong sing diajeni.',
        ]);
    }

    public function test_in_scope_question_is_answered_from_corpus(): void
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Mangan ngoko, nedha krama, dhahar krama inggil.']],
                ],
            ]),
        ]);

        $this->seedKorpus();

        $hasil = app(RagService::class)->ask('beda mangan nedha dhahar');

        $this->assertTrue($hasil['dijawab']);
        $this->assertNotEmpty($hasil['sumber']);
        $this->assertStringContainsString('dhahar', $hasil['jawaban']);
        Http::assertSent(fn ($request): bool => str_contains($request->url(), '/chat/completions'));
    }

    public function test_indonesian_question_matches_javanese_corpus_via_synonyms(): void
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Nalika matur marang guru, gunakna basa krama alus.']],
                ],
            ]),
        ]);

        Korpus::create([
            'judul' => 'Unggah-ungguh matur marang guru',
            'kategori' => 'unggah-ungguh',
            'konten' => 'Nalika matur marang guru, gunakna basa krama alus. Tembung kula lan panjenengan minangka tuladha.',
        ]);

        $hasil = app(RagService::class)->ask('kalau bicara ke guru pakai bahasa apa');

        $this->assertTrue($hasil['dijawab']);
        $this->assertNotEmpty($hasil['sumber']);
        Http::assertSent(fn ($request): bool => str_contains($request->url(), '/chat/completions'));
    }

    public function test_out_of_scope_question_is_refused_by_llm(): void
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => RagService::REFUSAL_TOKEN]],
                ],
            ]),
        ]);

        $this->seedKorpus();

        $hasil = app(RagService::class)->ask('resep kue coklat');

        $this->assertFalse($hasil['dijawab']);
        $this->assertSame([], $hasil['sumber']);
        Http::assertSent(fn ($request): bool => str_contains($request->url(), '/chat/completions'));
    }

    public function test_llm_refusal_token_turns_into_refusal(): void
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => RagService::REFUSAL_TOKEN]],
                ],
            ]),
        ]);

        $this->seedKorpus();

        $hasil = app(RagService::class)->ask('beda mangan nedha dhahar');

        $this->assertFalse($hasil['dijawab']);
        $this->assertSame([], $hasil['sumber']);
    }

    public function test_falls_back_to_raw_retrieval_when_llm_fails(): void
    {
        Http::fake([
            '*/chat/completions' => Http::response('boom', 500),
        ]);

        $this->seedKorpus();

        $hasil = app(RagService::class)->ask('beda mangan nedha dhahar');

        $this->assertTrue($hasil['dijawab']);
        $this->assertStringContainsString('Mangan kalebu ngoko', $hasil['jawaban']);
        $this->assertNotEmpty($hasil['sumber']);
    }

    public function test_falls_back_when_provider_not_configured(): void
    {
        config()->set('ai.base_url', null);
        Http::fake();

        $this->seedKorpus();

        $hasil = app(RagService::class)->ask('beda mangan nedha dhahar');

        $this->assertTrue($hasil['dijawab']);
        $this->assertStringContainsString('Mangan kalebu ngoko', $hasil['jawaban']);
        Http::assertNothingSent();
    }
}
