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
            SiswaSeeder::class,
            KorpusSeeder::class,
            SoalSeeder::class,
        ]);
    }
}
