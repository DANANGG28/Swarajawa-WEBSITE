<?php

namespace Database\Factories;

use App\Models\Exp;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exp>
 */
class ExpFactory extends Factory
{
    protected $model = Exp::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'total_exp' => fake()->numberBetween(0, 2000),
        ];
    }
}
