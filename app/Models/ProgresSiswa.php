<?php

namespace App\Models;

use Database\Factories\ProgresSiswaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgresSiswa extends Model
{
    /** @use HasFactory<ProgresSiswaFactory> */
    use HasFactory;

    protected $table = 'progres_siswa';

    public const STATUS_TERKUNCI = 'terkunci';

    public const STATUS_BERJALAN = 'berjalan';

    public const STATUS_SELESAI = 'selesai';

    protected $fillable = [
        'siswa_id',
        'level_materi_id',
        'status',
        'tanggal_selesai',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_selesai' => 'date',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function levelMateri(): BelongsTo
    {
        return $this->belongsTo(LevelMateri::class, 'level_materi_id');
    }
}
