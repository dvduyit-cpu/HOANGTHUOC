<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Support\TeamProducts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdateWebsiteContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_install_preserves_existing_content_and_does_not_duplicate_products(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        SiteSetting::putMany(['home_title_vi' => 'Nội dung đang dùng', 'team_product_image_1' => 'custom.png']);
        $this->artisan('website:update-content')->assertSuccessful();
        $this->artisan('website:update-content')->assertSuccessful();
        $this->assertDatabaseHas('site_settings', ['key' => 'home_title_vi', 'value' => 'Nội dung đang dùng']);
        $this->assertCount(1, TeamProducts::all());
        Storage::disk('public')->assertExists('media/2026/products/cardiovascular-solutions.png');
        $this->assertCount(2, Storage::disk('local')->allFiles('deployment/backups'));
    }

    public function test_overwrite_updates_text_but_preserves_images_products_and_unrelated_settings(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $productKey = TeamProducts::PREFIX.'cardiovascular-solutions-overview';
        SiteSetting::putMany(['home_title_vi' => 'Old content', 'team_product_image_1' => 'custom.png',
            'admin_name' => 'My admin', $productKey => '{"name":"Custom product","sort_order":0,"is_published":0}']);
        $this->artisan('website:update-content', ['--overwrite' => true])->assertSuccessful();
        $this->assertDatabaseHas('site_settings', ['key' => 'home_title_vi', 'value' => config('medical-content.home_title_vi')]);
        $this->assertDatabaseHas('site_settings', ['key' => 'team_product_image_1', 'value' => 'custom.png']);
        $this->assertDatabaseHas('site_settings', ['key' => 'admin_name', 'value' => 'My admin']);
        $this->assertSame('Custom product', TeamProducts::all()->first()['name']);
        $backup = json_decode(Storage::disk('local')->get(Storage::disk('local')->allFiles('deployment/backups')[0]), true);
        $this->assertSame('Old content', $backup['existing']['home_title_vi']);
    }
}
