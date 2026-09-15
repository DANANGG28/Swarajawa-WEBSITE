<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('test_soal');
        Schema::dropIfExists('test');
    }

    public function down(): void
    {
        Schema::create('test', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_materi_id')->constrained('level_materi')->cascadeOnDelete();
            $table->string('nama_test');
            $table->text('deskripsi')->nullable();
            $table->foreignId('guru_id')->nullable()->constrained('guru')->nullOnDelete();
            $table->foreignId('superadmin_id')->nullable()->constrained('superadmin')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('test_soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('test')->cascadeOnDelete();
            $table->foreignId('soal_id')->constrained('soal')->cascadeOnDelete();
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->unique(['test_id', 'soal_id']);
        });
    }
};
