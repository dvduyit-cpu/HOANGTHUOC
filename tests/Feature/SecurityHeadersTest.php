<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_translate_stylesheet_is_allowed_without_relaxing_other_sources(): void
    {
        $response = $this->get('/')->assertOk();
        $policy = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://www.gstatic.com/_/translate_http/;", $policy);
        $this->assertStringContainsString("default-src 'self';", $policy);
        $this->assertStringContainsString("object-src 'none';", $policy);
        $this->assertStringNotContainsString('https://*.gstatic.com', $policy);
    }
}
