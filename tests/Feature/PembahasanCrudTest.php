<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\LevelMateri;
use App\Models\Pembahasan;
use App\Models\Soal;
use App\Models\Superadmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembahasanCrudTest extends TestCase
{
    use RefreshDatabase;

    private function level(int $urutan = 1, string $nama = 'Dasar'): LevelMateri
    {
        return LevelMateri::create([
            'nama_materi' => $nama,
            'deskripsi' => 'Deskripsi',
            'reward_exp' => 100,
            'urutan' => $urutan,
        ]);
    }

    private function soalPayload(array $overrides = []): array
    {
        return array_merge([
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Pitakon uji?',
            'opsi_jawaban_raw' => json_encode([['label' => 'A', 'teks' => 'Bener']]),
            'kunci_jawaban_raw' => json_encode(['jawaban' => 'A']),
            'bobot_exp' => 10,
        ], $overrides);
    }

    public function test_guru_can_manage_pembahasan(): void
    {
        $guru = Guru::factory()->create();
        $level = $this->level();

        // Halaman kelola pembahasan tampil.
        $this->actingAs($guru, 'guru')
            ->get('/guru/pembahasan?level_materi_id='.$level->id)
            ->assertOk()
            ->assertSee('Kelola Pembahasan');

        // Tambah pembahasan (urutan otomatis).
        $this->actingAs($guru, 'guru')->post('/guru/pembahasan', [
            'level_materi_id' => $level->id,
            'nama' => 'Salam & Sapaan',
            'deskripsi' => 'Tetembungan salam',
        ])->assertRedirect();

        $this->assertDatabaseHas('pembahasan', ['level_materi_id' => $level->id, 'nama' => 'Salam & Sapaan', 'urutan' => 1]);

        $pembahasan = Pembahasan::where('nama', 'Salam & Sapaan')->firstOrFail();

        // Sunting pembahasan.
        $this->actingAs($guru, 'guru')->put('/guru/pembahasan/'.$pembahasan->id, [
            'nama' => 'Salam, Sapaan & Pakurmatan',
            'deskripsi' => 'Diperbarui',
        ])->assertRedirect();

        $this->assertDatabaseHas('pembahasan', ['id' => $pembahasan->id, 'nama' => 'Salam, Sapaan & Pakurmatan', 'urutan' => 1]);

        // Hapus pembahasan.
        $this->actingAs($guru, 'guru')->delete('/guru/pembahasan/'.$pembahasan->id)->assertRedirect();
        $this->assertDatabaseMissing('pembahasan', ['id' => $pembahasan->id]);
    }

    public function test_superadmin_can_manage_pembahasan(): void
    {
        $admin = Superadmin::factory()->create();
        $level = $this->level();

        $this->actingAs($admin, 'superadmin')
            ->get('/superadmin/pembahasan?level_materi_id='.$level->id)
            ->assertOk();

        $this->actingAs($admin, 'superadmin')->post('/superadmin/pembahasan', [
            'level_materi_id' => $level->id,
            'nama' => 'Krama Inggil',
        ])->assertRedirect();

        $pembahasan = Pembahasan::where('nama', 'Krama Inggil')->firstOrFail();

        $this->actingAs($admin, 'superadmin')->put('/superadmin/pembahasan/'.$pembahasan->id, [
            'nama' => 'Krama Inggil & Alus',
        ])->assertRedirect();

        $this->assertDatabaseHas('pembahasan', ['id' => $pembahasan->id, 'nama' => 'Krama Inggil & Alus', 'urutan' => 1]);

        $this->actingAs($admin, 'superadmin')->delete('/superadmin/pembahasan/'.$pembahasan->id)->assertRedirect();
        $this->assertDatabaseMissing('pembahasan', ['id' => $pembahasan->id]);
    }

    public function test_pembahasan_name_must_be_unique_per_level(): void
    {
        $guru = Guru::factory()->create();
        $level = $this->level();
        $otherLevel = $this->level(2, 'Unggah-ungguh');

        Pembahasan::create(['level_materi_id' => $level->id, 'nama' => 'Salam', 'urutan' => 1]);

        // Duplikat di level yang sama ditolak.
        $this->actingAs($guru, 'guru')->post('/guru/pembahasan', [
            'level_materi_id' => $level->id,
            'nama' => 'Salam',
        ])->assertSessionHasErrors('nama');

        // Nama sama di level berbeda tetap diperbolehkan.
        $this->actingAs($guru, 'guru')->post('/guru/pembahasan', [
            'level_materi_id' => $otherLevel->id,
            'nama' => 'Salam',
        ])->assertRedirect();

        $this->assertDatabaseCount('pembahasan', 2);
    }

    public function test_pembahasan_urutan_is_auto_incremented_per_level(): void
    {
        $guru = Guru::factory()->create();
        $level = $this->level();

        foreach (['Salam', 'Pakurmatan', 'Tembung'] as $nama) {
            $this->actingAs($guru, 'guru')->post('/guru/pembahasan', [
                'level_materi_id' => $level->id,
                'nama' => $nama,
            ])->assertRedirect();
        }

        $this->assertDatabaseHas('pembahasan', ['nama' => 'Salam', 'urutan' => 1]);
        $this->assertDatabaseHas('pembahasan', ['nama' => 'Pakurmatan', 'urutan' => 2]);
        $this->assertDatabaseHas('pembahasan', ['nama' => 'Tembung', 'urutan' => 3]);
    }

    public function test_guru_can_create_soal_with_pembahasan(): void
    {
        $guru = Guru::factory()->create();
        $level = $this->level();
        $pembahasan = Pembahasan::create(['level_materi_id' => $level->id, 'nama' => 'Salam', 'urutan' => 1]);

        $this->actingAs($guru, 'guru')->post('/guru/soal', $this->soalPayload([
            'level_materi_id' => $level->id,
            'pembahasan_id' => $pembahasan->id,
        ]))->assertRedirect();

        $this->assertDatabaseHas('soal', [
            'pertanyaan' => 'Pitakon uji?',
            'pembahasan_id' => $pembahasan->id,
            'guru_id' => $guru->id,
        ]);
    }

    public function test_pembahasan_must_belong_to_selected_level(): void
    {
        $guru = Guru::factory()->create();
        $levelA = $this->level(1, 'Dasar');
        $levelB = $this->level(2, 'Unggah-ungguh');
        $pembahasanB = Pembahasan::create(['level_materi_id' => $levelB->id, 'nama' => 'Krama', 'urutan' => 1]);

        $this->actingAs($guru, 'guru')->post('/guru/soal', $this->soalPayload([
            'level_materi_id' => $levelA->id,
            'pembahasan_id' => $pembahasanB->id,
        ]))->assertSessionHasErrors('pembahasan_id');

        $this->assertDatabaseCount('soal', 0);
    }

    public function test_deleting_pembahasan_keeps_soal_without_pembahasan(): void
    {
        $guru = Guru::factory()->create();
        $level = $this->level();
        $pembahasan = Pembahasan::create(['level_materi_id' => $level->id, 'nama' => 'Salam', 'urutan' => 1]);

        $soal = Soal::create([
            'level_materi_id' => $level->id,
            'pembahasan_id' => $pembahasan->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Pitakon?',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'A']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
            'guru_id' => $guru->id,
        ]);

        $this->actingAs($guru, 'guru')->delete('/guru/pembahasan/'.$pembahasan->id)->assertRedirect();

        $this->assertDatabaseHas('soal', ['id' => $soal->id, 'pembahasan_id' => null]);
    }
}
