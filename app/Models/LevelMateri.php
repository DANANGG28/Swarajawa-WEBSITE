<?php

namespace App\Models;

use Database\Factories\LevelMateriFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LevelMateri extends Model
{
    /** @use HasFactory<LevelMateriFactory> */
    use HasFactory;

    protected $table = 'level_materi';

    protected $fillable = [
        'nama_materi',
        'deskripsi',
        'reward_exp',
        'urutan',
    ];

    public function soal(): HasMany
    {
        return $this->hasMany(Soal::class, 'level_materi_id');
    }

    public function test(): HasMany
    {
        return $this->hasMany(Test::class, 'level_materi_id');
    }

    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'progres_siswa', 'level_materi_id', 'siswa_id')
            ->withPivot(['status', 'tanggal_selesai'])
            ->withTimestamps();
    }
}
