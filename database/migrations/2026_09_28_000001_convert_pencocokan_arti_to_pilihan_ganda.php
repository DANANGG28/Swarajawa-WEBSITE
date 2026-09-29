<?php

use App\Models\JawabanSiswa;
use App\Models\Soal;
use Database\Seeders\SoalSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus jawaban siswa dan soal bertipe pencocokan_arti dan puzzle_pakaian_adat
        $unsupportedIds = Soal::query()
            ->whereIn('tipe_soal', ['pencocokan_arti', 'puzzle_pakaian_adat'])
            ->pluck('id');

        if ($unsupportedIds->isNotEmpty()) {
            JawabanSiswa::query()->whereIn('soal_id', $unsupportedIds)->delete();
            Soal::query()->whereIn('id', $unsupportedIds)->delete();
        }

        // Jalankan seeder soal agar soal-soal pengganti (pilihan ganda) langsung tersedia
        if (class_exists(SoalSeeder::class)) {
            (new SoalSeeder)->run();
        }
    }

    public function down(): void
    {
        // Tidak perlu rollback karena tipe pencocokan_arti sudah ditiadakan
    }
};
