<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Services\ProgresService;
use App\Support\AuthContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

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
                    ->with('sukses', 'Selamat datang, '.Auth::guard($role)->user()->nama_lengkap.'!');
            }
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau kata sandi salah.']);
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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $siswa = Siswa::create($data);
        $siswa->exp()->create(['total_exp' => 0]);
        $siswa->strek()->create(['current_streak' => 0, 'highest_streak' => 0]);
        $this->progres->initialize($siswa);

        Auth::guard('siswa')->login($siswa);
        $request->session()->regenerate();

        return redirect()
            ->route(AuthContext::homeRouteFor('siswa'))
            ->with('sukses', 'Akun berhasil dibuat. Selamat belajar, '.$siswa->nama_lengkap.'!');
    }

    /**
     * Mulai proses OAuth Google (tombol "Masuk dengan Google").
     */
    public function googleRedirect(): RedirectResponse
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()->route('masuk')->withErrors([
                'email' => 'Login dengan Google belum dikonfigurasi. Hubungi administrator.',
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Tangani callback OAuth Google: masuk bila akun sudah ada, atau
     * otomatis membuat akun siswa baru (pendaftaran mandiri hanya siswa).
     */
    public function googleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            return redirect()->route('masuk')->withErrors([
                'email' => 'Gagal memproses login Google. Silakan coba lagi.',
            ]);
        }

        $email = $googleUser->getEmail();

        if (! $email) {
            return redirect()->route('masuk')->withErrors([
                'email' => 'Akun Google tidak menyediakan alamat email yang dapat digunakan.',
            ]);
        }

        $akun = AuthContext::findByEmail($email);

        if ($akun) {
            Auth::guard($akun['role'])->login($akun['user'], true);
            $request->session()->regenerate();

            return redirect()
                ->intended(route(AuthContext::homeRouteFor($akun['role'])))
                ->with('sukses', 'Selamat datang, '.$akun['user']->nama_lengkap.'!');
        }

        $siswa = $this->buatSiswaDariGoogle($googleUser->getName(), $email);

        Auth::guard('siswa')->login($siswa, true);
        $request->session()->regenerate();

        return redirect()
            ->route(AuthContext::homeRouteFor('siswa'))
            ->with('sukses', 'Akun berhasil dibuat dengan Google. Selamat belajar, '.$siswa->nama_lengkap.'!');
    }

    /**
     * Buat akun siswa baru dari data profil Google.
     */
    private function buatSiswaDariGoogle(?string $nama, string $email): Siswa
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

    public function showLupaSandi(): View
    {
        return view('auth.lupa-sandi');
    }

    /**
     * Kirim tautan atur ulang kata sandi ke email terdaftar.
     */
    public function kirimLupaSandi(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $akun = AuthContext::findByEmail($data['email']);

        if (! $akun) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email tidak terdaftar pada akun mana pun.']);
        }

        try {
            $status = Password::broker($akun['role'])->sendResetLink([
                'email' => $data['email'],
            ]);
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Gagal mengirim email atur ulang. Periksa konfigurasi email lalu coba lagi.']);
        }

        if ($status === Password::RESET_LINK_SENT) {
            return back()
                ->withInput($request->only('email'))
                ->with('status', __($status));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    public function showSandiAnyar(Request $request, string $token): View
    {
        return view('auth.sandi-anyar', [
            'token' => $token,
            'email' => (string) $request->input('email'),
        ]);
    }

    /**
     * Simpan kata sandi baru dari tautan atur ulang.
     */
    public function simpanSandiAnyar(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $akun = AuthContext::findByEmail($data['email']);

        if (! $akun) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email tidak terdaftar pada akun mana pun.']);
        }

        $status = Password::broker($akun['role'])->reset(
            $data,
            function ($user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('masuk')
                ->with('sukses', __('passwords.reset'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
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
