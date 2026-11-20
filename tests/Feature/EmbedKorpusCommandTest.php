<?php

namespace Tests\Feature;

use App\Models\Korpus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EmbedKorpusCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_fails_when_gemini_key_missing(): void
    {
        config()->set('ai.gemini_api_key', null);

        $this->artisan('korpus:embed')
            ->expectsOutputToContain('GEMINI_API_KEY belum dikonfigurasi')
            ->assertExitCode(1);
    }

    public function test_command_embeds_records_successfully(): void
    {
        config()->set('ai.gemini_api_key', 'test-key');

        $dimension = (int) config('ai.embedding_dimension');
        $first = array_fill(0, $dimension, 0.1);
        $second = array_fill(0, $dimension, 0.4);

        Korpus::create([
            'judul' => 'Entri 1',
            'kategori' => 'umum',
            'konten' => 'Konten nomer siji.',
        ]);

        Korpus::create([
            'judul' => 'Entri 2',
            'kategori' => 'umum',
            'konten' => 'Konten nomer loro.',
        ]);

        Http::fake([
            '*:batchEmbedContents*' => Http::response([
                'embeddings' => [
                    ['values' => $first],
                    ['values' => $second],
                ],
            ]),
        ]);

        $this->artisan('korpus:embed')
            ->expectsOutputToContain('Menghasilkan embedding untuk 2 entri korpus...')
            ->assertExitCode(0);

        $records = Korpus::all();
        $this->assertNotNull($records[0]->embedding);
        $this->assertNotNull($records[1]->embedding);
        $this->assertEquals($first, $records[0]->embedding);
        $this->assertEquals($second, $records[1]->embedding);
    }
}
