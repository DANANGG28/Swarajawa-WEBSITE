<?php

namespace App\Services;

use App\Models\Siswa;
use Illuminate\Support\Str;

/**
 * Pembuatan akun siswa dari data profil Google — dipakai bersama oleh
 * alur web (redirect OAuth) dan API mobile (verifikasi id_token).
 */
class GoogleSiswaService
{
    public function __construct(private readonly ProgresService $progres) {}

    /**
     * Buat akun siswa baru dari data profil Google.
     */
    public function createFromGoogle(?string $nama, string $email): Siswa
    {
        $siswa = Siswa::create([
            'nis' => $this->nisUnik(),
            'nama_lengkap' => $nama ?: Str::before($email, '@'),
            'jenis_kelamin' => 'L',
            'kelas' => null,
            'no_telpon' => null,
            'email' => $email,
            'password' => Str::random(32),
        ]);

        $siswa->exp()->create(['total_exp' => 0]);
        $siswa->strek()->create(['current_streak' => 0, 'highest_streak' => 0]);
        $this->progres->initialize($siswa);

        return $siswa;
    }

    private function nisUnik(): string
    {
        do {
            $nis = 'G'.now()->format('ymd').Str::upper(Str::random(4));
        } while (Siswa::query()->where('nis', $nis)->exists());

        return $nis;
    }
}
