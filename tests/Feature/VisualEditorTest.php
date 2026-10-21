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
}
