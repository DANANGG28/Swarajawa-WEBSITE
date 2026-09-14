<?php

namespace App\Support;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Superadmin;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthContext
{
    public const ROLES = ['siswa', 'guru', 'superadmin'];

    /**
     * Resolve the currently authenticated user across the API (Sanctum) and
     * the three session guards used by the web UI.
     */
    public static function currentUser(Request $request): ?Authenticatable
    {
        if ($user = $request->user()) {
            return $user;
        }

        if ($user = Auth::guard('sanctum')->user()) {
            return $user;
        }

        foreach (self::ROLES as $guard) {
            if ($user = Auth::guard($guard)->user()) {
                return $user;
            }
        }

        return null;
    }

    public static function roleOf(?Authenticatable $user): ?string
    {
        return match (true) {
            $user instanceof Siswa => 'siswa',
            $user instanceof Guru => 'guru',
            $user instanceof Superadmin => 'superadmin',
            default => null,
        };
    }

    public static function guardFor(string $role): string
    {
        return in_array($role, self::ROLES, true) ? $role : 'web';
    }

    public static function modelFor(string $role): ?string
    {
        return match ($role) {
            'siswa' => Siswa::class,
            'guru' => Guru::class,
            'superadmin' => Superadmin::class,
            default => null,
        };
    }

    /**
     * Cari akun berdasarkan email di seluruh tabel peran, sekaligus
     * mendeteksi perannya secara otomatis (login tanpa pilih peran).
     *
     * @return array{user: Authenticatable, role: string}|null
     */
    public static function findByEmail(string $email): ?array
    {
        foreach (self::ROLES as $role) {
            $model = self::modelFor($role);

            if ($model && $user = $model::query()->where('email', $email)->first()) {
                return ['user' => $user, 'role' => $role];
            }
        }

        return null;
    }

    /**
     * Nama route beranda sesuai peran (dipakai untuk redirect setelah login).
     */
    public static function homeRouteFor(?string $role): string
    {
        return match ($role) {
            'guru' => 'guru.dashboard',
            'superadmin' => 'superadmin.dashboard',
            default => 'siswa.dashboard',
        };
    }
}
