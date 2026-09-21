<?php

namespace Database\Seeders;

use App\Models\LevelMateri;
use App\Models\Pembahasan;
use Illuminate\Database\Seeder;

class PembahasanSeeder extends Seeder
{
    public function run(): void
    {
        $levels = LevelMateri::query()->orderBy('urutan')->get()->keyBy('urutan');

        $items = [
            1 => [
                ['urutan' => 1, 'nama' => 'Salam & Sapaan', 'deskripsi' => 'Tetembungan salam lan sapaan padinan ing basa Jawa.'],
                ['urutan' => 2, 'nama' => 'Tembung lan Ukara Dasar', 'deskripsi' => 'Tembung kriya, tembung kekerabatan, lan nyusun ukara ngoko.'],
            ],
            2 => [
                ['urutan' => 1, 'nama' => 'Krama Inggil', 'deskripsi' => 'Tembung krama inggil kanggo ngajeni wong sing luwih tuwa.'],
                ['urutan' => 2, 'nama' => 'Krama Alus & Penerapan', 'deskripsi' => 'Panganggone krama alus ing ukara lan pacelathon padinan.'],
            ],
            3 => [
                ['urutan' => 1, 'nama' => 'Aksara Legena & Sandhangan Swara', 'deskripsi' => 'Aksara nglegena lan sandhangan swara wulu, suku, taling, pepet.'],
                ['urutan' => 2, 'nama' => 'Nulis Aksara (Tracing)', 'deskripsi' => 'Latihan nggaris aksara Jawa ing kanvas digital.'],
            ],
            4 => [
                ['urutan' => 1, 'nama' => 'Paribasan & Tegese', 'deskripsi' => 'Paribasan Jawa sarta teges lan panganggone.'],
                ['urutan' => 2, 'nama' => 'Tembung Entar', 'deskripsi' => 'Tembung entar lan teges kiasane.'],
            ],
            5 => [
                ['urutan' => 1, 'nama' => 'Busana Adat & Omah Jawa', 'deskripsi' => 'Busana adat lan omah tradhisional Jawa.'],
                ['urutan' => 2, 'nama' => 'Crita Rakyat', 'deskripsi' => 'Legenda lan dongeng saka tanah Jawa.'],
            ],
        ];

        foreach ($items as $levelUrutan => $pembahasans) {
            $level = $levels->get($levelUrutan);

            if (! $level) {
                continue;
            }

            foreach ($pembahasans as $pembahasan) {
                Pembahasan::updateOrCreate(
                    ['level_materi_id' => $level->id, 'urutan' => $pembahasan['urutan']],
                    ['nama' => $pembahasan['nama'], 'deskripsi' => $pembahasan['deskripsi']],
                );
            }
        }
    }
}
