<?php

namespace App\Http\Middleware;

use App\Support\AuthContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Ensure the authenticated user belongs to one of the allowed roles.
     * Usage: ->middleware('role:guru,superadmin')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = AuthContext::currentUser($request);

        if (! $user) {
            return response()->json(['message' => 'Belum terautentikasi.'], 401);
        }

        $role = AuthContext::roleOf($user);

        if (! in_array($role, $roles, true)) {
            return response()->json(['message' => 'Akses ditolak untuk peran ini.'], 403);
        }

        return $next($request);
    }
}
