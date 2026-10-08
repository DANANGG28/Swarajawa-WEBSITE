<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Superadmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WebDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/guru/dashboard')->assertRedirect(route('masuk'));
        $this->get('/superadmin/dashboard')->assertRedirect(route('masuk'));
        $this->get('/papan-skor')->assertRedirect(route('masuk'));
    }

    public function test_guest_sees_landing_page_on_homepage(): void
    {
        $this->get('/')->assertOk()->assertSee('Mulai Belajar Gratis');
    }

    public function test_guru_can_access_guru_area_but_not_superadmin_area(): void
    {
        $guru = Guru::factory()->create();
        $level = LevelMateri::create(['nama_materi' => 'Dasar', 'deskripsi' => 'x', 'reward_exp' => 100, 'urutan' => 1]);

        $this->actingAs($guru, 'guru')->get('/guru/dashboard')->assertOk();
        $this->actingAs($guru, 'guru')->get('/guru/level-materi')->assertOk();
        $this->actingAs($guru, 'guru')->get('/guru/soal?level_materi_id='.$level->id)->assertOk();
        $this->actingAs($guru, 'guru')->get('/superadmin/dashboard')->assertForbidden();
    }

    public function test_guru_can_create_level_materi_via_web_form(): void
    {
        $guru = Guru::factory()->create();

        $this->actingAs($guru, 'guru')->post('/guru/level-materi', [
            'nama_materi' => 'Level Anyar Guru',
            'deskripsi' => 'Deskripsi',
            'reward_exp' => 120,
            'urutan' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('level_materi', ['nama_materi' => 'Level Anyar Guru']);
    }

    public function test_superadmin_can_access_superadmin_area_but_not_guru_area(): void
    {
        $admin = Superadmin::factory()->create();
        $level = LevelMateri::create(['nama_materi' => 'Dasar', 'deskripsi' => 'x', 'reward_exp' => 100, 'urutan' => 1]);

        foreach (['/superadmin/dashboard', '/superadmin/guru', '/superadmin/siswa', '/superadmin/level-materi'] as $url) {
            $this->actingAs($admin, 'superadmin')->get($url)->assertOk();
        }

        $this->actingAs($admin, 'superadmin')->get('/superadmin/soal?level_materi_id='.$level->id)->assertOk();

        $this->actingAs($admin, 'superadmin')->get('/guru/dashboard')->assertForbidden();
    }

    public function test_login_redirects_each_role_to_its_own_dashboard(): void
    {
        Guru::factory()->create(['email' => 'guru@test.test', 'password' => 'password']);
        Superadmin::factory()->create(['email' => 'admin@test.test', 'password' => 'password']);

        $this->post('/masuk', ['email' => 'guru@test.test', 'password' => 'password'])
            ->assertRedirect(route('guru.dashboard'));

        $this->post('/masuk', ['email' => 'admin@test.test', 'password' => 'password'])
            ->assertRedirect(route('superadmin.dashboard'));
    }

    public function test_home_redirects_guru_and_superadmin_to_their_dashboard(): void
    {
        $guru = Guru::factory()->create();
        $admin = Superadmin::factory()->create();

        $this->actingAs($guru, 'guru')->get('/')->assertRedirect(route('guru.dashboard'));
        $this->actingAs($admin, 'superadmin')->get('/')->assertRedirect(route('superadmin.dashboard'));
    }

    public function test_guru_can_create_soal_via_web_form(): void
    {
        $guru = Guru::factory()->create();
        $level = LevelMateri::create(['nama_materi' => 'Dasar', 'deskripsi' => 'x', 'reward_exp' => 100, 'urutan' => 1]);

        $this->actingAs($guru, 'guru')->post('/guru/soal', [
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Pitakon uji?',
            'opsi_jawaban_raw' => json_encode([['label' => 'A', 'teks' => 'Bener']]),
            'kunci_jawaban_raw' => json_encode(['jawaban' => 'A']),
            'bobot_exp' => 10,
        ])->assertRedirect();

        $this->assertDatabaseHas('soal', ['pertanyaan' => 'Pitakon uji?', 'guru_id' => $guru->id]);
    }

    public function test_guru_cannot_update_another_gurus_soal_via_web(): void
    {
        $guruA = Guru::factory()->create();
        $guruB = Guru::factory()->create();
        $level = LevelMateri::create(['nama_materi' => 'Dasar', 'deskripsi' => 'x', 'reward_exp' => 100, 'urutan' => 1]);

        $soal = Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Pitakon?',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'A']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'guru_id' => $guruA->id,
        ]);

        $this->actingAs($guruB, 'guru')->put("/guru/soal/{$soal->id}", [
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Diowahi',
            'kunci_jawaban_raw' => json_encode(['jawaban' => 'A']),
            'bobot_exp' => 10,
        ])->assertForbidden();
    }

    public function test_superadmin_can_create_level_materi_via_web_form(): void
    {
        $admin = Superadmin::factory()->create();

        $this->actingAs($admin, 'superadmin')->post('/superadmin/level-materi', [
            'nama_materi' => 'Level Anyar',
            'deskripsi' => 'Deskripsi',
            'reward_exp' => 120,
            'urutan' => 6,
        ])->assertRedirect();

        $this->assertDatabaseHas('level_materi', ['nama_materi' => 'Level Anyar']);
    }

    public function test_superadmin_can_create_soal_via_web_form(): void
    {
        $admin = Superadmin::factory()->create();
        $level = LevelMateri::create(['nama_materi' => 'Dasar', 'deskripsi' => 'x', 'reward_exp' => 100, 'urutan' => 1]);

        $this->actingAs($admin, 'superadmin')->post('/superadmin/soal', [
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Pitakon superadmin?',
            'opsi_jawaban_raw' => json_encode([['label' => 'A', 'teks' => 'Bener']]),
            'kunci_jawaban_raw' => json_encode(['jawaban' => 'A']),
            'bobot_exp' => 10,
        ])->assertRedirect(route('superadmin.soal', ['level_materi_id' => $level->id]));

        $this->assertDatabaseHas('soal', [
            'pertanyaan' => 'Pitakon superadmin?',
            'superadmin_id' => $admin->id,
            'guru_id' => null,
        ]);
    }

    public function test_superadmin_soal_and_create_pages_render_for_level(): void
    {
        $admin = Superadmin::factory()->create();
        $level = LevelMateri::create(['nama_materi' => 'Dasar', 'deskripsi' => 'x', 'reward_exp' => 100, 'urutan' => 1]);

        Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Pitakon superadmin?',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'Bener']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
            'superadmin_id' => $admin->id,
        ]);

        $this->actingAs($admin, 'superadmin')->get('/superadmin/soal?level_materi_id='.$level->id)
            ->assertOk()
            ->assertSee('Pitakon superadmin?');

        $this->actingAs($admin, 'superadmin')->get('/superadmin/soal/tambah?level_materi_id='.$level->id)
            ->assertOk()
            ->assertSee('Tambah Soal Anyar');

        $this->actingAs($admin, 'superadmin')->get('/superadmin/soal')->assertRedirect(route('superadmin.level-materi'));
    }

    public function test_siswa_can_access_siswa_area_after_login(): void
    {
        $siswa = Siswa::factory()->create();

        foreach (['/', '/dashboard', '/papan-skor', '/asisten-ai', '/profil'] as $url) {
            $this->actingAs($siswa, 'siswa')->get($url)->assertOk();
        }
    }

    public function test_guru_cannot_access_siswa_area(): void
    {
        $guru = Guru::factory()->create();

        $this->actingAs($guru, 'guru')->get('/papan-skor')->assertForbidden();
    }

    public function test_siswa_web_dashboard_shows_real_levels_and_can_answer(): void
    {
        $level = LevelMateri::create(['nama_materi' => 'Dasar', 'deskripsi' => 'x', 'reward_exp' => 100, 'urutan' => 1]);
        $soal = Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Salam esuk?',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'Sugeng enjing']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
        ]);
        // Soal kalih supados level mboten langsung rampung.
        Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Salam dalu?',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'Sugeng dalu']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
        ]);

        $siswa = Siswa::factory()->create(['nama_lengkap' => 'Uji Siswa', 'kelas' => '7A']);
        $siswa->exp()->create(['total_exp' => 0]);
        $siswa->strek()->create(['current_streak' => 0, 'highest_streak' => 0]);
        ProgresSiswa::create(['siswa_id' => $siswa->id, 'level_materi_id' => $level->id, 'status' => ProgresSiswa::STATUS_BERJALAN]);

        // Beranda menampilkan nama & level dinamis.
        $this->actingAs($siswa, 'siswa')->get('/')
            ->assertOk()
            ->assertSee('Uji Siswa')
            ->assertSee('Dasar');

        // Daftar level kini tampil ing beranda (bukan kaca latihan terpisah).
        // Klik level -> langsung diarahkan ke soal pertama level kasebut.
        $this->actingAs($siswa, 'siswa')->get("/kuis/mulai/{$level->id}")
            ->assertRedirect()
            ->assertRedirectContains('/kuis/pilihan-ganda')
            ->assertRedirectContains('soal_id='.$soal->id);

        // Jawab soal lewat endpoint web -> EXP bertambah.
        $this->actingAs($siswa, 'siswa')->postJson('/kuis/jawab', ['soal_id' => $soal->id, 'jawaban' => 'A'])
            ->assertOk()
            ->assertJson(['skor' => 100, 'exp_didapat' => 10, 'total_exp' => 10]);

        $this->assertDatabaseHas('jawaban_siswa', ['siswa_id' => $siswa->id, 'soal_id' => $soal->id, 'skor_tertinggi' => 100]);
    }

    public function test_locked_level_cannot_be_started(): void
    {
        $level = LevelMateri::create(['nama_materi' => 'Terkunci', 'deskripsi' => 'x', 'reward_exp' => 100, 'urutan' => 1]);
        Soal::create([
            'level_materi_id' => $level->id,
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => 'Pitakon?',
            'opsi_jawaban' => [['label' => 'A', 'teks' => 'A']],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
        ]);

        $siswa = Siswa::factory()->create();
        $siswa->exp()->create(['total_exp' => 0]);
        $siswa->strek()->create(['current_streak' => 0, 'highest_streak' => 0]);
        ProgresSiswa::create(['siswa_id' => $siswa->id, 'level_materi_id' => $level->id, 'status' => ProgresSiswa::STATUS_TERKUNCI]);

        $this->actingAs($siswa, 'siswa')->get("/kuis/mulai/{$level->id}")
            ->assertRedirect(route('siswa.dashboard'));
    }

    public function test_siswa_profile_page_renders_dynamic_badges(): void
    {
        $siswa = Siswa::factory()->create(['nama_lengkap' => 'Budi Santoso', 'kelas' => '8B']);
        $siswa->exp()->create(['total_exp' => 300]);
        $siswa->strek()->create(['current_streak' => 3, 'highest_streak' => 3]);

        $response = $this->actingAs($siswa, 'siswa')->get('/profil');

        $response->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('Gathutkaca Streak Master')
            ->assertSee('Wasasis Utama')
            ->assertSee('Wicara Prigel');
    }

    public function test_superadmin_can_create_guru_with_dropdown_status_pegawaian(): void
    {
        $admin = Superadmin::factory()->create();

        $response = $this->actingAs($admin, 'superadmin')->post('/superadmin/guru', [
            'role' => 'guru',
            'nip' => '198701012015011005',
            'nama_lengkap' => 'Pak Joko Widodo',
            'jenis_kelamin' => 'L',
            'status_pegawaian' => 'PKWTT',
            'no_telpon' => '081234567890',
            'email' => 'joko@sekolah.sch.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('superadmin.guru'));
        $this->assertDatabaseHas('guru', [
            'nip' => '198701012015011005',
            'nama_lengkap' => 'Pak Joko Widodo',
            'status_pegawaian' => 'PKWTT',
        ]);
    }

    public function test_superadmin_cannot_create_guru_with_invalid_status_pegawaian(): void
    {
        $admin = Superadmin::factory()->create();

        $response = $this->actingAs($admin, 'superadmin')->post('/superadmin/guru', [
            'role' => 'guru',
            'nip' => '198701012015011006',
            'nama_lengkap' => 'Pak Bambang',
            'jenis_kelamin' => 'L',
            'status_pegawaian' => 'HONORER_BEBAS',
            'no_telpon' => '081234567891',
            'email' => 'bambang@sekolah.sch.id',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('status_pegawaian');
    }

    public function test_superadmin_can_update_guru_status_pegawaian(): void
    {
        $admin = Superadmin::factory()->create();
        $guru = Guru::factory()->create(['status_pegawaian' => 'PKWT']);

        $response = $this->actingAs($admin, 'superadmin')->put("/superadmin/guru/{$guru->id}", [
            'role' => 'guru',
            'nip' => $guru->nip,
            'nama_lengkap' => $guru->nama_lengkap,
            'jenis_kelamin' => $guru->jenis_kelamin,
            'status_pegawaian' => 'PNS',
            'no_telpon' => $guru->no_telpon,
            'email' => $guru->email,
        ]);

        $response->assertRedirect(route('superadmin.guru'));
        $this->assertDatabaseHas('guru', [
            'id' => $guru->id,
            'status_pegawaian' => 'PNS',
        ]);
    }

    public function test_superadmin_guru_views_render_dropdown_options(): void
    {
        $admin = Superadmin::factory()->create();
        $guru = Guru::factory()->create(['status_pegawaian' => 'PPPK']);

        // Create page
        $createPage = $this->actingAs($admin, 'superadmin')->get('/superadmin/guru/tambah');
        $createPage->assertOk()
            ->assertSee('name="status_pegawaian"', false)
            ->assertSee('value="PKWTT"', false)
            ->assertSee('value="PKWT"', false)
            ->assertSee('value="PPPK"', false)
            ->assertSee('value="PNS"', false);

        // Edit page
        $editPage = $this->actingAs($admin, 'superadmin')->get("/superadmin/guru/{$guru->id}/edit?role=guru");
        $editPage->assertOk()
            ->assertSee('name="status_pegawaian"', false)
            ->assertSee('value="PPPK" selected', false);

        // Detail page
        $detailPage = $this->actingAs($admin, 'superadmin')->get("/superadmin/guru/{$guru->id}?role=guru");
        $detailPage->assertOk()
            ->assertSee('name="status_pegawaian"', false)
            ->assertSee('value="PPPK" selected', false);
    }

    public function test_superadmin_cannot_create_siswa_with_duplicate_nis(): void
    {
        $admin = Superadmin::factory()->create();
        Siswa::factory()->create(['nis' => '2026010001']);

        $response = $this->actingAs($admin, 'superadmin')->post('/superadmin/siswa', [
            'nis' => '2026010001',
            'nama_lengkap' => 'Siswa Baru',
            'jenis_kelamin' => 'L',
            'kelas' => '7A',
            'email' => 'siswabaru@sekolah.sch.id',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('nis');
        $this->assertDatabaseCount('siswa', 1);
    }

    public function test_superadmin_can_update_siswa_keeping_same_nis(): void
    {
        $admin = Superadmin::factory()->create();
        $siswa = Siswa::factory()->create(['nis' => '2026010001', 'nama_lengkap' => 'Nama Awal']);

        $response = $this->actingAs($admin, 'superadmin')->put("/superadmin/siswa/{$siswa->id}", [
            'nis' => '2026010001',
            'nama_lengkap' => 'Nama Anyar',
            'jenis_kelamin' => $siswa->jenis_kelamin,
            'kelas' => '8B',
            'email' => $siswa->email,
        ]);

        $response->assertRedirect(route('superadmin.siswa'));
        $this->assertDatabaseHas('siswa', [
            'id' => $siswa->id,
            'nis' => '2026010001',
            'nama_lengkap' => 'Nama Anyar',
            'kelas' => '8B',
        ]);
    }

    public function test_superadmin_cannot_update_siswa_to_another_siswa_nis(): void
    {
        $admin = Superadmin::factory()->create();
        $siswaA = Siswa::factory()->create(['nis' => '2026010001', 'nama_lengkap' => 'Siswa A']);
        $siswaB = Siswa::factory()->create(['nis' => '2026010002', 'nama_lengkap' => 'Siswa B']);

        $response = $this->actingAs($admin, 'superadmin')->put("/superadmin/siswa/{$siswaA->id}", [
            'nis' => '2026010002',
            'nama_lengkap' => 'Siswa A Edit',
            'jenis_kelamin' => $siswaA->jenis_kelamin,
            'kelas' => $siswaA->kelas,
            'email' => $siswaA->email,
        ]);

        $response->assertSessionHasErrors('nis');
    }

    public function test_superadmin_siswa_views_render_kelas_dropdown_options(): void
    {
        $admin = Superadmin::factory()->create();
        $siswa = Siswa::factory()->create(['kelas' => '8A']);

        // Create page
        $createPage = $this->actingAs($admin, 'superadmin')->get('/superadmin/siswa/tambah');
        $createPage->assertOk()
            ->assertSee('name="kelas"', false)
            ->assertSee('value="7A"', false)
            ->assertSee('value="8A"', false)
            ->assertSee('value="9A"', false);

        // Edit page
        $editPage = $this->actingAs($admin, 'superadmin')->get("/superadmin/siswa/{$siswa->id}/edit");
        $editPage->assertOk()
            ->assertSee('name="kelas"', false)
            ->assertSee('value="8A" selected', false);
    }

    public function test_superadmin_cannot_create_guru_with_duplicate_nip(): void
    {
        $admin = Superadmin::factory()->create();
        Guru::factory()->create(['nip' => '198501012010011001']);

        $response = $this->actingAs($admin, 'superadmin')->post('/superadmin/guru', [
            'role' => 'guru',
            'nip' => '198501012010011001',
            'nama_lengkap' => 'Guru Anyar',
            'jenis_kelamin' => 'L',
            'status_pegawaian' => 'PKWTT',
            'no_telpon' => '081234567800',
            'email' => 'guru.anyar@sekolah.sch.id',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('nip');
        $this->assertDatabaseCount('guru', 1);
    }

    public function test_superadmin_can_update_guru_keeping_same_nip(): void
    {
        $admin = Superadmin::factory()->create();
        $guru = Guru::factory()->create(['nip' => '198501012010011001', 'nama_lengkap' => 'Pak Joko']);

        $response = $this->actingAs($admin, 'superadmin')->put("/superadmin/guru/{$guru->id}", [
            'role' => 'guru',
            'nip' => '198501012010011001',
            'nama_lengkap' => 'Pak Joko Widodo',
            'jenis_kelamin' => $guru->jenis_kelamin,
            'status_pegawaian' => 'PNS',
            'no_telpon' => $guru->no_telpon,
            'email' => $guru->email,
        ]);

        $response->assertRedirect(route('superadmin.guru'));
        $this->assertDatabaseHas('guru', [
            'id' => $guru->id,
            'nip' => '198501012010011001',
            'nama_lengkap' => 'Pak Joko Widodo',
            'status_pegawaian' => 'PNS',
        ]);
    }

    public function test_superadmin_cannot_update_guru_to_another_guru_nip(): void
    {
        $admin = Superadmin::factory()->create();
        $guruA = Guru::factory()->create(['nip' => '198501012010011001', 'nama_lengkap' => 'Guru A']);
        $guruB = Guru::factory()->create(['nip' => '198501012010011002', 'nama_lengkap' => 'Guru B']);

        $response = $this->actingAs($admin, 'superadmin')->put("/superadmin/guru/{$guruA->id}", [
            'role' => 'guru',
            'nip' => '198501012010011002',
            'nama_lengkap' => 'Guru A Edit',
            'jenis_kelamin' => $guruA->jenis_kelamin,
            'no_telpon' => $guruA->no_telpon,
            'email' => $guruA->email,
        ]);

        $response->assertSessionHasErrors('nip');
    }

    public function test_superadmin_cannot_create_guru_with_photo_exceeding_500kb(): void
    {
        Storage::fake('public');
        $admin = Superadmin::factory()->create();
        $largeFile = UploadedFile::fake()->image('profile.jpg')->size(600);

        $response = $this->actingAs($admin, 'superadmin')->post('/superadmin/guru', [
            'role' => 'guru',
            'nip' => '198901012015011007',
            'nama_lengkap' => 'Guru Gambar Besar',
            'jenis_kelamin' => 'L',
            'status_pegawaian' => 'PKWTT',
            'no_telpon' => '081234567899',
            'email' => 'guruharustolak@sekolah.sch.id',
            'password' => 'password123',
            'foto' => $largeFile,
        ]);

        $response->assertSessionHasErrors('foto');
        $this->assertDatabaseMissing('guru', ['email' => 'guruharustolak@sekolah.sch.id']);
    }

    public function test_superadmin_cannot_create_guru_with_disallowed_file_format(): void
    {
        Storage::fake('public');
        $admin = Superadmin::factory()->create();
        $svgFile = UploadedFile::fake()->create('malicious.svg', 100, 'image/svg+xml');

        $response = $this->actingAs($admin, 'superadmin')->post('/superadmin/guru', [
            'role' => 'guru',
            'nip' => '198901012015011008',
            'nama_lengkap' => 'Guru Format Salah',
            'jenis_kelamin' => 'L',
            'status_pegawaian' => 'PKWTT',
            'no_telpon' => '081234567898',
            'email' => 'guruformat@sekolah.sch.id',
            'password' => 'password123',
            'foto' => $svgFile,
        ]);

        $response->assertSessionHasErrors('foto');
        $this->assertDatabaseMissing('guru', ['email' => 'guruformat@sekolah.sch.id']);
    }

    public function test_superadmin_can_create_guru_with_valid_photo_under_500kb(): void
    {
        $admin = Superadmin::factory()->create();
        $validFile = UploadedFile::fake()->image('guru_avatar.png', 200, 200)->size(300);

        $response = $this->actingAs($admin, 'superadmin')->post('/superadmin/guru', [
            'role' => 'guru',
            'nip' => '198901012015011009',
            'nama_lengkap' => 'Guru Valid Foto',
            'jenis_kelamin' => 'L',
            'status_pegawaian' => 'PKWTT',
            'no_telpon' => '081234567897',
            'email' => 'guruvalid@sekolah.sch.id',
            'password' => 'password123',
            'foto' => $validFile,
        ]);

        $response->assertRedirect(route('superadmin.guru'));
        $this->assertDatabaseHas('guru', [
            'email' => 'guruvalid@sekolah.sch.id',
        ]);
        $guru = Guru::where('email', 'guruvalid@sekolah.sch.id')->first();
        $this->assertNotNull($guru->foto);
        $this->assertFileExists(storage_path('image/guru/'.$guru->foto));

        // Clean up created file in storage
        if ($guru->foto && file_exists(storage_path('image/guru/'.$guru->foto))) {
            @unlink(storage_path('image/guru/'.$guru->foto));
        }
    }

    public function test_siswa_cannot_update_profile_photo_exceeding_500kb(): void
    {
        $siswa = Siswa::factory()->create();
        $siswa->exp()->create(['total_exp' => 0]);
        $siswa->strek()->create(['current_streak' => 0, 'highest_streak' => 0]);

        $largeFile = UploadedFile::fake()->image('avatar_siswa.jpg')->size(700);

        $response = $this->actingAs($siswa, 'siswa')->put(route('siswa.profil.update'), [
            'nama_lengkap' => $siswa->nama_lengkap,
            'email' => $siswa->email,
            'jenis_kelamin' => $siswa->jenis_kelamin,
            'kelas' => $siswa->kelas,
            'foto' => $largeFile,
        ]);

        $response->assertSessionHasErrors('foto');
    }

    public function test_siswa_cannot_update_profile_photo_with_disallowed_extension(): void
    {
        $siswa = Siswa::factory()->create();
        $siswa->exp()->create(['total_exp' => 0]);
        $siswa->strek()->create(['current_streak' => 0, 'highest_streak' => 0]);

        $pdfFile = UploadedFile::fake()->create('dokumen.pdf', 200, 'application/pdf');

        $response = $this->actingAs($siswa, 'siswa')->put(route('siswa.profil.update'), [
            'nama_lengkap' => $siswa->nama_lengkap,
            'email' => $siswa->email,
            'jenis_kelamin' => $siswa->jenis_kelamin,
            'kelas' => $siswa->kelas,
            'foto' => $pdfFile,
        ]);

        $response->assertSessionHasErrors('foto');
    }

    public function test_siswa_can_update_profile_photo_with_valid_image_under_500kb(): void
    {
        $siswa = Siswa::factory()->create();
        $siswa->exp()->create(['total_exp' => 0]);
        $siswa->strek()->create(['current_streak' => 0, 'highest_streak' => 0]);

        $validFile = UploadedFile::fake()->image('siswa_avatar.jpg', 200, 200)->size(250);

        $response = $this->actingAs($siswa, 'siswa')->put(route('siswa.profil.update'), [
            'nama_lengkap' => 'Nama Baru Siswa',
            'email' => $siswa->email,
            'jenis_kelamin' => 'P',
            'kelas' => '8A',
            'foto' => $validFile,
        ]);

        $response->assertRedirect(route('siswa.profil'));
        $siswa->refresh();
        $this->assertEquals('Nama Baru Siswa', $siswa->nama_lengkap);
        $this->assertNotNull($siswa->foto);
        $this->assertFileExists(storage_path('image/siswa/'.$siswa->foto));

        // Clean up created file
        if ($siswa->foto && file_exists(storage_path('image/siswa/'.$siswa->foto))) {
            @unlink(storage_path('image/siswa/'.$siswa->foto));
        }
    }

    public function test_siswa_profile_page_renders_logout_button_and_can_logout(): void
    {
        $siswa = Siswa::factory()->create();
        $siswa->exp()->create(['total_exp' => 0]);
        $siswa->strek()->create(['current_streak' => 0, 'highest_streak' => 0]);

        // Verify profile page contains logout action
        $response = $this->actingAs($siswa, 'siswa')->get('/profil');
        $response->assertOk()
            ->assertSee(route('keluar'))
            ->assertSee('Keluar');

        // Verify logout action successfully logs out the user
        $logoutResponse = $this->actingAs($siswa, 'siswa')->post('/keluar');
        $logoutResponse->assertRedirect(route('masuk'));
        $this->assertGuest('siswa');
    }
}
