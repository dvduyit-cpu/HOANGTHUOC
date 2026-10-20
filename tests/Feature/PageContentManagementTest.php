<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PageContentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_page_can_be_edited_and_changes_appear_publicly(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $pages = [
            'trang-chu' => ['/', 'home_block_3_vi'],
            'gioi-thieu' => ['/gioi-thieu', 'about_block_1_vi'],
            'doi-ngu' => ['/doi-ngu', 'team_block_2_vi'],
            'tuyen-dung' => ['/tuyen-dung', 'careers_block_2_vi'],
            'tin-tuc' => ['/tin-tuc', 'news_block_2_vi'],
            'lien-he' => ['/lien-he', 'contact_title_vi'],
        ];
        foreach ($pages as $section => [$url, $key]) {
            $this->get('/admin/noi-dung/'.$section)->assertOk()->assertSee('Xem trang này');
            $text = 'Nội dung mới '.$section;
            $this->put('/admin/noi-dung/'.$section, ['settings' => [$key => $text, 'admin_name' => 'Ignored']])
                ->assertSessionHasNoErrors();
            $this->get($url)->assertOk()->assertSee($text);
        }
        $this->assertDatabaseMissing('site_settings', ['key' => 'admin_name']);
    }

    public function test_uploaded_portrait_is_used_and_protected_from_media_deletion(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $this->put('/admin/noi-dung/doi-ngu', [
            'uploads' => ['team_portrait_image' => UploadedFile::fake()->image('portrait.jpg')],
        ])->assertSessionHasNoErrors();
        $path = SiteSetting::values()['team_portrait_image'];
        Storage::disk('public')->assertExists($path);
        $this->get('/doi-ngu')->assertOk()->assertSee(asset('storage/'.$path));
        $this->delete('/admin/kho-anh', ['path' => $path])->assertSessionHasErrors('path');
        Storage::disk('public')->assertExists($path);
        $this->put('/admin/noi-dung/doi-ngu', ['settings' => ['team_portrait_image' => '']])
            ->assertSessionHasNoErrors();
        $this->get('/doi-ngu')->assertDontSee(asset('storage/'.$path));
    }

    public function test_invalid_upload_and_image_path_do_not_change_saved_content(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $this->put('/admin/noi-dung/doi-ngu', [
            'settings' => ['team_portrait_image' => '../../private.jpg'],
            'uploads' => ['team_product_image_1' => UploadedFile::fake()->create('script.php', 1)],
        ])->assertSessionHasErrors(['settings.team_portrait_image', 'uploads.team_product_image_1']);
        $this->assertDatabaseMissing('site_settings', ['key' => 'team_portrait_image']);
    }
}
