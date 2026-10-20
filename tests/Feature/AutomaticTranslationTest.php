<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutomaticTranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_english_opens_translation_of_the_current_public_page(): void
    {
        $target = url('/tin-tuc');
        $this->get('/ngon-ngu/en?'.http_build_query(['redirect' => $target]))
            ->assertRedirect($target)->assertSessionHas('locale', 'en');
        $this->withSession(['locale' => 'en'])->get('/')->assertOk()->assertSee('<html lang="en">', false)->assertSee('Connecting Hearts');
    }

    public function test_redirects_cannot_translate_admin_or_redirect_to_another_site(): void
    {
        foreach ([url('/admin/settings'), 'https://example.org/'] as $target) {
            $this->get('/ngon-ngu/en?'.http_build_query(['redirect' => $target]))
                ->assertRedirect(route('home'));
        }
        $this->get('/ngon-ngu/vi?redirect=https://example.org/')->assertRedirect(route('home'));
    }

    public function test_admin_forms_support_optional_english_content(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $this->get('/admin/jobs/create')->assertOk()->assertSee('name="title_en"', false);
        $this->get('/admin/news/create')->assertOk()->assertSee('name="content_en"', false);
        foreach (['thuong-hieu', 'trang-chu', 'gioi-thieu', 'lien-he'] as $section) {
            $this->get('/admin/noi-dung/'.$section)->assertOk()->assertSee('_en]');
        }
    }
}
