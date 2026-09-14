<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Services\ProgresService;
use App\Support\AuthContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthWebController extends Controller
{
    public function __construct(private readonly ProgresService $progres) {}

    public function showMasuk(): View
    {
        return view('auth.masuk');
    }

    public function masuk(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        foreach (AuthContext::ROLES as $role) {
            if (Auth::guard($role)->attempt(
                ['email' => $data['email'], 'password' => $data['password']],
                $request->boolean('remember'),
            )) {
                $request->session()->regenerate();

                return redirect()
                    ->intended(route(AuthContext::homeRouteFor($role)))
                    ->with('sukses', 'Sugeng rawuh, '.Auth::guard($role)->user()->nama_lengkap.'!');
            }
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email utawa tembung sandi lepat.']);
    }

    public function showDaftar(): View
    {
        return view('auth.daftar');
    }

    public function daftar(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:30', 'unique:siswa,nis'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'no_telpon' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:siswa,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $siswa = Siswa::create($data);
        $siswa->exp()->create(['total_exp' => 0]);
        $siswa->strek()->create(['current_streak' => 0, 'highest_streak' => 0]);
        $this->progres->initialize($siswa);

        Auth::guard('siswa')->login($siswa);
        $request->session()->regenerate();

        return redirect()
            ->route(AuthContext::homeRouteFor('siswa'))
            ->with('sukses', 'Akun kasil digawe. Sugeng sinau, '.$siswa->nama_lengkap.'!');
    }

    public function keluar(Request $request): RedirectResponse
    {
        foreach (AuthContext::ROLES as $guard) {
            Auth::guard($guard)->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('masuk');
    }
}
