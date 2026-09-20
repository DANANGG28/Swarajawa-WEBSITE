<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Models\Soal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AksaraSoalTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_preview_aksara_endpoint(): void
    {
        $guru = Guru::factory()->create();

        $response = $this->actingAs($guru, 'guru')->postJson(route('guru.soal.preview'), [
            'soal_latin' => 'hana caraka',
            'ketik_pepet_mode' => false,
            'ignore_space' => false,
            'aksara_swara_mode' => true,
        ]);

        $response->assertOk()->assertJson([
            'aksara' => "\u{A9B2}\u{A9A4} \u{A995}\u{A9AB}\u{A98F}",
        ]);
    }

    public function test_guru_store_tracing_builds_aksara_and_paths(): void
    {
        $guru = Guru::factory()->create();
        $level = LevelMateri::factory()->create();

        $paths = [[[0.1, 0.1], [0.5, 0.5], [0.9, 0.9]]];

        $this->actingAs($guru, 'guru')->post(route('guru.soal.store'), [
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_MENULIS_AKSARA,
            'pertanyaan' => 'Tulisen aksara "ha".',
            'soal_latin' => 'ha',
            'opsi_jawaban_raw' => json_encode(['aksara' => '', 'petunjuk' => 'Telusuri.']),
            'kunci_jawaban_raw' => json_encode(['paths' => $paths]),
            'bobot_exp' => 25,
            'ketik_pepet_mode' => 0,
            'ignore_space' => 0,
            'aksara_swara_mode' => 1,
        ])->assertRedirect();

        $soal = Soal::query()->latest('id')->firstOrFail();

        $this->assertSame('ha', $soal->soal_latin);
        $this->assertSame("\u{A9B2}", $soal->soal_aksara);
        $this->assertSame("\u{A9B2}", $soal->kunci_jawaban['aksara']);
        $this->assertSame('ha', $soal->kunci_jawaban['latin']);
        $this->assertSame($paths, $soal->kunci_jawaban['paths']);
    }

    public function test_guru_edit_form_prefills_latin_and_aksara(): void
    {
        $guru = Guru::factory()->create();
        $level = LevelMateri::factory()->create();

        $soal = Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_MENULIS_AKSARA,
            'pertanyaan' => 'Tulisen aksara "na".',
            'soal_latin' => 'na',
            'soal_aksara' => "\u{A9A4}",
            'opsi_jawaban' => ['aksara' => "\u{A9A4}", 'petunjuk' => 'Telusuri.'],
            'kunci_jawaban' => ['aksara' => "\u{A9A4}", 'latin' => 'na', 'paths' => []],
            'bobot_exp' => 25,
            'guru_id' => $guru->id,
        ]);

        $this->actingAs($guru, 'guru')
            ->get(route('guru.soal', ['level_materi_id' => $level->id]))
            ->assertOk()
            ->assertSee('data-soal-latin', false)
            ->assertSee("\u{A9A4}", false);
    }

    public function test_siswa_tracing_page_renders_canvas_and_template(): void
    {
        $siswa = Siswa::factory()->create();
        $level = LevelMateri::factory()->create();

        ProgresSiswa::create([
            'siswa_id' => $siswa->id,
            'level_materi_id' => $level->id,
            'status' => ProgresSiswa::STATUS_BERJALAN,
        ]);

        $soal = Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_MENULIS_AKSARA,
            'pertanyaan' => 'Tulisen aksara "ha".',
            'soal_latin' => 'ha',
            'soal_aksara' => "\u{A9B2}",
            'opsi_jawaban' => ['aksara' => "\u{A9B2}", 'petunjuk' => 'Telusuri.'],
            'kunci_jawaban' => [
                'aksara' => "\u{A9B2}",
                'latin' => 'ha',
                'paths' => [[[0.5, 0.2], [0.8, 0.5], [0.5, 0.8]]],
            ],
            'bobot_exp' => 25,
        ]);

        $this->actingAs($siswa, 'siswa')
            ->get(route('kuis.tracing-aksara', ['soal_id' => $soal->id]))
            ->assertOk()
            ->assertSee('tracing-canvas', false)
            ->assertSee('aksara-tracing-canvas', false)
            ->assertSee('new window.TracingCanvas', false);
    }

    public function test_jawab_tracing_scores_using_client_template_when_paths_empty(): void
    {
        $siswa = Siswa::factory()->create();
        $level = LevelMateri::factory()->create();

        ProgresSiswa::create([
            'siswa_id' => $siswa->id,
            'level_materi_id' => $level->id,
            'status' => ProgresSiswa::STATUS_BERJALAN,
        ]);

        $soal = Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_MENULIS_AKSARA,
            'pertanyaan' => 'Tulisen aksara "ha".',
            'soal_latin' => 'ha',
            'soal_aksara' => "\u{A9B2}",
            'opsi_jawaban' => ['aksara' => "\u{A9B2}", 'petunjuk' => 'Telusuri.'],
            'kunci_jawaban' => ['aksara' => "\u{A9B2}", 'latin' => 'ha', 'paths' => []],
            'bobot_exp' => 25,
        ]);

        $template = [[[0.2, 0.2], [0.8, 0.2], [0.8, 0.8], [0.2, 0.8], [0.2, 0.2]]];

        $response = $this->actingAs($siswa, 'siswa')->postJson(route('kuis.jawab'), [
            'soal_id' => $soal->id,
            'jawaban' => [
                'strokes' => $template,
                'template' => $template,
            ],
        ]);

        $response->assertOk()
            ->assertJson(['benar' => true])
            ->assertJsonPath('skor', 100);
    }
}
