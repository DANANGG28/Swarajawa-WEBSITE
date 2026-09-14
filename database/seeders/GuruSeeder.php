<?php

namespace Database\Seeders;

use App\Models\Guru;
use Illuminate\Database\Seeder;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        Guru::updateOrCreate(
            ['email' => 'bu.sri@sinaujowo.test'],
            [
                'nip' => '198304122008012015',
                'nama_lengkap' => 'Bu Sri Handayani',
                'jenis_kelamin' => 'P',
                'status_pegawaian' => 'PNS',
                'no_telpon' => '081200000002',
                'password' => 'password',
            ],
        );

        Guru::updateOrCreate(
            ['email' => 'pak.bagus@sinaujowo.test'],
            [
                'nip' => '199001152015031008',
                'nama_lengkap' => 'Pak Bagus Setyawan',
                'jenis_kelamin' => 'L',
                'status_pegawaian' => 'PPPK',
                'no_telpon' => '081200000003',
                'password' => 'password',
            ],
        );
    }
}
