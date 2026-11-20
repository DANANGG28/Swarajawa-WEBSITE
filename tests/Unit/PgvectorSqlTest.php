<?php

namespace Tests\Unit;

use App\Models\Korpus;
use App\Support\Pgvector;
use ReflectionProperty;
use Tests\TestCase;

class PgvectorSqlTest extends TestCase
{
    protected function tearDown(): void
    {
        Pgvector::forget();

        parent::tearDown();
    }

    /**
     * Supabase memasang ekstensi `vector` di schema `extensions`. Operator yang
     * dikualifikasi schema hanya sah lewat sintaks OPERATOR(), bukan
     * `extensions.<=>` seperti tipe.
     */
    public function test_qualified_operator_uses_operator_syntax(): void
    {
        $this->fakeSchema('extensions');

        $sql = Korpus::query()->similarTo([0.1, 0.2], 3)->toSql();

        $this->assertStringContainsString('OPERATOR(extensions.<=>)', $sql);
        $this->assertStringContainsString('?::extensions.vector', $sql);
        $this->assertStringNotContainsString('embedding extensions.<=>', $sql);
    }

    public function test_unqualified_operator_when_schema_is_public(): void
    {
        $this->fakeSchema(null);

        $sql = Korpus::query()->similarTo([0.1, 0.2], 3)->toSql();

        $this->assertStringContainsString('embedding <=>', $sql);
        $this->assertStringContainsString('?::vector', $sql);
        $this->assertStringNotContainsString('OPERATOR(', $sql);
    }

    /**
     * Paksa hasil resolusi schema tanpa menyentuh database.
     */
    private function fakeSchema(?string $schema): void
    {
        $resolved = new ReflectionProperty(Pgvector::class, 'resolved');
        $resolved->setValue(null, true);

        $value = new ReflectionProperty(Pgvector::class, 'schema');
        $value->setValue(null, $schema);
    }
}
