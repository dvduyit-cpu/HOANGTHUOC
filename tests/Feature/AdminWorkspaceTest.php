<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    private function configuration(array $overrides = []): array
    {
        return array_replace([
            'admin_name' => 'Quản trị Hoàng Thước',
            'site_title' => 'Tuyển dụng Hoàng Thước',
            'site_description' => 'Cơ hội nghề nghiệp tại Hoàng Thước.',
            'admin_footer' => 'Hệ thống tuyển dụng',
            'admin_color' => '#0f766e',
            'admin_font' => 'arial',
            'admin_font_size' => 18,
        ], $overrides);
    }

    public function test_guests_are_redirected_to_admin_login(): void
    {
        $this->get('/admin/settings')->assertRedirect('/admin/login');
    }

    public function test_header_and_menu_colors_are_saved_with_readable_text(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $this->put('/admin/settings', $this->configuration([
            'admin_header_color' => '#000000', 'admin_sidebar_color' => '#ffffff',
        ]))->assertSessionHasNoErrors();
        $this->get('/admin')->assertOk()
            ->assertSee('--admin-header-bg: #000000', false)
            ->assertSee('--admin-header-text: #ffffff', false)
            ->assertSee('--admin-sidebar-bg: #ffffff', false)
            ->assertSee('--admin-sidebar-text: #18243a', false)
            ->assertSee('admin-navigation.css');
        $this->put('/admin/settings', $this->configuration([
            'admin_header_color' => 'invalid', 'admin_sidebar_color' => 'red; color: blue',
        ]))->assertSessionHasErrors(['admin_header_color', 'admin_sidebar_color']);
        $this->assertDatabaseHas('site_settings', ['key' => 'admin_header_color', 'value' => '#000000']);
    }

    public function test_admin_pages_render_with_the_new_navigation(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        foreach (['/admin', '/admin/settings', '/admin/jobs', '/admin/jobs/create', '/admin/news', '/admin/news/create', '/admin/users', '/admin/users/create', '/admin/applications', '/admin/noi-dung', '/admin/noi-dung/trang-chu'] as $path) {
            $this->actingAs($admin)->get($path)->assertOk()->assertSee('Cấu hình trang')->assertSee('admin-workspace.css');
        }
    }

    public function test_editor_can_manage_content_but_cannot_change_site_configuration(): void
    {
        $editor = User::factory()->create(['role' => 'editor', 'is_active' => true]);
        $this->actingAs($editor)->get('/admin')->assertOk()->assertDontSee('Cấu hình trang');
        $this->get('/admin/noi-dung')->assertOk();
        $this->get('/admin/settings')->assertForbidden();
        $this->put('/admin/settings', $this->configuration())->assertForbidden();
        $this->assertDatabaseCount('site_settings', 0);
    }

    public function test_admin_configuration_is_saved_and_used_on_admin_and_public_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin)->from('/admin/settings')
            ->put('/admin/settings', $this->configuration(['unapproved_key' => 'ignored']))
            ->assertRedirect('/admin/settings')->assertSessionHas('success');

        $this->assertDatabaseHas('site_settings', ['key' => 'admin_color', 'value' => '#0f766e']);
        $this->assertDatabaseMissing('site_settings', ['key' => 'unapproved_key']);
        $this->get('/admin')->assertOk()->assertSee('Quản trị Hoàng Thước')->assertSee('--primary: #0f766e', false)->assertSee('Hệ thống tuyển dụng');
        $this->get('/')->assertOk()->assertSee('Tuyển dụng Hoàng Thước')->assertSee('Cơ hội nghề nghiệp tại Hoàng Thước.');
        $this->post('/admin/logout');
        $this->get('/admin/login')->assertOk()->assertSee('Quản trị Hoàng Thước')->assertSee('Hệ thống tuyển dụng');
    }

    public function test_invalid_configuration_is_rejected_without_changing_saved_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        SiteSetting::putMany(['admin_name' => 'Tên hiện tại']);
        $this->actingAs($admin)->put('/admin/settings', $this->configuration([
            'admin_name' => '',
            'admin_color' => 'red; color: black',
            'admin_font' => '<script>',
            'admin_font_size' => 100,
        ]))->assertSessionHasErrors(['admin_name', 'admin_color', 'admin_font', 'admin_font_size']);
        $this->assertDatabaseHas('site_settings', ['key' => 'admin_name', 'value' => 'Tên hiện tại']);
        $this->assertDatabaseCount('site_settings', 1);
    }

    public function test_logo_upload_is_used_on_public_pages_and_can_be_reset(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $logo = UploadedFile::fake()->image('logo.png');
        $this->actingAs($admin)->put('/admin/settings', $this->configuration(['logo' => $logo]))->assertSessionHasNoErrors();

        $path = SiteSetting::values()['site_logo'];
        Storage::disk('public')->assertExists($path);
        $this->get('/')->assertOk()->assertSee(asset('storage/'.$path));

        $this->put('/admin/settings', $this->configuration())->assertSessionHasNoErrors();
        $this->assertDatabaseHas('site_settings', ['key' => 'site_logo', 'value' => $path]);

        $this->put('/admin/settings', $this->configuration(['remove_logo' => 1]))->assertSessionHasNoErrors();
        $this->get('/')->assertOk()->assertSee(asset('images/hoang-thuoc-logo.png'))->assertDontSee(asset('storage/'.$path));
    }

    public function test_non_image_and_oversized_logos_are_rejected(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        foreach ([UploadedFile::fake()->create('logo.svg', 10, 'image/svg+xml'), UploadedFile::fake()->image('large.png')->size(2049)] as $logo) {
            $this->actingAs($admin)->put('/admin/settings', $this->configuration(['logo' => $logo]))->assertSessionHasErrors('logo');
        }
        $this->assertDatabaseCount('site_settings', 0);
    }
}
