<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Superadmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ApiDocsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_tidak_bisa_akses_dokumentasi_api(): void
    {
        $this->get('/superadmin/dokumentasi-api')->assertRedirect(route('masuk'));
        $this->getJson('/superadmin/dokumentasi-api.json')->assertRedirect(route('masuk'));
    }

    public function test_guru_tidak_bisa_akses_dokumentasi_api(): void
    {
        $this->actingAs(Guru::factory()->create(), 'guru')
            ->get('/superadmin/dokumentasi-api')
            ->assertForbidden();
    }

    public function test_superadmin_bisa_membuka_halaman_dokumentasi_api(): void
    {
        $this->actingAs(Superadmin::factory()->create(), 'superadmin')
            ->get('/superadmin/dokumentasi-api')
            ->assertOk()
            ->assertSee('elements-api', false);
    }

    public function test_dokumen_openapi_valid_dan_mendokumentasikan_seluruh_endpoint_api(): void
    {
        $spec = $this->spec();

        $this->assertStringStartsWith('3.1', (string) ($spec['openapi'] ?? ''));

        $dokumentasi = [];
        $operationIds = [];
        foreach ($spec['paths'] as $path => $pathItem) {
            foreach (self::OPERATION_KEYS as $method) {
                if (isset($pathItem[$method])) {
                    $dokumentasi[] = strtoupper($method).' '.$path;
                    $operationIds[] = $pathItem[$method]['operationId'] ?? null;
                }
            }
        }

        $this->assertEqualsCanonicalizing($this->endpoint()->all(), $dokumentasi);

        $this->assertNotContains(null, $operationIds, 'Ada operasi tanpa operationId.');
        $this->assertSame(
            count($operationIds),
            count(array_unique($operationIds)),
            'Ada operationId duplikat pada dokumen OpenAPI.',
        );
    }

    public function test_endpoint_login_mendokumentasikan_email_dan_password(): void
    {
        $spec = $this->spec();

        foreach (['/auth/siswa/login', '/auth/guru/login', '/auth/superadmin/login'] as $path) {
            $schema = $spec['paths'][$path]['post']['requestBody']['content']['application/json']['schema'] ?? [];

            $this->assertSame(['email', 'password'], array_keys($schema['properties'] ?? []), "Badan permintaan {$path} tidak sesuai.");
            $this->assertSame(['email', 'password'], $schema['required'] ?? [], "Field wajib {$path} tidak sesuai.");
            $this->assertSame('email', $schema['properties']['email']['format'] ?? null, "Format email {$path} tidak terdokumentasi.");
        }
    }

    public function test_dokumen_memuat_panduan_ambil_token(): void
    {
        $spec = $this->spec();

        $this->assertStringContainsString('Ambil token', $spec['info']['description'] ?? '', 'Deskripsi dokumen tidak memuat langkah ambil token.');
        $this->assertStringContainsString('Authorize', $spec['info']['description'] ?? '');
        $this->assertStringContainsString('soal_id', $spec['info']['description'] ?? '', 'Langkah Try it out tidak menyebut contoh id yang harus nyata.');

        $grupAuth = collect($spec['tags'] ?? [])->firstWhere('name', 'Auth');
        $this->assertNotNull($grupAuth, 'Grup Auth tidak ada di dokumen.');
        $this->assertStringContainsString('token', $grupAuth['description'] ?? '');

        foreach (['/auth/siswa/login', '/auth/guru/login', '/auth/superadmin/login', '/auth/login'] as $path) {
            $this->assertStringContainsString('Authorize', $spec['paths'][$path]['post']['description'] ?? '', "Deskripsi {$path} tidak mengarahkan ke Authorize.");
        }

        $this->assertStringContainsString('api/auth/siswa/login', $spec['components']['securitySchemes']['http']['description'] ?? '', 'Deskripsi skema bearer tidak menyebut cara ambil token.');
    }

    public function test_url_server_mengarah_ke_prefiks_api(): void
    {
        $spec = $this->spec();

        $this->assertNotEmpty($spec['servers'] ?? []);
        $this->assertStringEndsWith('/api', $spec['servers'][0]['url']);
    }

    public function test_endpoint_terlindungi_butuh_bearer_token_dan_endpoint_publik_tidak(): void
    {
        $spec = $this->spec();

        $security = $spec['security'] ?? [];
        $this->assertNotEmpty($security, 'Dokumen OpenAPI tidak memuat permintaan token.');
        $scheme = $spec['components']['securitySchemes'][array_key_first($security[0])] ?? [];
        $this->assertSame('http', $scheme['type'] ?? null);
        $this->assertSame('bearer', $scheme['scheme'] ?? null);

        $this->assertSame($security, $this->securityOf($spec, '/materi', 'get'), 'Endpoint terlindungi tidak ditandai butuh token.');
        $this->assertSame([], $this->securityOf($spec, '/health', 'get'), 'Endpoint publik seharusnya ditandai tanpa token.');
    }

    /**
     * Daftar seluruh endpoint yang benar-benar terdaftar di bawah prefiks `api/`.
     *
     * @return Collection<int, string>
     */
    private function endpoint()
    {
        $endpoint = collect();

        foreach (Route::getRoutes() as $route) {
            if (! str_starts_with($route->uri(), 'api/')) {
                continue;
            }

            foreach (array_diff($route->methods(), ['HEAD', 'OPTIONS']) as $method) {
                $endpoint->push($method.' /'.substr($route->uri(), 4));
            }
        }

        return $endpoint;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function spec(): array
    {
        return $this->actingAs(Superadmin::factory()->create(), 'superadmin')
            ->getJson('/superadmin/dokumentasi-api.json')
            ->assertOk()
            ->json();
    }

    /**
     * Keamanan efektif sebuah operasi: nilai pada operasi itu sendiri, atau
     * nilai default tingkat dokumen bila operasi tidak menimpanya.
     *
     * @param  array<string, mixed>  $spec
     */
    private function securityOf(array $spec, string $path, string $method): array
    {
        return $spec['paths'][$path][$method]['security'] ?? $spec['security'] ?? [];
    }

    private const OPERATION_KEYS = ['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'];
}
