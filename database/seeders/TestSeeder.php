<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\LevelMateri;
use App\Models\Soal;
use App\Models\Test;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        $buSri = Guru::query()->where('email', 'bu.sri@sinaujowo.test')->first();
        $level1 = LevelMateri::query()->where('urutan', 1)->first();

        if (! $buSri || ! $level1) {
            return;
        }

        // 1) Test campuran beberapa tipe_soal (pengalaman variatif).
        $campuran = Test::updateOrCreate(
            ['level_materi_id' => $level1->id, 'nama_test' => 'Latihan Campuran Level 1'],
            [
                'deskripsi' => 'Paket latihan campuran: pilihan ganda, susun ukara, pencocokan, dan kuis suara.',
                'guru_id' => $buSri->id,
            ],
        );

        $mixedSoal = Soal::query()
            ->where('level_materi_id', $level1->id)
            ->orderBy('id')
            ->get();

        $pivot = [];
        foreach ($mixedSoal->values() as $index => $soal) {
            $pivot[$soal->id] = ['urutan' => $index + 1];
        }
        $campuran->soal()->sync($pivot);

        // 2) Test satu tipe saja untuk remedial/drilling (keputusan PRD v1.7).
        $remedial = Test::updateOrCreate(
            ['level_materi_id' => $level1->id, 'nama_test' => 'Remedial Susun Ukara'],
            [
                'deskripsi' => 'Paket fokus satu tipe soal (susun ukara) untuk latihan ulang.',
                'guru_id' => $buSri->id,
            ],
        );

        $susunSoal = Soal::query()
            ->where('level_materi_id', $level1->id)
            ->where('tipe_soal', Soal::TIPE_SUSUN_KALIMAT)
            ->orderBy('id')
            ->get();

        $pivot = [];
        foreach ($susunSoal->values() as $index => $soal) {
            $pivot[$soal->id] = ['urutan' => $index + 1];
        }
        $remedial->soal()->sync($pivot);
    }
}
