<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MedicalWebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_revised_public_pages_render_and_link_to_the_team(): void
    {
        $this->get('/')->assertOk()->assertSee('Kết Nối Trái Tim')->assertSee(route('team'));
        $this->get('/gioi-thieu')->assertOk()->assertSee('BẢO VẸN NHỊP ĐẬP');
        $this->get('/doi-ngu')->assertOk()->assertSee('NHỮNG NHỊP TIM.')->assertSee('ecg-timeline');
        $this->get('/lien-he')->assertOk()->assertSee('Sản phẩm &amp; Giải pháp', false)
            ->assertSee('maps/search')->assertSee('name="subject"', false);
    }

    public function test_contact_topic_is_delivered_to_the_contact_mailer(): void
    {
        Mail::fake();
        $this->from('/lien-he')->post('/lien-he', [
            'name' => 'Đối tác', 'email' => 'partner@example.com',
            'phone' => '0939481678', 'subject' => 'Sản phẩm & Giải pháp',
            'message' => 'Tôi muốn tìm hiểu giải pháp can thiệp tim mạch.',
        ])->assertRedirect('/lien-he')->assertSessionHasNoErrors()->assertSessionHas('success');
        Mail::assertSent(ContactMessageReceived::class);
    }
}
