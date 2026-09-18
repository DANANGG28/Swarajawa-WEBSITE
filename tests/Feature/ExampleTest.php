<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Tamu yang membuka beranda melihat landing page publik.
     */
    public function test_guest_sees_landing_page(): void
    {
        $this->get('/')->assertOk()->assertSee('Mulai Belajar Gratis');
    }
}
