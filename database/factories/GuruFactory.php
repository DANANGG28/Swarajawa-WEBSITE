<?php

namespace Database\Factories;

use App\Models\Guru;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guru>
 */
class GuruFactory extends Factory
{
    protected $model = Guru::class;

    public function definition(): array
    {
        return [
            'nip' => fake()->unique()->numerify('##################'),
            'nama_lengkap' => 'Bu '.fake()->firstNameFemale().' '.fake()->lastName(),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'status_pegawaian' => fake()->randomElement(['PNS', 'PPPK', 'GTT']),
            'no_telpon' => fake()->numerify('08##########'),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ];
    }
}
