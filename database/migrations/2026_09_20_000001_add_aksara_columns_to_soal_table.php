<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            // Teks Latin yang diketik guru + hasil konversi Aksara Jawa.
            // Bertipe text karena Aksara Jawa bisa panjang per karakter (multi-byte).
            $table->text('soal_latin')->nullable()->after('pertanyaan');
            $table->text('soal_aksara')->nullable()->after('soal_latin');
        });
    }

    public function down(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->dropColumn(['soal_latin', 'soal_aksara']);
        });
    }
};
