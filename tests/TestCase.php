<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Variabel `DB_*`/`APP_ENV` di shell menimpa `phpunit.xml`, jadi suite bisa
     * menulis ke database produksi tanpa peringatan. Dijaga di sini karena
     * `RefreshDatabase` sudah menyentuh koneksi di `setUp()`.
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $this->guardAgainstNonTestDatabase($app);

        return $app;
    }

    /**
     * Gagal cepat jauh lebih murah daripada membersihkan data yang sudah
     * terhapus di database produksi.
     */
    private function guardAgainstNonTestDatabase($app): void
    {
        $environment = (string) $app['config']->get('app.env');

        if ($environment !== 'testing') {
            throw new RuntimeException(
                "Test berjalan dengan APP_ENV='{$environment}', bukan 'testing'. ".
                'Periksa variabel environment yang ter-export di shell.'
            );
        }

        if ($app['config']->get('database.default') !== 'pgsql') {
            return;
        }

        $database = (string) $app['config']->get('database.connections.pgsql.database');
        $host = (string) $app['config']->get('database.connections.pgsql.host');

        if (! str_contains($database, 'test')) {
            throw new RuntimeException(
                "Test menolak database PostgreSQL '{$database}' di host '{$host}': ".
                'nama database harus mengandung "test".'
            );
        }
    }
}
