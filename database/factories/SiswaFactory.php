<?php

namespace Database\Factories;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Siswa>
 */
class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    public function definition(): array
    {
        return [
            'nis' => fake()->unique()->numerify('##########'),
            'nama_lengkap' => fake()->name(),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'kelas' => fake()->randomElement(['7A', '7B', '7C']),
            'no_telpon' => fake()->numerify('08##########'),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ];
    }
}
