<?php

namespace App\Models;

use Database\Factories\TestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Test extends Model
{
    /** @use HasFactory<TestFactory> */
    use HasFactory;

    protected $table = 'test';

    protected $fillable = [
        'level_materi_id',
        'nama_test',
        'deskripsi',
        'guru_id',
        'superadmin_id',
    ];

    public function levelMateri(): BelongsTo
    {
        return $this->belongsTo(LevelMateri::class, 'level_materi_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function superadmin(): BelongsTo
    {
        return $this->belongsTo(Superadmin::class, 'superadmin_id');
    }

    public function soal(): BelongsToMany
    {
        return $this->belongsToMany(Soal::class, 'test_soal', 'test_id', 'soal_id')
            ->withPivot('urutan')
            ->withTimestamps()
            ->orderBy('test_soal.urutan');
    }
}
