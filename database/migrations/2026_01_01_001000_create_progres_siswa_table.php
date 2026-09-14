<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progres_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('level_materi_id')->constrained('level_materi')->cascadeOnDelete();
            $table->string('status')->default('terkunci');
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'level_materi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progres_siswa');
    }
};
