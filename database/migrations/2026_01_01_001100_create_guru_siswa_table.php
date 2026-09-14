<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->string('kelas');
            $table->string('mata_pelajaran')->default('Bahasa Jawa');
            $table->timestamps();

            $table->unique(['guru_id', 'siswa_id', 'kelas', 'mata_pelajaran'], 'guru_siswa_unik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_siswa');
    }
};
