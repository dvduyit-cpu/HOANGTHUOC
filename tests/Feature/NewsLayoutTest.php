<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_the_newest_published_story_is_featured_on_the_first_page(): void
    {
        foreach (range(1, 10) as $index) {
            News::create([
                'title_vi' => 'Story '.$index, 'slug' => 'story-'.$index,
                'excerpt_vi' => 'Excerpt', 'content_vi' => 'Content',
                'status' => 'published', 'published_at' => now()->subDays(11 - $index),
            ]);
        }
        News::create([
            'title_vi' => 'Scheduled story', 'slug' => 'scheduled',
            'excerpt_vi' => 'Excerpt', 'content_vi' => 'Content',
            'status' => 'published', 'published_at' => now()->addDay(),
        ]);

        $response = $this->get('/tin-tuc')->assertOk()
            ->assertSee('news-card-latest', false)->assertSee('news-card-compact', false)
            ->assertSeeInOrder(['Story 10', 'Story 9'])->assertDontSee('Scheduled story');
        $this->assertSame(1, substr_count($response->getContent(), 'class="news-card news-card-latest"'));
        $this->get('/tin-tuc?page=2')->assertOk()->assertSee('Story 1')
            ->assertDontSee('news-card-latest', false)->assertSee('news-card-compact', false);
    }
}
