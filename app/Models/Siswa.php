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
        if ($this->foto && file_exists(storage_path('image/siswa/'.$this->foto))) {
            return route('siswa.image', $this->foto);
        }

        return null;
    }

    /**
     * Memeriksa apakah data diri siswa (terutama Kelas dan NIS) belum lengkap.
     */
    public function profilBelumLengkap(): bool
    {
        return ! empty($this->dataBelumLengkap());
    }

    /**
     * Mengembalikan daftar field data diri yang belum lengkap/belum valid.
     *
     * @return array<string, string>
     */
    public function dataBelumLengkap(): array
    {
        $missing = [];

        // 1. Kelas (wajib untuk penugasan dan perankingan kelas)
        if (empty(trim((string) $this->kelas))) {
            $missing['kelas'] = 'Kelas belum dipilih / diisi';
        }

        // 2. NIS (wajib berupa angka valid, bukan kode acak placeholder Google auth)
        $nis = trim((string) $this->nis);
        if (empty($nis)) {
            $missing['nis'] = 'Nomor Induk Siswa (NIS) belum diisi';
        } elseif (str_starts_with($nis, 'G') && ! ctype_digit(substr($nis, 1))) {
            $missing['nis'] = 'NIS masih berupa kode acak sementara dari Google Login';
        }

        return $missing;
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
