<?php

namespace App\Http\Middleware;

use App\Support\AuthContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autentikasi sesi untuk halaman web (redirect ke /masuk bila belum login).
 * Opsional membatasi peran: ->middleware('web.auth:siswa')
 */
class EnsureWebAuth
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = AuthContext::currentUser($request);

        if (! $user) {
            return redirect()->guest(route('masuk'));
        }

        if ($roles !== [] && ! in_array(AuthContext::roleOf($user), $roles, true)) {
            abort(403, 'Akses ditolak untuk peran ini.');
        }

        // Selaraskan user pada guard default agar Gate/Policy & $request->user() konsisten.
        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
