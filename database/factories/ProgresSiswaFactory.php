<?php

namespace Database\Factories;

use App\Models\LevelMateri;
use App\Models\ProgresSiswa;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProgresSiswa>
 */
class ProgresSiswaFactory extends Factory
{
    protected $model = ProgresSiswa::class;

    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'level_materi_id' => LevelMateri::factory(),
            'status' => ProgresSiswa::STATUS_BERJALAN,
            'tanggal_selesai' => null,
        ];
    }
}
