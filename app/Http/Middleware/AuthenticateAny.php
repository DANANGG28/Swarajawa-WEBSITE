<?php

namespace App\Http\Middleware;

use App\Support\AuthContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autentikasi lintas-kanal: menerima token Sanctum (mobile/API) maupun
 * sesi web (guard siswa/guru/superadmin) dalam satu middleware.
 */
class AuthenticateAny
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = AuthContext::currentUser($request);

        if (! $user) {
            return response()->json(['message' => 'Belum terautentikasi.'], 401);
        }

        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
