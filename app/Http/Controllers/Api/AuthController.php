<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Services\GamificationService;
use App\Services\ProgresService;
use App\Support\AuthContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        private readonly ProgresService $progres,
        private readonly GamificationService $gamification,
    ) {}

    /**
     * Registrasi mandiri KHUSUS siswa (FR-1).
     */
    public function registerSiswa(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:30', 'unique:siswa,nis'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'no_telpon' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:siswa,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $siswa = Siswa::create($data);

        $siswa->exp()->create(['total_exp' => 0]);
        $siswa->strek()->create(['current_streak' => 0, 'highest_streak' => 0]);
        $this->progres->initialize($siswa);

        $token = $siswa->createToken('siswa-token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi siswa berhasil.',
            'role' => 'siswa',
            'user' => $siswa,
            'token' => $token,
        ], 201);
    }

    /**
     * Login terpadu tanpa memilih peran — peran dideteksi otomatis dari email.
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $found = AuthContext::findByEmail($data['email']);

        if (! $found || ! Hash::check($data['password'], $found['user']->password)) {
            return response()->json(['message' => 'Email atau kata sandi salah.'], 422);
        }

        $token = $found['user']->createToken("{$found['role']}-token")->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'role' => $found['role'],
            'user' => $found['user'],
            'token' => $token,
        ]);
    }

    public function loginSiswa(Request $request): JsonResponse
    {
        return $this->attemptRole($request, 'siswa');
    }

    public function loginGuru(Request $request): JsonResponse
    {
        return $this->attemptRole($request, 'guru');
    }

    public function loginSuperadmin(Request $request): JsonResponse
    {
        return $this->attemptRole($request, 'superadmin');
    }

    public function logout(Request $request): JsonResponse
    {
        if ($token = $request->user()?->currentAccessToken()) {
            $token->delete();
        }

        foreach (AuthContext::ROLES as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
            }
        }

        return response()->json(['message' => 'Berhasil keluar.']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $role = AuthContext::roleOf($user);

        $payload = ['role' => $role, 'user' => $user];

        if ($user instanceof Siswa) {
            $this->gamification->syncStreak($user);
            $payload['exp'] = $user->exp()->first();
            $payload['strek'] = $user->strek()->first();
        }

        return response()->json($payload);
    }

    private function attemptRole(Request $request, string $role): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $model = AuthContext::modelFor($role);
        $user = $model::query()->where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Email atau kata sandi salah.'], 422);
        }

        $token = $user->createToken("{$role}-token")->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'role' => $role,
            'user' => $user,
            'token' => $token,
        ]);
    }
}
