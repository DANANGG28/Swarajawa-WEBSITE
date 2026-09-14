<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('level_materi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_materi');
            $table->text('deskripsi')->nullable();
            $table->integer('reward_exp')->default(0);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('level_materi');
    }
};
