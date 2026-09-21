<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topik extends Model
{
    protected $table = 'topik';

    protected $fillable = [
        'nama',
        'deskripsi',
        'urutan',
    ];

    /**
     * Unit (level_materi) yang tergabung dalam topik ini.
     */
    public function units(): HasMany
    {
        return $this->hasMany(LevelMateri::class, 'topik_id')->orderBy('urutan');
    }

    public function levelMateri(): HasMany
    {
        return $this->units();
    }
}
