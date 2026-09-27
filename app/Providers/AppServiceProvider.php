<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
                ->subject('Atur Ulang Kata Sandi - Sinau Jowo')
                ->greeting('Halo '.$notifiable->nama_lengkap.'!')
                ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun Sinau Jowo Anda.')
                ->action('Atur Ulang Kata Sandi', $url)
                ->line('Tautan ini berlaku selama 60 menit.')
                ->line('Jika Anda tidak merasa meminta atur ulang kata sandi, abaikan saja email ini.');
        });
    }
}
