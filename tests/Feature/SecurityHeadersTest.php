<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_security_headers_are_set_on_responses(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('X-Frame-Options', 'DENY');
    }

    public function test_csrf_token_is_present_in_response(): void
    {
        $response = $this->get('/');

        // The Blade template renders the CSRF token so AJAX requests can
        // include it as the X-CSRF-TOKEN header.
        $response->assertSee('X-CSRF-TOKEN');
    }
}