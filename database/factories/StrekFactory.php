<?php

namespace Database\Factories;

use App\Models\Siswa;
use App\Models\Strek;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Strek>
 */
class StrekFactory extends Factory
{
    protected $model = Strek::class;

    public function definition(): array
    {
        $current = fake()->numberBetween(0, 15);

        return [
            'siswa_id' => Siswa::factory(),
            'current_streak' => $current,
            'highest_streak' => max($current, fake()->numberBetween(0, 20)),
            'last_activity_date' => fake()->dateTimeBetween('-5 days', 'now')->format('Y-m-d'),
        ];
    }
}
