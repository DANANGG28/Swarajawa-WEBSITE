<?php

use App\Support\Pgvector;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * CREATE EXTENSION di PostgreSQL menggagalkan transaksi bila ekstensi tidak
     * tersedia; tanpa transaksi, kegagalan itu bisa ditangkap dan diturunkan ke
     * kolom JSON.
     */
    public $withinTransaction = false;

    public function up(): void
    {
        if ($this->hasColumn()) {
            return;
        }

        if (! $this->enablePgvector()) {
            Schema::table('korpus', function (Blueprint $table): void {
                $table->json('embedding')->nullable();
            });

            return;
        }

        $dimension = (int) config('ai.embedding_dimension', 768);
        $type = Pgvector::type();

        DB::statement("ALTER TABLE korpus ADD COLUMN IF NOT EXISTS embedding {$type}({$dimension})");
        DB::statement('CREATE INDEX IF NOT EXISTS korpus_embedding_hnsw_idx ON korpus USING hnsw (embedding '.Pgvector::cosineOps().')');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS korpus_embedding_hnsw_idx');
        }

        if ($this->hasColumn()) {
            Schema::table('korpus', function (Blueprint $table): void {
                $table->dropColumn('embedding');
            });
        }
    }

    private function hasColumn(): bool
    {
        return Schema::hasColumn('korpus', 'embedding');
    }

    /**
     * Postgres tanpa ekstensi pgvector (image `postgres:*-alpine`, service CI)
     * tetap harus bisa migrasi. Di Supabase, ekstensi dipasang ke schema
     * `extensions`; di Postgres biasa hasilnya jatuh ke `public`.
     */
    private function enablePgvector(): bool
    {
        if (DB::getDriverName() !== 'pgsql') {
            return false;
        }

        try {
            DB::statement('CREATE EXTENSION IF NOT EXISTS vector WITH SCHEMA extensions');
        } catch (Throwable) {
            try {
                DB::statement('CREATE EXTENSION IF NOT EXISTS vector');
            } catch (Throwable) {
                return false;
            }
        }

        Pgvector::forget();

        return Pgvector::available();
    }
};
