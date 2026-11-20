<?php

namespace App\Models;

use App\Support\Pgvector;
use Database\Factories\KorpusFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Korpus extends Model
{
    /** @use HasFactory<KorpusFactory> */
    use HasFactory;

    protected $table = 'korpus';

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
        if (DB::getDriverName() !== 'pgsql') {
            return false;
        }

        if (! Pgvector::available()) {
            return false;
        }

        return Schema::hasColumn('korpus', 'embedding')
            && self::embeddingColumnType() === 'vector';
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
        $type = Pgvector::type();
        $distance = Pgvector::distanceOperator();

        return $query->select(['id', 'judul', 'kategori', 'konten', 'sumber'])
            ->selectRaw("(1 - (embedding {$distance} ?::{$type})) AS score", [$vectorStr])
            ->whereNotNull('embedding')
            ->orderByRaw("embedding {$distance} ?::{$type} ASC", [$vectorStr])
            ->limit($limit);
    }

    private static function embeddingColumnType(): ?string
    {
        return DB::table('information_schema.columns')
            ->where('table_name', 'korpus')
            ->where('column_name', 'embedding')
            ->value('udt_name');
    }
}
