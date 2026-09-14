<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_materi_id')->constrained('level_materi')->cascadeOnDelete();
            $table->string('tipe_soal');
            $table->text('pertanyaan');
            $table->jsonb('opsi_jawaban')->nullable();
            $table->jsonb('kunci_jawaban')->nullable();
            $table->string('media_audio_url')->nullable();
            $table->integer('bobot_exp')->default(10);
            $table->foreignId('guru_id')->nullable()->constrained('guru')->nullOnDelete();
            $table->foreignId('superadmin_id')->nullable()->constrained('superadmin')->nullOnDelete();
            $table->timestamps();

            $table->index('tipe_soal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal');
    }
};
