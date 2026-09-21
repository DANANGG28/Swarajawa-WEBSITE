<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pembahasan extends Model
{
    protected $table = 'pembahasan';

    protected $fillable = [
        'level_materi_id',
        'nama',
        'deskripsi',
        'urutan',
    ];

    public function levelMateri(): BelongsTo
    {
        return $this->belongsTo(LevelMateri::class, 'level_materi_id');
    }

    public function soal(): HasMany
    {
        return $this->hasMany(Soal::class, 'pembahasan_id');
    }
}
