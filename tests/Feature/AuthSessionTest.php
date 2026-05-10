<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_opens_with_configured_session_driver(): void
    {
        $this->get('/login')->assertOk();
    }
}
