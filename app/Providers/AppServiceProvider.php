<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Dokumentasi API hanya disajikan lewat grup superadmin (lihat routes/web.php),
        // jadi rute bawaan paket (/docs/api dan /docs/api.json) tidak dipakai.
        if (class_exists(Scramble::class)) {
            Scramble::ignoreDefaultRoutes();
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $appUrl = (string) config('app.url');

        if ($this->app->environment('production') || str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }

        $this->configurePasswordResetNotification();
        $this->configureApiDocumentation();
    }

    /**
     * Dokumentasikan semua metode HTTP pada rute bertipe `match`.
     *
     * Bawaan Scramble hanya memakai metode pertama (`$route->methods()[0]`), sehingga
     * `POST /api/profil/data` dan `PATCH` pada rute update resource hilang dari dokumen.
     */
    private function configureApiDocumentation(): void
    {
        if (class_exists(Scramble::class)) {
            Scramble::configure()->resolveOperationMethodsUsing(
                fn (Route $route): array => array_values(array_diff($route->methods(), ['HEAD', 'OPTIONS']))
            );
        }
    }

    /**
     * Sesuaikan email reset kata sandi (tautan & teks Bahasa Indonesia).
     */
    private function configurePasswordResetNotification(): void
    {
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            return route('sandi.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        });

        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = route('sandi.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);

            return (new MailMessage)
                ->subject('Atur Ulang Kata Sandi - SINAU APP')
                ->greeting('Halo '.$notifiable->nama_lengkap.'!')
                ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun SINAU APP Anda.')
                ->action('Atur Ulang Kata Sandi', $url)
                ->line('Tautan ini berlaku selama 60 menit.')
                ->line('Jika Anda tidak merasa meminta atur ulang kata sandi, abaikan saja email ini.');
        });
    }
}
