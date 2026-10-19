<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_unused_images_can_be_deleted_but_used_images_and_other_files_are_preserved(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('media/2026/unused.png', 'image');
        Storage::disk('public')->put('media/2026/hero.png', 'image');
        Storage::disk('public')->put('document.txt', 'document');
        \App\Models\SiteSetting::putMany(['home_hero_image' => 'media/2026/hero.png']);
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $this->delete('/admin/kho-anh', ['path' => 'media/2026/unused.png'])->assertSessionHasNoErrors()->assertSessionHas('success');
        Storage::disk('public')->assertMissing('media/2026/unused.png');
        $this->delete('/admin/kho-anh', ['path' => 'media/2026/hero.png'])->assertSessionHasErrors('path');
        Storage::disk('public')->assertExists('media/2026/hero.png');
        foreach (['document.txt', '../private/file.png'] as $path) {
            $this->delete('/admin/kho-anh', ['path' => $path])->assertSessionHasErrors('path');
        }
        Storage::disk('public')->assertExists('document.txt');
        $this->actingAs(User::factory()->create(['role' => 'editor', 'is_active' => true, 'permissions' => ['jobs']]));
        $this->delete('/admin/kho-anh', ['path' => 'media/2026/hero.png'])->assertForbidden();
    }

    public function test_website_images_are_imported_once_and_available_in_library(): void
    {
        Storage::fake('public');
        $this->artisan('media:import-website')->assertSuccessful();
        $files = Storage::disk('public')->allFiles();
        $this->assertNotEmpty($files);
        $this->assertTrue(collect($files)->contains(fn ($path) => str_contains($path, '/website/hoang-thuoc-logo-')));
        $this->assertTrue(collect($files)->contains(fn ($path) => str_contains($path, '/website/hero-healthcare-team-')));
        $this->artisan('media:import-website')->assertSuccessful();
        $this->assertSame($files, Storage::disk('public')->allFiles());
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $this->get('/admin/kho-anh')->assertOk()->assertSee($files[0]);
        $this->get('/admin/noi-dung/trang-chu')->assertOk()->assertSee($files[0]);
    }

    public function test_uploaded_image_is_archived_by_year_and_can_be_used_as_hero(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $this->post('/admin/kho-anh', ['images' => [UploadedFile::fake()->image('banner.jpg')]])->assertSessionHasNoErrors();
        $path = Storage::disk('public')->allFiles()[0];
        $this->assertStringStartsWith('media/'.now()->timezone('Asia/Bangkok')->format('Y').'/', $path);
        $this->get('/admin/kho-anh')->assertOk()->assertSee($path);
        $this->get('/admin/noi-dung/trang-chu')->assertOk()->assertSee($path);
        $this->put('/admin/noi-dung/trang-chu', ['settings' => ['home_hero_image' => $path]])->assertSessionHasNoErrors();
        $this->get('/')->assertOk()->assertSee(asset('storage/'.$path));
        $this->put('/admin/noi-dung/trang-chu', ['settings' => ['home_hero_image' => '']])->assertSessionHasNoErrors();
        Storage::disk('public')->assertExists($path);
        $this->get('/')->assertSee(asset('images/hero-healthcare-team.png'));
    }

    public function test_invalid_selection_and_unauthorized_uploads_are_rejected(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $this->put('/admin/noi-dung/trang-chu', ['settings' => ['home_hero_image' => '../private/file']])->assertSessionHasErrors('settings.home_hero_image');
        $this->post('/admin/kho-anh', ['images' => [UploadedFile::fake()->create('file.txt')]])->assertSessionHasErrors('images.0');
        $this->actingAs(User::factory()->create(['role' => 'editor', 'is_active' => true, 'permissions' => ['jobs']]));
        $this->get('/admin/kho-anh')->assertForbidden();
        $this->post('/admin/kho-anh')->assertForbidden();
    }
}
