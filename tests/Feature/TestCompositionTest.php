<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\LevelMateri;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Superadmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TestCompositionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_can_compose_mixed_and_single_type_tests(): void
    {
        $guru = Guru::factory()->create();
        $level = LevelMateri::create(['nama_materi' => 'Dasar', 'deskripsi' => 'x', 'reward_exp' => 100, 'urutan' => 1]);

        $pg = Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'PG?',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'A']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'guru_id' => $guru->id,
        ]);

        $susun = Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_SUSUN_KALIMAT,
            'pertanyaan' => 'Susun?',
            'opsi_jawaban' => ['a', 'b'],
            'kunci_jawaban' => ['susunan' => ['a', 'b']],
            'guru_id' => $guru->id,
        ]);

        Sanctum::actingAs($guru);

        // Campuran beberapa tipe_soal.
        $this->postJson('/api/test', [
            'level_materi_id' => $level->id,
            'nama_test' => 'Campuran',
            'soal_ids' => [$pg->id, $susun->id],
        ])->assertCreated();

        // Satu tipe soal saja (remedial/drilling).
        $this->postJson('/api/test', [
            'level_materi_id' => $level->id,
            'nama_test' => 'Remedial Susun',
            'soal_ids' => [$susun->id],
        ])->assertCreated();

        $this->assertDatabaseCount('test', 2);
        $this->assertDatabaseHas('test_soal', ['soal_id' => $pg->id, 'urutan' => 1]);
        $this->assertDatabaseHas('test_soal', ['soal_id' => $susun->id, 'urutan' => 2]);
    }

    public function test_superadmin_dashboard_reports_account_counts(): void
    {
        Guru::factory()->count(2)->create();
        Siswa::factory()->count(3)->create();

        Sanctum::actingAs(Superadmin::factory()->create());

        $this->getJson('/api/superadmin/dashboard')
            ->assertOk()
            ->assertJson([
                'total_guru' => 2,
                'total_siswa' => 3,
            ]);
    }
}
