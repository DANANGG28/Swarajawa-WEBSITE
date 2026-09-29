<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Superadmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SuperadminDashboardApiTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_api_guru_store_validates_status_pegawaian_whitelist(): void
    {
        Sanctum::actingAs(Superadmin::factory()->create());

        $response = $this->postJson('/api/superadmin/guru', [
            'nip' => '198901012015011001',
            'nama_lengkap' => 'Pak Joko',
            'jenis_kelamin' => 'L',
            'status_pegawaian' => 'INVALID_STATUS',
            'no_telpon' => '081234567899',
            'email' => 'joko.guru@sekolah.sch.id',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status_pegawaian']);

        $validResponse = $this->postJson('/api/superadmin/guru', [
            'nip' => '198901012015011001',
            'nama_lengkap' => 'Pak Joko',
            'jenis_kelamin' => 'L',
            'status_pegawaian' => 'PKWTT',
            'no_telpon' => '081234567899',
            'email' => 'joko.guru@sekolah.sch.id',
            'password' => 'password123',
        ]);

        $validResponse->assertStatus(201);
        $this->assertDatabaseHas('guru', [
            'nip' => '198901012015011001',
            'status_pegawaian' => 'PKWTT',
        ]);
    }

    public function test_api_siswa_store_and_update_validate_duplicate_nis(): void
    {
        Sanctum::actingAs(Superadmin::factory()->create());

        $siswaA = Siswa::factory()->create(['nis' => '2026010001', 'nama_lengkap' => 'Siswa Pertama']);
        $siswaB = Siswa::factory()->create(['nis' => '2026010002', 'nama_lengkap' => 'Siswa Kedua']);

        // Duplicate on store
        $storeResponse = $this->postJson('/api/superadmin/siswa', [
            'nis' => '2026010001',
            'nama_lengkap' => 'Siswa Baru',
            'jenis_kelamin' => 'L',
            'email' => 'siswa.baru@sekolah.sch.id',
            'password' => 'password123',
        ]);

        $storeResponse->assertStatus(422)
            ->assertJsonValidationErrors(['nis']);

        // Duplicate on update
        $updateResponse = $this->putJson("/api/superadmin/siswa/{$siswaA->id}", [
            'nis' => '2026010002',
        ]);

        $updateResponse->assertStatus(422)
            ->assertJsonValidationErrors(['nis']);

        // Keeping same NIS on update is allowed
        $validUpdateResponse = $this->putJson("/api/superadmin/siswa/{$siswaA->id}", [
            'nis' => '2026010001',
            'nama_lengkap' => 'Siswa Pertama Updated',
        ]);

        $validUpdateResponse->assertOk();
    }
}
