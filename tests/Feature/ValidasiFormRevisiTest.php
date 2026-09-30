<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\LevelMateri;
use App\Models\Pembahasan;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Superadmin;
use App\Models\Topik;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidasiFormRevisiTest extends TestCase
{
    use RefreshDatabase;

    public function test_nama_lengkap_hanya_boleh_huruf_dan_spasi(): void
    {
        $admin = Superadmin::factory()->create();

        // Mengandung angka -> tolak
        $this->actingAs($admin, 'superadmin')->post(route('superadmin.siswa.store'), [
            'nis' => '12345',
            'nama_lengkap' => 'Budi 123',
            'jenis_kelamin' => 'L',
            'email' => 'budi123@test.test',
            'password' => 'password123',
        ])->assertSessionHasErrors('nama_lengkap');

        // Mengandung simbol -> tolak
        $this->actingAs($admin, 'superadmin')->post(route('superadmin.siswa.store'), [
            'nis' => '12346',
            'nama_lengkap' => 'Budi@Santoso!',
            'jenis_kelamin' => 'L',
            'email' => 'budis@test.test',
            'password' => 'password123',
        ])->assertSessionHasErrors('nama_lengkap');

        // Hanya huruf dan spasi -> lolos
        $this->actingAs($admin, 'superadmin')->post(route('superadmin.siswa.store'), [
            'nis' => '12347',
            'nama_lengkap' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'email' => 'budi.santoso@test.test',
            'password' => 'password123',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('siswa', ['email' => 'budi.santoso@test.test']);
    }

    public function test_nis_hanya_angka_dan_harus_unik(): void
    {
        $admin = Superadmin::factory()->create();

        // NIS mengandung huruf -> ditolak
        $this->actingAs($admin, 'superadmin')->post(route('superadmin.siswa.store'), [
            'nis' => 'NIS12345',
            'nama_lengkap' => 'Siswa Pertama',
            'jenis_kelamin' => 'L',
            'email' => 'siswa1@test.test',
            'password' => 'password123',
        ])->assertSessionHasErrors('nis');

        // Simpan siswa 1 dengan NIS 99999
        $this->actingAs($admin, 'superadmin')->post(route('superadmin.siswa.store'), [
            'nis' => '99999',
            'nama_lengkap' => 'Siswa Satu',
            'jenis_kelamin' => 'L',
            'email' => 'siswa1@test.test',
            'password' => 'password123',
        ])->assertSessionHasNoErrors();

        // Simpan siswa 2 dengan NIS yang SAMA (99999) -> HARUS DITOLAK karena NIS wajib unik
        $this->actingAs($admin, 'superadmin')->post(route('superadmin.siswa.store'), [
            'nis' => '99999',
            'nama_lengkap' => 'Siswa Dua',
            'jenis_kelamin' => 'P',
            'email' => 'siswa2@test.test',
            'password' => 'password123',
        ])->assertSessionHasErrors('nis');

        $this->assertEquals(1, Siswa::where('nis', '99999')->count());
    }

    public function test_topik_case_insensitive_duplicate_ditolak(): void
    {
        $admin = Superadmin::factory()->create();

        Topik::create(['nama' => 'Aksara Jawa', 'urutan' => 1]);

        // Huruf sama tapi beda besar kecil -> tolak
        $this->actingAs($admin, 'superadmin')->post(route('superadmin.topik.store'), [
            'nama' => 'aksara jawa',
            'urutan' => 2,
        ])->assertSessionHasErrors('nama');

        $this->actingAs($admin, 'superadmin')->post(route('superadmin.topik.store'), [
            'nama' => 'AKSARA JAWA',
            'urutan' => 3,
        ])->assertSessionHasErrors('nama');
    }

    public function test_unit_level_materi_case_insensitive_duplicate_per_topik_ditolak(): void
    {
        $admin = Superadmin::factory()->create();
        $topikA = Topik::create(['nama' => 'Topik A', 'urutan' => 1]);
        $topikB = Topik::create(['nama' => 'Topik B', 'urutan' => 2]);

        LevelMateri::create([
            'topik_id' => $topikA->id,
            'nama_materi' => 'Unggah Ungguh',
            'reward_exp' => 10,
            'urutan' => 1,
        ]);

        // Duplikat beda besar kecil pada topik yang sama -> tolak
        $this->actingAs($admin, 'superadmin')->post(route('superadmin.level-materi.store'), [
            'topik_id' => $topikA->id,
            'nama_materi' => 'unggah ungguh',
            'reward_exp' => 10,
            'urutan' => 2,
        ])->assertSessionHasErrors('nama_materi');

        // Beda topik -> boleh
        $this->actingAs($admin, 'superadmin')->post(route('superadmin.level-materi.store'), [
            'topik_id' => $topikB->id,
            'nama_materi' => 'unggah ungguh',
            'reward_exp' => 10,
            'urutan' => 1,
        ])->assertSessionHasNoErrors();
    }

    public function test_bagian_pembahasan_case_insensitive_duplicate_per_level_ditolak(): void
    {
        $admin = Superadmin::factory()->create();
        $level = LevelMateri::factory()->create();

        Pembahasan::create([
            'level_materi_id' => $level->id,
            'nama' => 'Tembung Ngoko',
            'urutan' => 1,
        ]);

        // Duplikat beda besar kecil -> tolak
        $this->actingAs($admin, 'superadmin')->post(route('superadmin.pembahasan.store'), [
            'level_materi_id' => $level->id,
            'nama' => 'tembung ngoko',
        ])->assertSessionHasErrors('nama');
    }

    public function test_soal_case_insensitive_duplicate_per_level_ditolak(): void
    {
        $admin = Superadmin::factory()->create();
        $level = LevelMateri::factory()->create();

        Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => 'pilihan_ganda',
            'pertanyaan' => 'Salam nalika esuk yaiku ...',
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
        ]);

        // Tambah soal dengan pertanyaan sama beda huruf besar kecil -> tolak
        $this->actingAs($admin, 'superadmin')->post(route('superadmin.soal.store'), [
            'level_materi_id' => $level->id,
            'tipe_soal' => 'pilihan_ganda',
            'pertanyaan' => 'salam nalika esuk yaiku ...',
            'kunci_jawaban_raw' => json_encode(['jawaban' => 'A']),
            'bobot_exp' => 10,
        ])->assertSessionHasErrors('pertanyaan');
    }

    public function test_user_nama_dan_email_case_insensitive_duplicate_ditolak(): void
    {
        $admin = Superadmin::factory()->create();

        Siswa::create([
            'nis' => '11111',
            'nama_lengkap' => 'Andi Prasetyo',
            'jenis_kelamin' => 'L',
            'email' => 'andi@test.test',
            'password' => 'password',
        ]);

        // Nama siswa sama beda besar kecil -> tolak
        $this->actingAs($admin, 'superadmin')->post(route('superadmin.siswa.store'), [
            'nis' => '22222',
            'nama_lengkap' => 'andi prasetyo',
            'jenis_kelamin' => 'L',
            'email' => 'lain@test.test',
            'password' => 'password',
        ])->assertSessionHasErrors('nama_lengkap');

        // Email sama beda besar kecil -> tolak
        $this->actingAs($admin, 'superadmin')->post(route('superadmin.siswa.store'), [
            'nis' => '33333',
            'nama_lengkap' => 'Nama Beda',
            'jenis_kelamin' => 'L',
            'email' => 'ANDI@TEST.TEST',
            'password' => 'password',
        ])->assertSessionHasErrors('email');
    }

    public function test_no_telepon_duplikat_ditolak(): void
    {
        $admin = Superadmin::factory()->create();

        Siswa::create([
            'nis' => '11111',
            'nama_lengkap' => 'Siswa Telepon Satu',
            'jenis_kelamin' => 'L',
            'no_telpon' => '081234567890',
            'email' => 'telp1@test.test',
            'password' => 'password',
        ]);

        // No telepon sama pada siswa lain -> tolak
        $this->actingAs($admin, 'superadmin')->post(route('superadmin.siswa.store'), [
            'nis' => '22222',
            'nama_lengkap' => 'Siswa Telepon Dua',
            'jenis_kelamin' => 'P',
            'no_telpon' => '081234567890',
            'email' => 'telp2@test.test',
            'password' => 'password',
        ])->assertSessionHasErrors('no_telpon');

        // No telepon sama pada guru -> tolak
        $this->actingAs($admin, 'superadmin')->post(route('superadmin.guru.store'), [
            'role' => 'guru',
            'nip' => '198001012005011001',
            'nama_lengkap' => 'Guru Telepon',
            'jenis_kelamin' => 'L',
            'no_telpon' => '081234567890',
            'email' => 'guru.telp@test.test',
            'password' => 'password',
        ])->assertSessionHasErrors('no_telpon');
    }

    public function test_api_register_siswa_validasi_lengkap(): void
    {
        Siswa::create([
            'nis' => '11111',
            'nama_lengkap' => 'Siswa Eksis',
            'jenis_kelamin' => 'L',
            'no_telpon' => '081211112222',
            'email' => 'eksis@test.test',
            'password' => 'password123',
        ]);

        // Nama mengandung angka -> 422
        $this->postJson('/api/auth/siswa/register', [
            'nis' => '22222',
            'nama_lengkap' => 'Budi 123',
            'jenis_kelamin' => 'L',
            'email' => 'budi123@test.test',
            'password' => 'password123',
        ])->assertStatus(422)->assertJsonValidationErrors('nama_lengkap');

        // Nama duplikat case-insensitive -> 422
        $this->postJson('/api/auth/siswa/register', [
            'nis' => '22222',
            'nama_lengkap' => 'siswa eksis',
            'jenis_kelamin' => 'L',
            'email' => 'lain@test.test',
            'password' => 'password123',
        ])->assertStatus(422)->assertJsonValidationErrors('nama_lengkap');

        // No telepon duplikat -> 422
        $this->postJson('/api/auth/siswa/register', [
            'nis' => '22222',
            'nama_lengkap' => 'Siswa Baru',
            'jenis_kelamin' => 'L',
            'no_telpon' => '081211112222',
            'email' => 'baru@test.test',
            'password' => 'password123',
        ])->assertStatus(422)->assertJsonValidationErrors('no_telpon');

        // NIS sama -> LOLOS (karena NIS tidak unik)
        $this->postJson('/api/auth/siswa/register', [
            'nis' => '11111',
            'nama_lengkap' => 'Siswa Unik Baru',
            'jenis_kelamin' => 'P',
            'email' => 'sukses@test.test',
            'password' => 'password123',
        ])->assertStatus(201);
    }

    public function test_api_soal_case_insensitive_duplicate_ditolak(): void
    {
        $guru = Guru::factory()->create();
        $level = LevelMateri::factory()->create();

        Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => 'pilihan_ganda',
            'pertanyaan' => 'Pitakon bab aksara?',
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
            'guru_id' => $guru->id,
        ]);

        $this->actingAs($guru, 'guru')->postJson('/api/soal', [
            'level_materi_id' => $level->id,
            'tipe_soal' => 'pilihan_ganda',
            'pertanyaan' => 'PITAKON BAB AKSARA?',
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
        ])->assertStatus(422)->assertJsonValidationErrors('pertanyaan');
    }
}
