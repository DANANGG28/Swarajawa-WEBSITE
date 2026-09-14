<?php

namespace Database\Factories;

use App\Models\LevelMateri;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LevelMateri>
 */
class LevelMateriFactory extends Factory
{
    protected $model = LevelMateri::class;

    public function definition(): array
    {
        return [
            'nama_materi' => fake()->unique()->sentence(3),
            'deskripsi' => fake()->sentence(12),
            'reward_exp' => fake()->numberBetween(50, 300),
            'urutan' => fake()->numberBetween(1, 20),
        ];
    }
}
