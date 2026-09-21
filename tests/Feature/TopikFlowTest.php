<?php

namespace Tests\Feature;

use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Superadmin;
use App\Models\Topik;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopikFlowTest extends TestCase
{
    use RefreshDatabase;

    private function topik(string $nama, int $urutan): Topik
    {
        return Topik::create(['nama' => $nama, 'deskripsi' => 'Deskripsi '.$nama, 'urutan' => $urutan]);
    }

    private function unit(Topik $topik, string $nama, int $urutan): LevelMateri
    {
        $unit = LevelMateri::create([
            'topik_id' => $topik->id,
            'nama_materi' => $nama,
            'deskripsi' => 'Deskripsi '.$nama,
            'reward_exp' => 100,
            'urutan' => $urutan,
        ]);

        Soal::create([
            'level_materi_id' => $unit->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Pitakon '.$nama.'?',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'A']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
        ]);

        return $unit;
    }

    private function siswaDenganProgres(LevelMateri $unit): Siswa
    {
        $siswa = Siswa::factory()->create(['nama_lengkap' => 'Uji Topik', 'kelas' => '7A']);
        $siswa->exp()->create(['total_exp' => 0]);
        $siswa->strek()->create(['current_streak' => 0, 'highest_streak' => 0]);
        ProgresSiswa::create([
            'siswa_id' => $siswa->id,
            'level_materi_id' => $unit->id,
            'status' => ProgresSiswa::STATUS_BERJALAN,
        ]);

        return $siswa;
    }

    public function test_dashboard_menampilkan_topik_dari_unit_yang_sedang_berjalan(): void
    {
        $topikA = $this->topik('Basa Saben Dina', 1);
        $topikB = $this->topik('Aksara lan Sastra', 2);
        $unitA = $this->unit($topikA, 'Dasar', 1);
        $this->unit($topikB, 'Aksara Jawa', 1);

        $siswa = $this->siswaDenganProgres($unitA);

        // Beranda nuduhake unit saka topik sing aktif (topik A), dudu unit topik liya.
        $this->actingAs($siswa, 'siswa')->get('/')
            ->assertOk()
            ->assertSee('Dasar')
            ->assertDontSee('Aksara Jawa');
    }

    public function test_superadmin_dapat_mengelola_topik(): void
    {
        $admin = Superadmin::factory()->create();

        $this->actingAs($admin, 'superadmin')->get('/superadmin/topik')
            ->assertOk()
            ->assertSee('Manajemen Topik');

        $this->actingAs($admin, 'superadmin')->post('/superadmin/topik', [
            'nama' => 'Topik Anyar',
            'deskripsi' => 'Deskripsi',
            'urutan' => 1,
        ])->assertRedirect(route('superadmin.topik'));

        $this->assertDatabaseHas('topik', ['nama' => 'Topik Anyar']);

        $topik = Topik::where('nama', 'Topik Anyar')->firstOrFail();

        $this->actingAs($admin, 'superadmin')->get('/superadmin/topik/'.$topik->id.'/edit')->assertOk();

        $this->actingAs($admin, 'superadmin')->put('/superadmin/topik/'.$topik->id, [
            'nama' => 'Topik Diowahi',
            'urutan' => 2,
        ])->assertRedirect();

        $this->assertDatabaseHas('topik', ['id' => $topik->id, 'nama' => 'Topik Diowahi']);

        $this->actingAs($admin, 'superadmin')->delete('/superadmin/topik/'.$topik->id)->assertRedirect();
        $this->assertDatabaseMissing('topik', ['id' => $topik->id]);
    }

    public function test_superadmin_dapat_membuat_unit_dengan_topik(): void
    {
        $admin = Superadmin::factory()->create();
        $topik = $this->topik('Basa', 1);

        $this->actingAs($admin, 'superadmin')->post('/superadmin/level-materi', [
            'topik_id' => $topik->id,
            'nama_materi' => 'Unit Anyar',
            'deskripsi' => 'Deskripsi',
            'reward_exp' => 100,
            'urutan' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('level_materi', ['nama_materi' => 'Unit Anyar', 'topik_id' => $topik->id]);
    }

    public function test_siswa_dapat_memilih_topik_lain(): void
    {
        $topikA = $this->topik('Basa Saben Dina', 1);
        $topikB = $this->topik('Aksara lan Sastra', 2);
        $unitA = $this->unit($topikA, 'Dasar', 1);
        $unitB = $this->unit($topikB, 'Aksara Jawa', 1);

        $siswa = $this->siswaDenganProgres($unitA);

        // Kaca pilih topik tampil.
        $this->actingAs($siswa, 'siswa')->get('/pilih-topik')
            ->assertOk()
            ->assertSee('Pilih Topik')
            ->assertSee('Basa Saben Dina')
            ->assertSee('Aksara lan Sastra');

        // Pilih topik B.
        $this->actingAs($siswa, 'siswa')->get(route('siswa.topik.pilih', $topikB))
            ->assertRedirect(route('siswa.dashboard'))
            ->assertSessionHas('topik_id', $topikB->id);

        // Beranda sekarang nuduhake unit saka topik B.
        $this->actingAs($siswa, 'siswa')->get('/')
            ->assertOk()
            ->assertSee($unitB->nama_materi)
            ->assertDontSee('Dasar');
    }
}
