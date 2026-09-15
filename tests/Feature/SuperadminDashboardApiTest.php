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
}
