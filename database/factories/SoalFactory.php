<?php

namespace Database\Factories;

use App\Models\LevelMateri;
use App\Models\Soal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Soal>
 */
class SoalFactory extends Factory
{
    protected $model = Soal::class;

    public function definition(): array
    {
        return [
            'level_materi_id' => LevelMateri::factory(),
            'tipe_soal' => Soal::TIPE_PILIHAN_GANDA,
            'pertanyaan' => fake()->sentence(6).'?',
            'opsi_jawaban' => [
                ['label' => 'A', 'teks' => fake()->word()],
                ['label' => 'B', 'teks' => fake()->word()],
                ['label' => 'C', 'teks' => fake()->word()],
                ['label' => 'D', 'teks' => fake()->word()],
            ],
            'kunci_jawaban' => ['jawaban' => 'A'],
            'bobot_exp' => 10,
        ];
    }
}
