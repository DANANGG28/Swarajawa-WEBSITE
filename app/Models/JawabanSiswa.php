<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanSiswa extends Model
{
    use HasFactory;

    protected $table = 'jawaban_siswa';

    protected $fillable = [
        'siswa_id',
        'soal_id',
        'skor_tertinggi',
        'exp_diberikan',
        'jumlah_percobaan',
    ];

    protected function casts(): array
    {
        return [
            'skor_tertinggi' => 'integer',
            'exp_diberikan' => 'integer',
            'jumlah_percobaan' => 'integer',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class, 'soal_id');
    }
}
