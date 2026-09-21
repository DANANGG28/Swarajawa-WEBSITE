<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SuperadminSeeder::class,
            GuruSeeder::class,
            LevelMateriSeeder::class,
            PembahasanSeeder::class,
            SoalSeeder::class,
            SiswaSeeder::class,
            KorpusSeeder::class,
        ]);
    }
}
