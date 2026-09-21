<?php

namespace App\Models;

use Database\Factories\SiswaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Siswa extends Authenticatable
{
    /** @use HasFactory<SiswaFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'siswa';

    protected $fillable = [
        'nis',
        'nama_lengkap',
        'jenis_kelamin',
        'kelas',
        'no_telpon',
        'email',
        'password',
        'foto',
    ];

    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto && file_exists(storage_path('image/siswa/' . $this->foto))) {
            return route('siswa.image', $this->foto);
        }

        return null;
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function exp(): HasOne
    {
        return $this->hasOne(Exp::class, 'siswa_id');
    }

    public function strek(): HasOne
    {
        return $this->hasOne(Strek::class, 'siswa_id');
    }

    public function progres(): HasMany
    {
        return $this->hasMany(ProgresSiswa::class, 'siswa_id');
    }

    public function levelMateri(): BelongsToMany
    {
        return $this->belongsToMany(LevelMateri::class, 'progres_siswa', 'siswa_id', 'level_materi_id')
            ->withPivot(['status', 'tanggal_selesai'])
            ->withTimestamps();
    }

    public function guru(): BelongsToMany
    {
        return $this->belongsToMany(Guru::class, 'guru_siswa', 'siswa_id', 'guru_id')
            ->withPivot(['kelas', 'mata_pelajaran'])
            ->withTimestamps();
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class, 'siswa_id')->orderBy('updated_at', 'desc');
    }

    public function jawaban(): HasMany
    {
        return $this->hasMany(JawabanSiswa::class, 'siswa_id');
    }
}
