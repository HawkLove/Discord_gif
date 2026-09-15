<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageTest extends TestCase
{
    public function test_home_shows_discord_login_and_is_not_the_laravel_welcome(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewIs('home');
        $response->assertSee('Continue with Discord', false);
        $response->assertSee(route('auth.discord', absolute: false), false);
        $response->assertDontSee("Let's get started", false);
        $response->assertDontSee('laravel.com/docs', false);
    }
}
