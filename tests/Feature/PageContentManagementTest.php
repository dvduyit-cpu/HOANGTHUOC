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

    public function test_about_story_image_is_independent_of_hero_and_duplicate_label_is_removed(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $this->put('/admin/noi-dung/gioi-thieu', ['uploads' => [
            'about_hero_image' => UploadedFile::fake()->image('hero.jpg'),
            'about_story_image' => UploadedFile::fake()->image('story.jpg'),
        ]])->assertSessionHasNoErrors();
        $values = SiteSetting::values();
        $heroPath = $values['about_hero_image'];
        $storyPath = $values['about_story_image'];
        $this->assertNotSame($heroPath, $storyPath);
        Storage::disk('public')->assertExists($storyPath);
        $this->get('/gioi-thieu')->assertOk()
            ->assertSee(asset('storage/'.$heroPath), false)
            ->assertSee('class="about-story-image" data-content-keys="about_story_image" src="'.asset('storage/'.$storyPath).'"', false)
            ->assertDontSee('data-content-keys="about_block_2_vi"', false);
        $this->put('/admin/noi-dung/gioi-thieu', ['uploads' => [
            'about_story_image' => UploadedFile::fake()->image('replacement.jpg'),
        ]])->assertSessionHasNoErrors();
        $this->assertSame($heroPath, SiteSetting::values()['about_hero_image']);
    }

    public function test_text_alignment_can_be_previewed_saved_and_validated(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $settings = ['about_body_vi_alignment' => 'justify', 'content_alignment_gioi-thieu' => 'right'];
        $this->get('/admin/noi-dung/gioi-thieu')->assertOk()
            ->assertSee('settings[about_body_vi_alignment]', false);
        $this->post('/admin/noi-dung/gioi-thieu/preview', [
            'settings' => $settings, 'preview_locale' => 'vi',
        ])->assertOk()->assertSee('[data-content-keys~="about_body_vi"] { text-align: justify; }', false);
        $this->assertDatabaseMissing('site_settings', ['key' => 'about_body_vi_alignment']);
        $this->put('/admin/noi-dung/gioi-thieu', ['settings' => $settings])->assertSessionHasNoErrors();
        $this->get('/gioi-thieu')->assertOk()->assertSee('text-align: right;', false)
            ->assertSee('[data-content-keys~="about_body_vi"] { text-align: justify; }', false);
        $this->withSession(['locale' => 'en'])->get('/gioi-thieu')->assertOk()
            ->assertSee('[data-content-keys~="about_body_vi"] { text-align: justify; }', false);
        $this->put('/admin/noi-dung/gioi-thieu', ['settings' => [
            'about_body_vi_alignment' => 'left; background:red',
        ]])->assertSessionHasErrors('settings.about_body_vi_alignment');
    }

    public function test_standards_background_can_switch_between_white_and_photo(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $key = 'home_standards_white_background';
        $this->get('/admin/noi-dung/trang-chu')->assertOk()->assertSee('type="checkbox"', false);
        $this->put('/admin/noi-dung/trang-chu', ['settings' => [$key => '1']])
            ->assertSessionHasNoErrors();
        $this->get('/')->assertOk()->assertSee('class="hp-standards-white"', false)
            ->assertDontSee(asset('images/cardiovascular-solutions.png'), false);
        $this->put('/admin/noi-dung/trang-chu', ['settings' => [$key => '0']])
            ->assertSessionHasNoErrors();
        $this->get('/')->assertOk()->assertSee('class="hp-photo-section hp-standards-background"', false)
            ->assertSee(asset('images/cardiovascular-solutions.png'), false);
        $this->put('/admin/noi-dung/trang-chu', ['settings' => [$key => 'invalid']])
            ->assertSessionHasErrors('settings.'.$key);
    }

    public function test_home_section_backgrounds_can_be_uploaded_and_removed(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));

        foreach (['home_process_image', 'home_updates_image'] as $key) {
            $this->get('/admin/noi-dung/trang-chu')->assertOk()->assertSee('settings['.$key.']', false);
            $this->put('/admin/noi-dung/trang-chu', [
                'uploads' => [$key => UploadedFile::fake()->image('background.jpg')],
            ])->assertSessionHasNoErrors();
            $path = SiteSetting::values()[$key];
            Storage::disk('public')->assertExists($path);
            if ($key === 'home_process_image') {
                $this->get('/')->assertOk()->assertSee(asset('storage/'.$path), false);
            }
            $this->put('/admin/noi-dung/trang-chu', ['settings' => [$key => '']])
                ->assertSessionHasNoErrors();
            $this->assertDatabaseHas('site_settings', ['key' => $key, 'value' => '']);
        }
        $this->get('/')->assertOk()->assertSee(asset('images/cardiovascular-solutions.png'), false);
    }

    public function test_website_colors_can_be_previewed_saved_and_validated(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $this->get('/admin/noi-dung/mau-sac')->assertOk()->assertSee('data-website-color', false);
        $settings = ['web_primary_color' => '#a12345', 'web_background_color' => '#fafafa'];
        $this->post('/admin/noi-dung/mau-sac/preview', ['settings' => $settings, 'preview_locale' => 'vi'])
            ->assertOk()->assertSee('--web-primary: #a12345', false)->assertSee('css/home.css', false);
        $this->assertDatabaseMissing('site_settings', ['key' => 'web_primary_color']);
        $this->put('/admin/noi-dung/mau-sac', ['settings' => $settings])->assertSessionHasNoErrors();
        foreach (['/', '/gioi-thieu', '/doi-ngu', '/tuyen-dung', '/tin-tuc', '/lien-he'] as $url) {
            $this->get($url)->assertOk()->assertSee('--web-primary: #a12345', false);
        }
        $this->put('/admin/noi-dung/mau-sac', ['settings' => ['web_primary_color' => 'red; color: blue']])
            ->assertSessionHasErrors('settings.web_primary_color');
        $this->assertDatabaseHas('site_settings', ['key' => 'web_primary_color', 'value' => '#a12345']);
    }

    public function test_preview_renders_draft_without_publishing_it(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $pages = ['trang-chu', 'gioi-thieu', 'doi-ngu', 'tuyen-dung', 'tin-tuc', 'lien-he', 'thuong-hieu'];
        foreach ($pages as $section) {
            $this->post('/admin/noi-dung/'.$section.'/preview', ['preview_locale' => 'vi'])
                ->assertOk()->assertHeader('Cache-Control', 'max-age=0, must-revalidate, no-store, private');
        }
        $this->post('/admin/noi-dung/trang-chu/preview', [
            'preview_locale' => 'vi',
            'settings' => ['home_value_1_number' => 'Draft number', 'home_value_1_title_size' => 34],
        ])->assertOk()->assertSee('Draft number')->assertSee('font-size: 34px', false)
            ->assertSee('data-content-keys="home_value_1_number home_value_1_number_size"', false)
            ->assertSee('data-content-keys="home_block_3_vi home_value_1_title_size"', false)
            ->assertSee('css/home.css', false);
        $this->assertDatabaseMissing('site_settings', ['key' => 'home_value_1_number']);
        $this->get('/')->assertDontSee('Draft number');
        $this->postJson('/admin/noi-dung/trang-chu/preview', [
            'preview_locale' => 'vi', 'settings' => ['home_value_1_title_size' => 'bad'],
        ])->assertUnprocessable()->assertJsonValidationErrors('settings.home_value_1_title_size');
    }

    public function test_preview_requires_authentication(): void
    {
        $this->postJson('/admin/noi-dung/trang-chu/preview', ['preview_locale' => 'vi'])->assertUnauthorized();
    }

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

    public function test_home_value_numbers_and_font_sizes_can_be_managed(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $settings = [];
        foreach (range(1, 3) as $index) {
            $key = 'home_value_'.$index;
            $this->get('/admin/noi-dung/trang-chu')->assertSee('settings['.$key.'_number]', false);
            $settings[$key.'_number'] = '0'.($index + 3);
            $settings[$key.'_number_size'] = 18;
            $settings[$key.'_title_size'] = 26;
            $settings[$key.'_body_size'] = 16;
        }
        $this->put('/admin/noi-dung/trang-chu', ['settings' => $settings])->assertSessionHasNoErrors();
        $page = $this->get('/')->assertOk();
        foreach (range(4, 6) as $number) {
            $page->assertSee('>0'.$number.'</span>', false);
        }
        foreach ([18, 26, 16] as $size) {
            $page->assertSee('font-size: '.$size.'px', false);
        }
        $this->put('/admin/noi-dung/trang-chu', ['settings' => [
            'home_value_1_number_size' => '18px',
            'home_value_1_title_size' => 97,
            'home_value_1_body_size' => 7,
        ]])->assertSessionHasErrors([
            'settings.home_value_1_number_size',
            'settings.home_value_1_title_size',
            'settings.home_value_1_body_size',
        ]);
        $this->assertDatabaseHas('site_settings', ['key' => 'home_value_1_title_size', 'value' => '26']);
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
            'uploads' => ['team_group_image' => UploadedFile::fake()->create('script.php', 1)],
        ])->assertSessionHasErrors(['settings.team_portrait_image', 'uploads.team_group_image']);
        $this->assertDatabaseMissing('site_settings', ['key' => 'team_portrait_image']);
    }

    public function test_team_group_image_and_description_can_be_managed(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
        $this->get('/admin/noi-dung/doi-ngu')->assertOk()
            ->assertSee('uploads[team_group_image]', false)
            ->assertSee('settings[team_group_description_vi]', false);
        $this->put('/admin/noi-dung/doi-ngu', [
            'uploads' => [
                'team_group_image' => UploadedFile::fake()->image('team.jpg'),
            ],
            'settings' => ['team_group_description_vi' => "Đội ngũ của chúng tôi\n<script>alert(1)</script>"],
        ])->assertSessionHasNoErrors();
        $path = SiteSetting::values()['team_group_image'];
        Storage::disk('public')->assertExists($path);
        $this->get('/doi-ngu')->assertOk()
            ->assertSeeInOrder(['Đội ngũ của chúng tôi', asset('storage/'.$path), 'ecg-timeline'])
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
        $this->put('/admin/noi-dung/doi-ngu', ['settings' => ['team_group_enabled' => '0']])
            ->assertSessionHasNoErrors();
        $this->get('/doi-ngu')->assertDontSee(asset('storage/'.$path))->assertDontSee('Đội ngũ của chúng tôi');
        $this->put('/admin/noi-dung/doi-ngu', ['settings' => ['team_group_enabled' => '1']])
            ->assertSessionHasNoErrors();
        $this->get('/doi-ngu')->assertSee(asset('storage/'.$path));
        $this->put('/admin/noi-dung/doi-ngu', ['settings' => [
            'team_group_image' => '', 'team_group_description_vi' => '',
        ]])->assertSessionHasNoErrors();
        $this->get('/doi-ngu')->assertSee(asset('images/hero-healthcare-team.png'));
    }
}
