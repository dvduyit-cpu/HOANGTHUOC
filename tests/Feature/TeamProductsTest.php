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
        $this->get('/gioi-thieu')->assertSeeInOrder(['Product 1', 'Product 2', 'Product 3', 'Product 4'])
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
