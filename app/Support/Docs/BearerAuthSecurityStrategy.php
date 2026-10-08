<?php

namespace App\Support\Docs;

use Dedoc\Scramble\SecurityDocumentation\MiddlewareAuthSecurityStrategy;
use Dedoc\Scramble\Support\Generator\SecurityScheme;

/**
 * Strategi keamanan dokumentasi API SINAU APP.
 *
 * Pola middleware dan deskripsi skema sengaja ditulis di kelas ini, bukan di
 * config/scramble.php, supaya file config hanya berisi nilai skalar: nilai objek
 * pada file config (atau pada config yang dimutasi saat runtime) membuat
 * `php artisan config:cache` gagal dengan "Your configuration files are not
 * serializable" karena var_export tidak bisa mengekspor objek tanpa __set_state —
 * dan container produksi menjalankan config:cache saat start.
 *
 * Alias `auth.any` harus ditulis eksplisit: pola bawaan strategi (`auth`, `auth:*`)
 * tidak cocok karena Str::is mencocokkan awalan literal `auth:`.
 */
class BearerAuthSecurityStrategy extends MiddlewareAuthSecurityStrategy
{
    public function __construct()
    {
        parent::__construct(
            middleware: ['auth.any'],
            scheme: SecurityScheme::http('bearer')->setDescription(
                'Ambil token lewat POST /api/auth/siswa/login (atau guru / superadmin, '
                .'atau siswa/register untuk akun baru), salin nilai `token` dari respons, '
                .'lalu tempel di kolom ini. Token hanya berlaku untuk endpoint dengan peran '
                .'yang sama; di luar itu dijawab 403. Tanpa token, endpoint bertanda gembok '
                .'dijawab 401 "Belum terautentikasi.".'
            ),
        );
    }
}
