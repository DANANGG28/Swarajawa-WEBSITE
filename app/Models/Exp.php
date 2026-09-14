<?php

namespace App\Models;

use Database\Factories\ExpFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exp extends Model
{
    /** @use HasFactory<ExpFactory> */
    use HasFactory;

    protected $table = 'exp';

    protected $fillable = [
        'siswa_id',
        'total_exp',
    ];

    protected function casts(): array
    {
        return [
            'total_exp' => 'integer',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
