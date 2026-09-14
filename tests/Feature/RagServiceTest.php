<?php

namespace Tests\Feature;

use App\Models\Korpus;
use App\Services\Ai\RagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RagServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_in_scope_question_is_answered_from_corpus(): void
    {
        Korpus::create([
            'judul' => 'Unggah-ungguh mangan nedha dhahar',
            'kategori' => 'unggah-ungguh',
            'konten' => 'Mangan kalebu ngoko, nedha krama, dhahar krama inggil kanggo wong sing diajeni.',
        ]);

        $hasil = app(RagService::class)->ask('beda mangan nedha dhahar');

        $this->assertTrue($hasil['dijawab']);
        $this->assertNotEmpty($hasil['sumber']);
    }

    public function test_out_of_scope_question_is_refused(): void
    {
        Korpus::create([
            'judul' => 'Unggah-ungguh mangan nedha dhahar',
            'kategori' => 'unggah-ungguh',
            'konten' => 'Mangan kalebu ngoko, nedha krama, dhahar krama inggil.',
        ]);

        $hasil = app(RagService::class)->ask('resep kue coklat');

        $this->assertFalse($hasil['dijawab']);
        $this->assertSame([], $hasil['sumber']);
    }
}
