<?php

namespace App\Models;

use Database\Factories\GuruFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Guru extends Authenticatable
{
    /** @use HasFactory<GuruFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'guru';

    protected $fillable = [
        'nip',
        'nama_lengkap',
        'jenis_kelamin',
        'status_pegawaian',
        'no_telpon',
        'email',
        'password',
        'foto',
    ];

    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto && file_exists(resource_path('image/guru/' . $this->foto))) {
            return route('guru.image', $this->foto);
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

    public function soal(): HasMany
    {
        return $this->hasMany(Soal::class, 'guru_id');
    }

    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'guru_siswa', 'guru_id', 'siswa_id')
            ->withPivot(['kelas', 'mata_pelajaran'])
            ->withTimestamps();
    }
}
