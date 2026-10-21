<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\TeamProducts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeamProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_medical_products_layout_can_be_changed_in_admin(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $this->post('/admin/products', ['name' => 'Stent', 'sort_order' => 0, 'is_published' => 1])
            ->assertSessionHasNoErrors();
        $this->get('/admin/products')->assertOk()->assertSee('settings[medical_products_layout]', false);
        $this->get('/gioi-thieu')->assertOk()->assertSee('product-slider--three', false);
        foreach (['single', 'three'] as $layout) {
            $this->put('/admin/noi-dung/gioi-thieu', ['settings' => ['medical_products_layout' => $layout]])
                ->assertSessionHasNoErrors();
            $this->get('/gioi-thieu')->assertOk()->assertSee('product-slider--'.$layout, false);
        }
        $this->put('/admin/noi-dung/gioi-thieu', ['settings' => ['medical_products_layout' => 'invalid']])
            ->assertSessionHasErrors('settings.medical_products_layout');
    }

    public function test_multiple_product_images_form_a_slider_and_remain_protected_in_media_library(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $data = ['name' => 'Medical solutions', 'sort_order' => 0, 'is_published' => 1];
        $this->post('/admin/products', $data + ['images' => [
            UploadedFile::fake()->image('tools.jpg'),
            UploadedFile::fake()->image('balloon.jpg'),
            UploadedFile::fake()->image('stent.jpg'),
        ]])->assertSessionHasNoErrors();
        $product = TeamProducts::all()->first();
        $this->assertCount(3, $product['gallery_paths']);
        $admin = $this->get('/admin/products')->assertOk()
            ->assertSee('Slider Giải pháp y khoa')
            ->assertSee('name="images[]" multiple', false);
        foreach ($product['gallery_paths'] as $path) {
            $admin->assertSee(asset('storage/'.$path), false);
        }
        $page = $this->get('/gioi-thieu')->assertOk()->assertSee('data-slide-counter', false);
        foreach ($product['gallery_paths'] as $path) {
            Storage::disk('public')->assertExists($path);
            $page->assertSee(asset('storage/'.$path), false);
            $this->delete('/admin/kho-anh', ['path' => $path])->assertSessionHasErrors('path');
        }
        $this->put('/admin/products/'.$product['id'], $data)->assertSessionHasNoErrors();
        $this->assertSame($product['gallery_paths'], TeamProducts::all()->first()['gallery_paths']);
        $this->put('/admin/products/'.$product['id'], $data + ['gallery_paths' => ['']])
            ->assertSessionHasNoErrors();
        $this->assertSame([], TeamProducts::all()->first()['gallery_paths']);
        $this->post('/admin/products', $data + ['gallery_paths' => ['../private.png'], 'images' => [
            UploadedFile::fake()->create('invalid.php', 1),
        ]])->assertSessionHasErrors(['gallery_paths.0', 'images.0']);
    }

    public function test_products_can_be_created_updated_hidden_and_deleted(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $this->get('/admin/products/create')->assertOk();
        $data = ['name' => 'Stent mạch vành', 'name_en' => 'Precision coronary stent 42', 'description' => 'Giải pháp tim mạch',
            'description_en' => 'Cardiovascular solution', 'sort_order' => 1, 'is_published' => 1];
        $this->post('/admin/products', $data + ['image' => UploadedFile::fake()->image('stent.png')])
            ->assertRedirect(route('admin.products.index'))->assertSessionHasNoErrors();
        $product = TeamProducts::all()->first();
        Storage::disk('public')->assertExists($product['image_path']);
        $this->get('/gioi-thieu')->assertOk()->assertSee('Stent mạch vành')->assertSee(asset('storage/'.$product['image_path']));
        $this->withSession(['locale' => 'en'])->get('/gioi-thieu')->assertSee('Precision coronary stent 42')->assertSee('Cardiovascular solution');
        $this->get('/admin/products/'.$product['id'].'/edit')->assertOk()->assertSee('Stent mạch vành');
        $this->delete('/admin/kho-anh', ['path' => $product['image_path']])->assertSessionHasErrors('path');
        $this->put('/admin/products/'.$product['id'], array_replace($data, ['name' => 'Sản phẩm ẩn', 'is_published' => 0, 'image_path' => $product['image_path']]))
            ->assertSessionHasNoErrors();
        $this->get('/gioi-thieu')->assertDontSee('Sản phẩm ẩn')->assertDontSee('Precision coronary stent 42');
        $this->delete('/admin/products/'.$product['id'])->assertSessionHas('success');
        $this->assertCount(0, TeamProducts::all());
        Storage::disk('public')->assertExists($product['image_path']);
    }

    public function test_multiple_products_are_sorted_and_content_is_escaped(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        foreach ([4, 3, 2, 1] as $i) {
            $this->post('/admin/products', ['name' => 'Product '.$i, 'description' => '<script>alert(1)</script>',
                'sort_order' => $i, 'is_published' => 1])->assertSessionHasNoErrors();
        }
        $this->get('/gioi-thieu')->assertSee('data-product-slider', false)
            ->assertSee('data-slide-next', false)
            ->assertSeeInOrder(['Product 1', 'Product 2', 'Product 3', 'Product 4'])
            ->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;', false);
    }

    public function test_product_management_requires_content_permission_and_valid_images(): void
    {
        $this->get('/admin/products')->assertRedirect(route('admin.login'));
        $editor = User::factory()->create(['role' => 'editor', 'is_active' => true, 'permissions' => ['jobs']]);
        $this->actingAs($editor)->get('/admin/products')->assertForbidden();
        $this->post('/admin/products', [])->assertForbidden();
        $editor->update(['permissions' => ['content']]);
        $this->get('/admin/products')->assertOk();
        $this->post('/admin/products', ['name' => 'Invalid', 'sort_order' => 0, 'is_published' => 1,
            'image_path' => '../../private.png', 'image' => UploadedFile::fake()->create('script.php', 1)])
            ->assertSessionHasErrors(['image_path', 'image']);
        $this->assertCount(0, TeamProducts::all());
    }
}
