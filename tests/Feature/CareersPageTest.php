<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CareersPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_and_careers_are_separate_and_job_search_stays_on_careers(): void
    {
        $user = User::factory()->create();
        foreach (['Nurse' => 'published', 'Consultant' => 'published', 'Private draft' => 'draft'] as $title => $status) {
            Job::create([
                'title' => $title, 'slug' => str_replace(' ', '-', strtolower($title)),
                'location' => 'Can Tho', 'summary' => 'Join our team', 'description' => 'Job details',
                'employment_type' => 'full-time', 'status' => $status,
                'published_at' => now(), 'created_by' => $user->id,
            ]);
        }
        $this->get('/')->assertOk()->assertViewIs('home')->assertDontSee('Nurse')->assertSee(route('jobs.index'));
        $this->get('/tuyen-dung')->assertOk()->assertViewIs('jobs.index')
            ->assertSee('Nurse')->assertSee('Consultant')->assertDontSee('Private draft')
            ->assertDontSee('class="home-hero"', false);
        $this->get('/tuyen-dung?q=Nurse')->assertOk()->assertSee('Nurse')->assertDontSee('Consultant');
        $this->get('/viec-lam/nurse')->assertOk()->assertSee('href="'.route('jobs.index').'"', false);
    }
}
