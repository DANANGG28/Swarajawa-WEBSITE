<?php

namespace Database\Factories;

use App\Models\Superadmin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Superadmin>
 */
class SuperadminFactory extends Factory
{
    protected $model = Superadmin::class;

    public function definition(): array
    {
        return [
            'nama_lengkap' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'no_telpon' => fake()->numerify('08##########'),
            'password' => 'password',
        ];
    }
}
