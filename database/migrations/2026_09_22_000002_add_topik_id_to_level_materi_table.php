<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('level_materi', function (Blueprint $table) {
            $table->foreignId('topik_id')
                ->nullable()
                ->after('id')
                ->constrained('topik')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('level_materi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('topik_id');
        });
    }
};
