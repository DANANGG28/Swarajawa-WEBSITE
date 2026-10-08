<?php

use App\Support\Docs\BearerAuthSecurityStrategy;

return [
    /*
     * Which routes to document. String or array form; use Scramble::routes() for custom selection.
     *
     * 'api_path' => [
     *     'include' => 'api',
     *     'exclude' => ['api/internal'],
     * ],
     *
     * Without *, patterns match path segments (api matches api and api/users, not apiary).
     * With *, Str::is is used (e.g. api/v*).
     *
     * One static include → default server is /{include} and paths are stripped (/users).
     * Multiple includes or wildcards → server defaults to / and paths stay full (/api/users).
     * Override with `servers`, or use Scramble::registerApi() for separate bases.
     */
    'api_path' => 'api',

    /*
     * Your API domain. By default, app domain is used. This is also a part of the default API routes
     * matcher, so when implementing your own, make sure you use this config if needed.
     */
    'api_domain' => null,

    /*
     * The path where your OpenAPI specification will be exported.
     */
    'export_path' => 'api.json',

    /*
     * Cache configuration for the generated OpenAPI document.
     *
     * Use `scramble:cache` to warm the cache and `scramble:clear` to invalidate it.
     */
    'cache' => [
        'key' => 'scramble.openapi',
        'store' => 'file',
    ],

    'info' => [
        /*
         * API version.
         */
        'version' => env('API_VERSION', '1.0.0'),

        /*
         * Description rendered on the home page of the API documentation.
         */
        'description' => <<<'MD'
API REST SINAU APP (Bahasa Jawa). Semua endpoint berada di bawah prefiks `/api`.

## Autentikasi
Ambil token lewat `POST /api/auth/siswa/login`, `POST /api/auth/guru/login`, atau
`POST /api/auth/superadmin/login` (atau `POST /api/auth/siswa/register` untuk siswa baru).
Responsnya memuat `token`. Kirim di setiap permintaan berikutnya sebagai header:

    Authorization: Bearer <token>

Klik **Authorize** di halaman ini lalu tempel token tersebut. Token tidak kedaluwarsa
sampai dihapus lewat `POST /api/auth/logout`.

## Cara mencoba (tombol Try it out)
1. **Ambil token.** Buka grup **Auth**, pilih endpoint login sesuai peran, isi `email`
   & `password`, kirim, lalu salin nilai `token` dari respons. Untuk `POST /api/kuis/jawab`
   pakai `POST /api/auth/siswa/login` (endpoint itu bertanda peran `siswa`).
2. **Pasang token.** Klik tombol **Authorize** di kanan atas, tempel token tadi, lalu
   Authorize. Setelah itu setiap request dari halaman ini membawa
   `Authorization: Bearer <token>`.
3. **Kirim request.** Pilih endpoint, isi body, klik Send API Request. Nilai yang merujuk
   data lain wajib benar-benar ada: `soal_id` pada `POST /api/kuis/jawab` harus id soal
   nyata (ambil dari `GET /api/materi`), kalau tidak dijawab `422` oleh aturan `exists`.

Arti kode status yang sering muncul di sini: `401` token belum dikirim atau tidak sah,
`403` token sah tapi perannya tidak cocok, `422` isi body tidak lolos validasi.

## Peran
Endpoint bertanda peran hanya bisa diakses token dengan peran itu:

| Peran | Cakupan |
|---|---|
| `siswa` | materi, topik, kuis, progres, badge, leaderboard, chat, profil, speech |
| `guru` | `soal` (CRUD), `level-materi` (baca), `guru/siswa` |
| `superadmin` | `superadmin/dashboard`, CRUD `superadmin/guru`, `superadmin/siswa`, `level-materi` (tulis) |

Endpoint tanpa tanda peran cukup token apa pun yang valid. `GET /api/health` publik.

## Spesifikasi OpenAPI
Dokumen mentah (OpenAPI 3.1) tersedia di `/superadmin/dokumentasi-api.json`
(hanya untuk sesi superadmin). Untuk menyimpan salinan statis, jalankan
`php artisan scramble:export`.
MD,
    ],

    'ui' => [
        'title' => 'SINAU APP — Dokumentasi API',
    ],

    /*
     * Load Scramble's development tools on documentation pages. An explicit
     * SCRAMBLE_DEV_TOOLS value takes precedence over APP_DEBUG.
     */
    'dev_tools' => [
        'enabled' => env('SCRAMBLE_DEV_TOOLS', env('APP_DEBUG', false)),
    ],

    'renderer' => 'elements',

    'renderers' => [
        /*
         * Stoplight Elements config options: https://docs.stoplight.io/docs/elements/b074dc47b2826-elements-configuration-options
         */
        'elements' => [
            'view' => 'scramble::docs',
            'theme' => 'light',
            'hideTryIt' => false,
            'hideSchemas' => false,
            'logo' => '',
            'tryItCredentialsPolicy' => 'include',
            'layout' => 'responsive',
            'router' => 'hash',
        ],
        /*
         * Scalar API reference config options: https://scalar.com/products/api-references/configuration
         */
        'scalar' => [
            'view' => 'scramble::scalar',
            'cdn' => 'https://cdn.jsdelivr.net/npm/@scalar/api-reference',
            'theme' => 'laravel',
            'proxyUrl' => 'https://proxy.scalar.com',
            'darkMode' => false,
            'showDeveloperTools' => 'never',
            'agent' => ['disabled' => true],
            'credentials' => 'include',
        ],
    ],

    /*
     * The list of servers of the API. By default, when `null`, server URL will be created from
     * `scramble.api_path` and `scramble.api_domain` config variables. When providing an array, you
     * will need to specify the local server URL manually (if needed).
     *
     * Example of non-default config (final URLs are generated using Laravel `url` helper):
     *
     * ```php
     * 'servers' => [
     *     'Live' => 'api',
     *     'Prod' => 'https://scramble.dedoc.co/api',
     * ],
     * ```
     */
    'servers' => null,

    /**
     * Determines how Scramble stores the descriptions of enum cases.
     * Available options:
     * - 'description' – Case descriptions are stored as the enum schema's description using table formatting.
     * - 'extension' – Case descriptions are stored in the `x-enumDescriptions` enum schema extension.
     *
     *    @see https://redocly.com/docs-legacy/api-reference-docs/specification-extensions/x-enum-descriptions
     * - false - Case descriptions are ignored.
     */
    'enum_cases_description_strategy' => 'description',

    /**
     * Determines how Scramble stores the names of enum cases.
     * Available options:
     * - 'names' – Case names are stored in the `x-enumNames` enum schema extension.
     * - 'varnames' - Case names are stored in the `x-enum-varnames` enum schema extension.
     * - false - Case names are not stored.
     */
    'enum_cases_names_strategy' => false,

    /**
     * When Scramble encounters deep objects in query parameters, it flattens the parameters so the generated
     * OpenAPI document correctly describes the API. Flattening deep query parameters is relevant until
     * OpenAPI 3.2 is released and query string structure can be described properly.
     *
     * For example, this nested validation rule describes the object with `bar` property:
     * `['foo.bar' => ['required', 'int']]`.
     *
     * When `flatten_deep_query_parameters` is `true`, Scramble will document the parameter like so:
     * `{"name":"foo[bar]", "schema":{"type":"int"}, "required":true}`.
     *
     * When `flatten_deep_query_parameters` is `false`, Scramble will document the parameter like so:
     *  `{"name":"foo", "schema": {"type":"object", "properties":{"bar":{"type": "int"}}, "required": ["bar"]}, "required":true}`.
     */
    'flatten_deep_query_parameters' => true,

    /*
     * Rute dokumentasi didaftarkan di dalam grup superadmin (`routes/web.php`), jadi
     * proteksinya sudah dipegang `web.auth:superadmin`. Middleware bawaan Scramble
     * (`RestrictedDocsAccess`) justru tidak dipakai: ia memeriksa gate `viewApiDocs`
     * terhadap guard default, yang tidak cocok dengan guard sesi aplikasi ini.
     */
    'middleware' => [],

    'extensions' => [],

    /*
     * Automatically document API security (OpenAPI `security` / `securitySchemes`) based on route
     * middleware.
     *
     * Disabled by default. Uncomment the line below to enable `MiddlewareAuthSecurityStrategy`.
     * When at least one documented route uses middleware matching the configured patterns (by default
     * `auth` and `auth:*`), bearer auth is applied globally. Routes without matching middleware are
     * marked as public (`security: []`).
     *
     * Set to `null` explicitly to disable. If you already configure security manually via
     * `afterOpenApiGenerated` / `extendOpenApi`, keep this disabled to avoid duplicate schemes.
     *
     * Customize with a class-string or [class, options]:
     *
     * 'security_strategy' => [
     *     \Dedoc\Scramble\SecurityDocumentation\MiddlewareAuthSecurityStrategy::class,
     *     [
     *         'middleware' => ['auth', 'auth:*'],
     *         'scheme' => \Dedoc\Scramble\Support\Generator\SecurityScheme::http('bearer'),
     *     ],
     * ],
     */
    'security_strategy' => BearerAuthSecurityStrategy::class,
];
