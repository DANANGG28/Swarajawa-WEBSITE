<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jawaban_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('soal_id')->constrained('soal')->cascadeOnDelete();
            $table->integer('skor_tertinggi')->default(0);
            $table->integer('exp_diberikan')->default(0);
            $table->integer('jumlah_percobaan')->default(1);
            $table->timestamps();

            $table->unique(['siswa_id', 'soal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_siswa');
    }
};
