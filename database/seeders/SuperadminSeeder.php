<?php

namespace Database\Seeders;

use App\Models\Superadmin;
use Illuminate\Database\Seeder;

class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        Superadmin::updateOrCreate(
            ['email' => 'superadmin@sinaujowo.test'],
            [
                'nama_lengkap' => 'Pak Dedi Prasetyo',
                'no_telpon' => '081200000001',
                'password' => 'password',
            ],
        );
    }
}
