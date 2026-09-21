<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->foreignId('pembahasan_id')
                ->nullable()
                ->after('level_materi_id')
                ->constrained('pembahasan')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pembahasan_id');
        });
    }
};
