<?php

namespace App\Models;

use Database\Factories\SoalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Soal extends Model
{
    /** @use HasFactory<SoalFactory> */
    use HasFactory;

    protected $table = 'soal';

    public const TIPE_PILIHAN_GANDA = 'pilihan_ganda';

    public const TIPE_SUSUN_KALIMAT = 'susun_kalimat';

    public const TIPE_PENCOCOKAN_ARTI = 'pencocokan_arti';

    public const TIPE_PUZZLE_PAKAIAN_ADAT = 'puzzle_pakaian_adat';

    public const TIPE_MENULIS_AKSARA = 'menulis_aksara';

    public const TIPE_KUIS_SUARA = 'kuis_suara';

    protected $fillable = [
        'level_materi_id',
        'tipe_soal',
        'pertanyaan',
        'opsi_jawaban',
        'kunci_jawaban',
        'media_audio_url',
        'bobot_exp',
        'guru_id',
        'superadmin_id',
    ];

    protected function casts(): array
    {
        return [
            'opsi_jawaban' => 'array',
            'kunci_jawaban' => 'array',
            'bobot_exp' => 'integer',
        ];
    }

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

    public function getPembuatAttribute(): ?string
    {
        return $this->guru?->nama_lengkap ?? $this->superadmin?->nama_lengkap;
    }

    public function jawaban(): HasMany
    {
        return $this->hasMany(JawabanSiswa::class, 'soal_id');
    }
}
