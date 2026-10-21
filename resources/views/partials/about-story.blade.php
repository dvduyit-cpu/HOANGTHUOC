<section class="container section about-story about-story-photo" aria-labelledby="about-story-heading">
    <img class="about-story-image" data-content-keys="about_story_image" src="{{ !empty($site['about_story_image']) ? asset('storage/'.$site['about_story_image']) : asset('images/hero-healthcare-team.png') }}" alt="{{ $locale === 'en' ? 'Hoang Thuoc Medical team' : 'Đội ngũ Hoàng Thuộc Medical' }}" loading="lazy">
    <div class="about-story-copy">
        <h2 id="about-story-heading" data-content-keys="about_story_title_vi">{{ $site['about_story_title_'.$locale] ?? ($locale === 'en' ? 'Our story' : 'Câu chuyện về chúng tôi') }}</h2>
        <p data-content-keys="about_body_vi">{!! nl2br(e($site['about_body_'.$locale] ?? '')) !!}</p>
        <p data-content-keys="about_block_3_vi">{{ $site['about_block_3_vi'] }}</p>
        <a class="link" href="{{ route('jobs.index') }}">{{ $locale === 'en' ? 'Explore career opportunities' : 'Xem cơ hội nghề nghiệp' }} →</a>
    </div>
</section>
