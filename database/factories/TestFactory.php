<?php

namespace Database\Factories;

use App\Models\LevelMateri;
use App\Models\Test;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Test>
 */
class TestFactory extends Factory
{
    protected $model = Test::class;

    public function definition(): array
    {
        return [
            'level_materi_id' => LevelMateri::factory(),
            'nama_test' => 'Paket '.fake()->words(2, true),
            'deskripsi' => fake()->sentence(10),
        ];
    }
}
