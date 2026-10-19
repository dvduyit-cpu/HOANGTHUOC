<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_ends_a_remembered_session_and_blocks_admin_access(): void
    {
        $user = User::factory()->create(['role' => 'admin', 'is_active' => true, 'password' => 'Test-password-123']);
        $this->post('/admin/login', ['email' => $user->email, 'password' => 'Test-password-123', 'remember' => '1'])
            ->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
        $page = $this->get('/admin')->assertOk();
        $this->assertStringContainsString('no-store', $page->headers->get('Cache-Control'));
        $token = $user->fresh()->remember_token;
        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
        $this->assertNotSame($token, $user->fresh()->remember_token);
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
    }
}
