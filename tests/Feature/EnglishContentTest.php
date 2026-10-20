<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnglishContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_english_pages_and_switch_back_to_vietnamese(): void
    {
        $this->get('/ngon-ngu/en?'.http_build_query(['redirect' => url('/doi-ngu')]))
            ->assertRedirect(url('/doi-ngu'))->assertSessionHas('locale', 'en');
        foreach (['/' => 'Connecting Hearts', '/gioi-thieu' => 'PROTECTING EVERY HEARTBEAT',
            '/doi-ngu' => 'ONE JOURNEY.', '/tuyen-dung' => 'CAREER OPPORTUNITIES',
            '/tin-tuc' => 'News &amp; stories', '/lien-he' => 'Connect with Hoang Thuoc Medical'] as $path => $text) {
            $this->get($path)->assertOk()->assertSee('<html lang="en">', false)
                ->assertSee($text, false)->assertSee('/ngon-ngu/vi?');
        }
        $this->get('/ngon-ngu/vi?'.http_build_query(['redirect' => url('/doi-ngu')]))
            ->assertRedirect(url('/doi-ngu'))->assertSessionHas('locale', 'vi');
        $this->get('/')->assertSee('<html lang="vi">', false)->assertSee('Kết Nối Trái Tim');
    }

    public function test_english_can_be_edited_and_admin_stays_in_vietnamese(): void
    {
        $this->withSession(['locale' => 'en'])->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $this->get('/admin/noi-dung/trang-chu')->assertOk()->assertSee('Quản lý nội dung')
            ->assertSee('settings[home_title_en]', false);
        $this->put('/admin/noi-dung/trang-chu', ['settings' => ['home_title_vi' => 'Tiêu đề mới', 'home_title_en' => 'Our new English title']])
            ->assertSessionHasNoErrors();
        $this->get('/')->assertSee('Our new English title')->assertDontSee('Tiêu đề mới');
        $this->withSession(['locale' => 'vi'])->get('/')->assertSee('Tiêu đề mới')->assertDontSee('Our new English title');
    }

    public function test_missing_custom_translation_falls_back_without_breaking_the_page(): void
    {
        SiteSetting::putMany(['home_title_en' => '', 'home_title_vi' => 'Nội dung chưa dịch']);
        $this->withSession(['locale' => 'en'])->get('/')->assertOk()->assertSee('Nội dung chưa dịch');
    }
}
