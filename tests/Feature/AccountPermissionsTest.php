<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountPermissionsTest extends TestCase
{
    use RefreshDatabase;

    private function data(array $overrides = []): array
    {
        return array_replace([
            'name' => 'Test Editor', 'email' => 'editor@example.com',
            'password' => 'SecurePassword123!', 'password_confirmation' => 'SecurePassword123!',
            'role' => 'editor', 'is_active' => 1, 'permissions' => ['applications'],
        ], $overrides);
    }

    public function test_admin_can_create_account_with_selected_permissions(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin)->post('/admin/users', $this->data())->assertRedirect('/admin/users');
        $editor = User::where('email', 'editor@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('SecurePassword123!', $editor->password));
        $this->actingAs($editor)->get('/admin/applications')->assertOk();
        foreach (['/admin/jobs', '/admin/news', '/admin/noi-dung', '/admin/settings', '/admin/users'] as $path) {
            $this->get($path)->assertForbidden();
        }
        $this->post('/admin/users', $this->data())->assertForbidden();
        $job = \App\Models\Job::create([
            'title' => 'Private draft', 'slug' => 'private-draft', 'location' => 'Office',
            'employment_type' => 'full-time', 'summary' => 'Summary', 'description' => 'Description',
            'created_by' => $admin->id,
        ]);
        $this->delete('/admin/jobs/'.$job->id)->assertForbidden();
        $this->assertDatabaseHas('jobs', ['id' => $job->id]);
        $this->get('/admin')->assertOk()->assertDontSee('href="'.route('admin.jobs.create').'"', false);
    }

    public function test_admin_can_clear_permissions_without_changing_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $editor = User::factory()->create(['role' => 'editor', 'is_active' => true, 'permissions' => ['jobs']]);
        $hash = $editor->password;
        $data = $this->data(['email' => $editor->email, 'password' => '', 'password_confirmation' => '']);
        unset($data['permissions']);
        $this->actingAs($admin)->put('/admin/users/'.$editor->id, $data)->assertSessionHasNoErrors();
        $this->assertSame([], $editor->fresh()->permissions);
        $this->assertSame($hash, $editor->fresh()->password);
        $this->actingAs($editor->fresh())->get('/admin/jobs')->assertForbidden();
    }

    public function test_admin_cannot_lock_or_demote_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin)->put('/admin/users/'.$admin->id, $this->data(['email' => $admin->email]))->assertSessionHasErrors('role');
        $this->put('/admin/users/'.$admin->id, $this->data(['email' => $admin->email, 'role' => 'admin', 'is_active' => 0]))->assertSessionHasErrors('is_active');
        $this->assertSame('admin', $admin->fresh()->role);
        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_unknown_permissions_are_rejected_and_locked_users_cannot_access_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin)->post('/admin/users', $this->data(['permissions' => ['users']]))->assertSessionHasErrors('permissions.0');
        $locked = User::factory()->create(['role' => 'editor', 'is_active' => false, 'permissions' => ['applications']]);
        $this->actingAs($locked)->get('/admin/applications')->assertForbidden();
        $this->get('/admin')->assertForbidden();
    }

    public function test_admin_can_reset_password_and_locked_account_cannot_login(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $editor = User::factory()->create(['role' => 'editor', 'is_active' => true]);
        $this->actingAs($admin)->put('/admin/users/'.$editor->id, $this->data(['email' => $editor->email, 'is_active' => 0]))->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('SecurePassword123!', $editor->fresh()->password));
        $this->post('/admin/logout');
        $this->post('/admin/login', ['email' => $editor->email, 'password' => 'SecurePassword123!'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
