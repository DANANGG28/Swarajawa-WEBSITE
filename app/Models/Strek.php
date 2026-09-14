<?php

namespace App\Models;

use Database\Factories\StrekFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Strek extends Model
{
    /** @use HasFactory<StrekFactory> */
    use HasFactory;

    protected $table = 'strek';

    protected $fillable = [
        'siswa_id',
        'current_streak',
        'highest_streak',
        'last_activity_date',
    ];

    protected function casts(): array
    {
        return [
            'current_streak' => 'integer',
            'highest_streak' => 'integer',
            'last_activity_date' => 'date',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
