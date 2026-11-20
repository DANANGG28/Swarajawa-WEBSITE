<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Nama objek pgvector yang sudah dikualifikasi schema.
 *
 * Supabase memasang ekstensi `vector` di schema `extensions`, sedangkan
 * koneksi Laravel memakai `search_path` sendiri. Tanpa kualifikasi, cast
 * `?::vector` dan operator `<=>` gagal walau ekstensinya terpasang.
 */
class Pgvector
{
    private static ?string $schema = null;

    private static bool $resolved = false;

    /**
     * Schema tempat tipe `vector` berada, atau null bila pgvector tidak ada.
     */
    public static function schema(): ?string
    {
        if (self::$resolved) {
            return self::$schema;
        }

        self::$resolved = true;

        if (DB::getDriverName() !== 'pgsql') {
            return null;
        }

        try {
            $schema = DB::table('pg_type')
                ->join('pg_namespace', 'pg_namespace.oid', '=', 'pg_type.typnamespace')
                ->where('pg_type.typname', 'vector')
                ->value('pg_namespace.nspname');
        } catch (Throwable) {
            return null;
        }

        return self::$schema = is_string($schema) ? $schema : null;
    }

    public static function available(): bool
    {
        return self::schema() !== null;
    }

    /**
     * Nama tipe siap dipakai di SQL, mis. `extensions.vector`.
     */
    public static function type(): string
    {
        return self::prefix().'vector';
    }

    /**
     * Operator jarak kosinus siap pakai, mis. `OPERATOR(extensions.<=>)`.
     * Operator yang dikualifikasi schema wajib lewat sintaks OPERATOR().
     */
    public static function distanceOperator(): string
    {
        $schema = self::schema();

        return $schema === null ? '<=>' : "OPERATOR({$schema}.<=>)";
    }

    /**
     * Kelas operator index HNSW, mis. `extensions.vector_cosine_ops`.
     */
    public static function cosineOps(): string
    {
        return self::prefix().'vector_cosine_ops';
    }

    /**
     * Buang cache, dipakai setelah ekstensi baru dibuat.
     */
    public static function forget(): void
    {
        self::$resolved = false;
        self::$schema = null;
    }

    private static function prefix(): string
    {
        $schema = self::schema();

        return $schema === null ? '' : $schema.'.';
    }
}
