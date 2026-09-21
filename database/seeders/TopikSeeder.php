<?php

namespace Database\Seeders;

use App\Models\LevelMateri;
use App\Models\Topik;
use Illuminate\Database\Seeder;

class TopikSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'nama' => 'Basa Jawa Saben Dina',
                'deskripsi' => 'Salam, sapaan, lan tembung-tembung padinan kanggo pacelathon saben dina.',
                'urutan' => 1,
                'units' => ['Dasar', 'Unggah-ungguh', 'Bahasa sehari hari'],
            ],
            [
                'nama' => 'Aksara lan Sastra',
                'deskripsi' => 'Aksara Jawa, sandhangan, paribasan, lan tembung entar.',
                'urutan' => 2,
                'units' => ['Aksara Jawa', 'Peribahasa'],
            ],
            [
                'nama' => 'Budaya lan Crita Rakyat',
                'deskripsi' => 'Busana adat, omah tradhisional, lan crita rakyat tanah Jawa.',
                'urutan' => 3,
                'units' => ['Cerita Rakyat'],
            ],
        ];

        $firstTopik = null;

        foreach ($items as $item) {
            $topik = Topik::updateOrCreate(
                ['nama' => $item['nama']],
                [
                    'deskripsi' => $item['deskripsi'],
                    'urutan' => $item['urutan'],
                ],
            );

            $firstTopik ??= $topik;

            LevelMateri::query()
                ->whereIn('nama_materi', $item['units'])
                ->update(['topik_id' => $topik->id]);
        }

        // Unit lawas sing durung kagolong topik apa-apa dilebokake menyang topik kapisan.
        if ($firstTopik) {
            LevelMateri::query()->whereNull('topik_id')->update(['topik_id' => $firstTopik->id]);
        }
    }
}
