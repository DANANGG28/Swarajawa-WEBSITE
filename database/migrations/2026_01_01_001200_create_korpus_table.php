<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('korpus', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('kategori')->default('umum');
            $table->text('konten');
            $table->string('sumber')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('korpus');
    }
};
