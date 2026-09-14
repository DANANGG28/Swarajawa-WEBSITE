<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Support\AuthContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Beranda: tamu diarahkan ke halaman login, siswa melihat halaman
     * pembelajaran, guru/superadmin diarahkan ke dashboard perannya.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $role = AuthContext::roleOf(AuthContext::currentUser($request));

        if ($role === 'guru') {
            return redirect()->route('guru.dashboard');
        }

        if ($role === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }

        if ($role === 'siswa') {
            return view('welcome');
        }

        return redirect()->route('masuk');
    }
}
