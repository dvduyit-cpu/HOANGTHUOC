<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use App\Support\VisualEditor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VisualEditorTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
    }

    public function test_draft_and_uploaded_image_are_private_until_published(): void
    {
        Storage::fake('public');
        $this->admin();
        SiteSetting::putMany(['home_title_vi' => 'Published title']);
        $this->put('/admin/noi-dung/trang-chu', [
            'intent' => 'draft', 'settings' => ['home_title_vi' => 'Draft title'],
            'uploads' => ['home_hero_image' => UploadedFile::fake()->image('draft.jpg')],
        ])->assertSessionHasNoErrors();
        $draft = json_decode(SiteSetting::values()['content_draft_trang-chu'], true);
        $path = $draft['settings']['home_hero_image'];
        Storage::disk('public')->assertExists($path);
        $this->get('/')->assertSee('Published title')->assertDontSee('Draft title')->assertDontSee($path);
        $this->get('/admin/noi-dung/trang-chu')->assertOk()->assertSee('Draft title')->assertSee('visual-editor.js');
        $this->delete('/admin/kho-anh', ['path' => $path])->assertSessionHasErrors('path');
        $this->put('/admin/noi-dung/trang-chu', ['settings' => $draft['settings']])->assertSessionHasNoErrors();
        $this->get('/')->assertSee('Draft title')->assertSee($path);
        $this->assertArrayNotHasKey('content_draft_trang-chu', SiteSetting::values());
    }

    public function test_versions_restore_to_draft_and_do_not_publish_without_review(): void
    {
        $this->admin();
        SiteSetting::putMany(['home_title_vi' => 'Original title']);
        $this->put('/admin/noi-dung/trang-chu', ['settings' => ['home_title_vi' => 'New title']])
            ->assertSessionHasNoErrors();
        $history = json_decode(SiteSetting::values()['content_history_trang-chu'], true);
        $this->assertSame('Original title', $history[0]['settings']['home_title_vi']);
        $this->post('/admin/noi-dung/trang-chu/restore', ['version' => $history[0]['id']])->assertSessionHasNoErrors();
        $this->get('/')->assertSee('New title')->assertDontSee('Original title');
        $this->get('/admin/noi-dung/trang-chu')->assertSee('Original title');
        $this->delete('/admin/noi-dung/trang-chu/draft')->assertSessionHas('success');
        $this->assertArrayNotHasKey('content_draft_trang-chu', SiteSetting::values());
        $this->get('/')->assertSee('New title');
    }

    public function test_block_order_visibility_and_typography_can_be_previewed_then_published(): void
    {
        $this->admin();
        $layout = array_reverse(VisualEditor::layout('trang-chu', []));
        $layout[0]['visible'] = false;
        $settings = ['page_layout_trang-chu' => json_encode($layout), 'home_title_vi_font_size' => '54', 'home_title_vi_text_color' => '#123456'];
        $preview = $this->post('/admin/noi-dung/trang-chu/preview', ['settings' => $settings, 'preview_locale' => 'vi']);
        $preview->assertOk()->assertSee('.home-premium > .hp-final { order: 0;', false)
            ->assertSee('display: none !important;', false)->assertSee('font-size: 54px !important;', false)
            ->assertSee('color: #123456 !important;', false);
        $this->assertDatabaseMissing('site_settings', ['key' => 'home_title_vi_font_size']);
        $this->put('/admin/noi-dung/trang-chu', ['settings' => $settings])->assertSessionHasNoErrors();
        $this->get('/')->assertOk()->assertSee('font-size: 54px !important;', false);
    }

    public function test_invalid_layout_styles_and_unauthorized_changes_are_rejected(): void
    {
        $this->post('/admin/noi-dung/trang-chu/draft')->assertRedirect(route('admin.login'));
        $this->admin();
        $this->put('/admin/noi-dung/trang-chu', ['settings' => [
            'page_layout_trang-chu' => '[{"id":"body","visible":true}]',
            'home_title_vi_font_size' => 999, 'home_title_vi_text_color' => 'red;display:none',
        ]])->assertSessionHasErrors(['settings.page_layout_trang-chu', 'settings.home_title_vi_font_size', 'settings.home_title_vi_text_color']);
        $this->post('/admin/noi-dung/trang-chu/draft', ['settings' => ['admin_name' => 'Ignore']])->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('site_settings', ['key' => 'admin_name']);
        $editor = User::factory()->create(['role' => 'editor', 'is_active' => true, 'permissions' => ['jobs']]);
        $this->actingAs($editor)->post('/admin/noi-dung/trang-chu/draft')->assertForbidden();
        $this->post('/admin/noi-dung/trang-chu/restore')->assertForbidden();
        $this->delete('/admin/noi-dung/trang-chu/draft')->assertForbidden();
    }

    public function test_image_fitting_position_height_and_overlay_can_be_adjusted(): void
    {
        $this->admin();
        $this->get('/admin/noi-dung/trang-chu')->assertOk()
            ->assertSee('settings[home_hero_image_image_fit]', false)
            ->assertSee('data-image-adjustment', false);
        $settings = [
            'home_hero_image_image_fit' => 'contain', 'home_hero_image_image_x' => '0',
            'home_hero_image_image_y' => '75', 'home_hero_image_image_height' => '650',
            'home_hero_image_image_overlay' => '0',
        ];
        $this->post('/admin/noi-dung/trang-chu/preview', ['settings' => $settings, 'preview_locale' => 'vi'])
            ->assertOk()->assertSee('object-fit: contain !important;', false)
            ->assertSee('object-position: 0% 75% !important;', false)
            ->assertSee('min-height: 650px !important;', false)
            ->assertSee('rgba(16,47,67,0)', false);
        $this->assertDatabaseMissing('site_settings', ['key' => 'home_hero_image_image_fit']);
        $this->put('/admin/noi-dung/trang-chu', ['settings' => $settings])->assertSessionHasNoErrors();
        $this->get('/')->assertSee('object-position: 0% 75% !important;', false);
        $this->put('/admin/noi-dung/trang-chu', ['settings' => [
            'home_hero_image_image_fit' => 'invalid', 'home_hero_image_image_x' => -1,
            'home_hero_image_image_height' => 9999, 'home_hero_image_image_overlay' => 95,
        ]])->assertSessionHasErrors(['settings.home_hero_image_image_fit', 'settings.home_hero_image_image_x', 'settings.home_hero_image_image_height', 'settings.home_hero_image_image_overlay']);
        $this->put('/admin/noi-dung/trang-chu', ['settings' => array_fill_keys(array_keys($settings), '')])
            ->assertSessionHasNoErrors();
        $this->get('/')->assertDontSee('object-position: 0% 75% !important;', false);
    }

    public function test_missing_editor_assets_show_a_deployment_message_instead_of_500(): void
    {
        $this->admin();
        $originalPublicPath = public_path();
        $temporaryPublicPath = storage_path('framework/testing/editor-public-'.bin2hex(random_bytes(5)));
        foreach (['css/admin-workspace.css', 'js/admin-workspace.js', 'css/admin-navigation.css', 'css/admin-content.css', 'js/password-toggle.js'] as $asset) {
            \Illuminate\Support\Facades\File::ensureDirectoryExists(dirname($temporaryPublicPath.'/'.$asset));
            file_put_contents($temporaryPublicPath.'/'.$asset, '');
        }
        try {
            app()->usePublicPath($temporaryPublicPath);
            $this->get('/admin/noi-dung/trang-chu')->assertOk()
                ->assertSee('Hosting đang thiếu tệp của trình chỉnh sửa.')
                ->assertSee('public/css/visual-editor.css');
        } finally {
            app()->usePublicPath($originalPublicPath);
            \Illuminate\Support\Facades\File::deleteDirectory($temporaryPublicPath);
        }
    }

    public function test_mysql_text_limit_returns_an_actionable_message_and_rolls_back(): void
    {
        $this->admin();
        SiteSetting::putMany(['home_title_vi' => 'Published before error']);
        SiteSetting::saving(function (SiteSetting $setting) {
            if ($setting->key === 'content_history_trang-chu') {
                $cause = new \PDOException('Data too long for column value', 22001);
                $cause->errorInfo = ['22001', 1406, 'Data too long for column value'];
                throw new \Illuminate\Database\QueryException('mysql', 'insert into site_settings', [], $cause);
            }
        });
        try {
            $this->from('/admin/noi-dung/trang-chu')->put('/admin/noi-dung/trang-chu', [
                'settings' => ['home_title_vi' => 'Unsaved change'],
            ])->assertRedirect('/admin/noi-dung/trang-chu')->assertSessionHasErrors('settings')
                ->assertSessionHasInput('settings.home_title_vi', 'Unsaved change');
            $this->assertSame('Published before error', SiteSetting::values()['home_title_vi']);
            $this->assertArrayNotHasKey('content_history_trang-chu', SiteSetting::values());
        } finally {
            \Illuminate\Support\Facades\Event::forget('eloquent.saving: '.SiteSetting::class);
        }
    }

    public function test_large_draft_and_history_values_are_preserved(): void
    {
        $this->admin();
        $body = str_repeat('Nội dung y khoa. ', 900);
        $settings = array_fill_keys(['home_body_vi', 'home_body_en', 'home_intro_body_vi', 'home_intro_body_en', 'culture_body_vi', 'culture_body_en'], $body);
        $this->post('/admin/noi-dung/trang-chu/draft', ['settings' => $settings])->assertSessionHasNoErrors();
        $this->assertGreaterThan(65535, strlen(SiteSetting::values()['content_draft_trang-chu']));
        $this->put('/admin/noi-dung/trang-chu', ['settings' => $settings])->assertSessionHasNoErrors();
        $this->put('/admin/noi-dung/trang-chu', ['settings' => ['home_title_vi' => 'Next version']])->assertSessionHasNoErrors();
        $historyJson = SiteSetting::values()['content_history_trang-chu'];
        $this->assertGreaterThan(65535, strlen($historyJson));
        $this->assertSame(trim($body), json_decode($historyJson, true)[0]['settings']['home_body_vi']);
    }
}
