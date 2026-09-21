<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembahasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_materi_id')->constrained('level_materi')->cascadeOnDelete();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->index(['level_materi_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembahasan');
    }
};
