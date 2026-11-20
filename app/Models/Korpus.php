<?php

namespace App\Models;

use Database\Factories\KorpusFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Korpus extends Model
{
    /** @use HasFactory<KorpusFactory> */
    use HasFactory;

    protected $table = 'korpus';

    private static ?bool $vectorSearchSupport = null;

    protected $fillable = [
        'judul',
        'kategori',
        'konten',
        'sumber',
        'embedding',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'embedding' => 'array',
        ];
    }

    /**
     * Apakah kolom `embedding` bertipe pgvector. Di PostgreSQL tanpa ekstensi
     * pgvector, kolom jatuh ke JSON dan pencarian dihitung di PHP.
     */
    public static function supportsVectorSearch(): bool
    {
        if (self::$vectorSearchSupport !== null) {
            return self::$vectorSearchSupport;
        }

        if (DB::getDriverName() !== 'pgsql') {
            return self::$vectorSearchSupport = false;
        }

        $type = DB::table('information_schema.columns')
            ->where('table_name', 'korpus')
            ->where('column_name', 'embedding')
            ->value('udt_name');

        return self::$vectorSearchSupport = $type === 'vector';
    }

    /**
     * Urutkan korpus berdasarkan kemiripan kosinus (butuh kolom pgvector).
     *
     * @param  Builder<Korpus>  $query
     * @param  array<float>  $vector
     * @return Builder<Korpus>
     */
    public function scopeSimilarTo(Builder $query, array $vector, int $limit = 3): Builder
    {
        $vectorStr = '['.implode(',', $vector).']';

        return $query->select(['id', 'judul', 'kategori', 'konten', 'sumber'])
            ->selectRaw('(1 - (embedding <=> ?::vector)) AS score', [$vectorStr])
            ->whereNotNull('embedding')
            ->orderByRaw('embedding <=> ?::vector ASC', [$vectorStr])
            ->limit($limit);
    }
}
