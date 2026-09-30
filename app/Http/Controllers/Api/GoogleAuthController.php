<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GoogleSiswaService;
use App\Support\AuthContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function __construct(private readonly GoogleSiswaService $googleSiswa) {}

    /**
     * Login aplikasi mobile memakai Google ID token (native Google Sign-In).
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'id_token' => ['required', 'string'],
        ]);

        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($request->input('id_token'));
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'Token Google tidak valid.'], 401);
        }

        $email = mb_strtolower(trim((string) $googleUser->getEmail()));

        if ($email === '') {
            return response()->json(['message' => 'Akun Google tidak memiliki email.'], 422);
        }

        $akun = AuthContext::findByEmail($email);

        if ($akun) {
            $user = $akun['user'];
            $role = $akun['role'];
        } else {
            $user = DB::transaction(
                fn () => $this->googleSiswa->createFromGoogle($googleUser->getName(), $email)
            );
            $role = 'siswa';
        }

        $token = $user->createToken("{$role}-token")->plainTextToken;

        return response()->json([
            'message' => 'Login dengan Google berhasil.',
            'role' => $role,
            'user' => $user,
            'token' => $token,
        ]);
    }
}
