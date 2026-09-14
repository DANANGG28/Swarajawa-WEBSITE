<?php

namespace App\Models;

use Database\Factories\SuperadminFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Superadmin extends Authenticatable
{
    /** @use HasFactory<SuperadminFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'superadmin';

    protected $fillable = [
        'nama_lengkap',
        'email',
        'no_telpon',
        'password',
    ];

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
        return $this->hasMany(Soal::class, 'superadmin_id');
    }

    public function test(): HasMany
    {
        return $this->hasMany(Test::class, 'superadmin_id');
    }
}
