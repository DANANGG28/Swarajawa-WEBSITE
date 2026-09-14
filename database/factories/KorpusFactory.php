<?php

namespace Database\Factories;

use App\Models\Korpus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Korpus>
 */
class KorpusFactory extends Factory
{
    protected $model = Korpus::class;

    public function definition(): array
    {
        return [
            'judul' => fake()->sentence(3),
            'kategori' => fake()->randomElement(['unggah-ungguh', 'aksara', 'paribasan', 'budaya']),
            'konten' => fake()->paragraph(4),
            'sumber' => 'Kamus Unggah-Ungguh Basa Jawa',
        ];
    }
}
