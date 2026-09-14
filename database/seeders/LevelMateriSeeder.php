<?php

namespace Database\Seeders;

use App\Models\LevelMateri;
use Illuminate\Database\Seeder;

class LevelMateriSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            [
                'nama_materi' => 'Dasar',
                'deskripsi' => 'Kosakata dasar, salam pasrawungan, dan pengenalan unggah-ungguh basa.',
                'reward_exp' => 100,
                'urutan' => 1,
            ],
            [
                'nama_materi' => 'Unggah-ungguh',
                'deskripsi' => 'Tata krama berbahasa: Ngoko Lugu, Ngoko Alus, Krama Lugu, Krama Alus, dan Krama Inggil.',
                'reward_exp' => 150,
                'urutan' => 2,
            ],
            [
                'nama_materi' => 'Aksara Jawa',
                'deskripsi' => 'Hanacaraka, sandhangan swara, pasangan, aksara murda, dan latihan menulis (tracing).',
                'reward_exp' => 200,
                'urutan' => 3,
            ],
            [
                'nama_materi' => 'Peribahasa',
                'deskripsi' => 'Bebasan, saloka, paribasan, dan tembung entar beserta maknanya.',
                'reward_exp' => 150,
                'urutan' => 4,
            ],
            [
                'nama_materi' => 'Cerita Rakyat',
                'deskripsi' => 'Apresiasi cerita rakyat Jawa dan artefak budaya: busana adat, rumah adat, kesenian tradisional.',
                'reward_exp' => 250,
                'urutan' => 5,
            ],
        ];

        foreach ($levels as $level) {
            LevelMateri::updateOrCreate(['nama_materi' => $level['nama_materi']], $level);
        }
    }
}
