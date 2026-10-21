@extends('layouts.app')
@section('title', __('Trang chủ').' | '.($site['site_title'] ?? 'Hoang Thuoc Medical'))
@section('content')
@php
    $heroImage = !empty($site['home_hero_image']) ? asset('storage/'.$site['home_hero_image']) : asset('images/hero-healthcare-team.png');
@endphp
<div class="home-premium">
<section class="hp-hero" aria-labelledby="home-heading">
    <img class="hp-hero-image" src="{{ $heroImage }}" alt="Đội ngũ chuyên môn y tế Hoang Thuoc Medical" fetchpriority="high">
    <div class="hp-hero-shade"></div>
    <div class="container hp-hero-content">
        <span class="hp-kicker hp-kicker-light" data-content-keys="home_eyebrow_vi"><i></i>{{ $site['home_eyebrow_vi'] ?? 'HOANG THUOC MEDICAL CAREERS' }}</span>
        <h1 id="home-heading" data-content-keys="home_title_vi">{{ ($site['home_title_vi'] ?? '') ?: 'Cùng kiến tạo những giá trị chăm sóc bền vững' }}</h1>
        <p data-content-keys="home_body_vi">{{ ($site['home_body_vi'] ?? '') ?: 'Mang chuyên môn và sự tận tâm của bạn đến một hành trình nghề nghiệp có ý nghĩa.' }}</p>
        <div class="hp-actions"><a class="hp-button hp-button-white" href="{{ route('jobs.index') }}">{{ __('Khám phá cơ hội') }} <span aria-hidden="true">↗</span></a><a class="hp-text-link" href="{{ route('about') }}">{{ __('Tìm hiểu về chúng tôi') }} <span aria-hidden="true">→</span></a></div>
    </div>
    <div class="container hp-hero-bottom"><span data-content-keys="home_block_1_vi">{{ $site['home_block_1_vi'] }}</span><a href="#cung-dong-hanh">{{ __('Khám phá thêm') }} <span aria-hidden="true">↓</span></a></div>
</section>
<section class="hp-opportunity" aria-label="Cơ hội nghề nghiệp"><div class="container hp-opportunity-inner"><div><span class="hp-status-dot"></span><strong>{{ __($openPositions > 0 ? 'Chúng tôi đang tìm kiếm đồng đội' : 'Kết nối cho hành trình tiếp theo') }}</strong></div><a href="{{ route('jobs.index') }}">{{ $openPositions }} {{ __('vị trí đang mở') }} <span aria-hidden="true">→</span></a></div></section>

<section class="{{ ($site['home_standards_white_background'] ?? '0') == '1' ? 'hp-standards-white' : 'hp-photo-section hp-standards-background' }}" id="cung-dong-hanh" aria-labelledby="intro-heading">
    @if(($site['home_standards_white_background'] ?? '0') != '1')
    <img class="hp-section-background" src="{{ !empty($site['home_process_image']) ? asset('storage/'.$site['home_process_image']) : asset('images/cardiovascular-solutions.png') }}" alt="" loading="lazy">
    @endif
<div class="container hp-intro">
    <div><span class="hp-kicker" data-content-keys="home_block_2_vi">{{ $site['home_block_2_vi'] }}</span><h2 id="intro-heading" data-content-keys="home_intro_title_vi">{{ ($site['home_intro_title_vi'] ?? '') ?: 'Một công việc ý nghĩa. Một hành trình xứng đáng.' }}</h2></div>
    <div class="hp-intro-copy"><p data-content-keys="home_intro_body_vi">{{ ($site['home_intro_body_vi'] ?? '') ?: 'Chúng tôi tin rằng những giá trị tốt đẹp bắt đầu từ con người. Cùng nhau, chúng ta học hỏi, kết nối và đóng góp bằng chuyên môn của mình.' }}</p><a class="hp-inline-link" href="{{ route('about') }}">{{ __('Câu chuyện của chúng tôi') }} <span aria-hidden="true">↗</span></a></div>
</div>
<section class="container hp-values" aria-label="Tinh thần làm việc">
    @foreach(range(1, 3) as $valueIndex)
    @php
        $valueKey = 'home_value_'.$valueIndex;
        $titleKey = 'home_block_'.($valueIndex * 2 + 1).'_vi';
        $bodyKey = 'home_block_'.($valueIndex * 2 + 2).'_vi';
        $numberSize = filled($site[$valueKey.'_number_size'] ?? null) ? max(8, min(96, (int) $site[$valueKey.'_number_size'])) : 13;
        $titleSize = filled($site[$valueKey.'_title_size'] ?? null) ? max(8, min(96, (int) $site[$valueKey.'_title_size'])) : 20;
        $bodySize = filled($site[$valueKey.'_body_size'] ?? null) ? max(8, min(96, (int) $site[$valueKey.'_body_size'])) : 14;
    @endphp
    <article>
        <span data-content-keys="{{ $valueKey }}_number {{ $valueKey }}_number_size" class="hp-value-number" style="font-size: {{ $numberSize }}px">{{ $site[$valueKey.'_number'] ?? sprintf('%02d', $valueIndex) }}</span>
        <h3 data-content-keys="{{ $titleKey }} {{ $valueKey }}_title_size" style="font-size: {{ $titleSize }}px">{{ $site[$titleKey] }}</h3>
        <p data-content-keys="{{ $bodyKey }} {{ $valueKey }}_body_size" style="font-size: {{ $bodySize }}px">{{ $site[$bodyKey] }}</p>
    </article>
    @endforeach
</section>
</section>
<section class="hp-culture {{ !empty($site['culture_image']) ? 'hp-culture-photo' : '' }}" aria-labelledby="culture-heading">@if(!empty($site['culture_image']))<img class="culture-background" src="{{ asset('storage/'.$site['culture_image']) }}" alt="Chăm sóc người bệnh" loading="lazy">@endif<div class="container hp-culture-inner">
    <div class="hp-culture-mark" aria-hidden="true"><span data-content-keys="home_block_9_vi">{{ $site['home_block_9_vi'] }}</span><span data-content-keys="home_block_10_vi">{{ $site['home_block_10_vi'] }}</span><span data-content-keys="home_block_11_vi">{{ $site['home_block_11_vi'] }}</span><i>H<span>+</span></i></div>
    <div class="hp-culture-copy"><span class="hp-kicker" data-content-keys="home_block_12_vi">{{ $site['home_block_12_vi'] }}</span><h2 id="culture-heading" data-content-keys="culture_title_vi">{{ ($site['culture_title_vi'] ?? '') ?: 'Chúng tôi tin rằng con người là trọng tâm.' }}</h2><p data-content-keys="culture_body_vi">{{ ($site['culture_body_vi'] ?? '') ?: 'Một hành trình nghề nghiệp tốt được tạo nên từ đội ngũ cùng chí hướng, sự tin cậy và những trải nghiệm đáng tự hào mỗi ngày.' }}</p><a class="hp-inline-link" href="{{ route('team') }}">{{ __('Đội ngũ tại') }} {{ $site['company_name'] ?? 'Hoang Thuoc Medical' }} <span aria-hidden="true">↗</span></a></div>
</div></section>
<section class="hp-process {{ !empty($site['home_journey_background_image']) ? 'hp-process-photo' : '' }}" aria-labelledby="process-heading">
    @if(!empty($site['home_journey_background_image']))<img class="hp-journey-background" data-content-keys="home_journey_background_image" src="{{ asset('storage/'.$site['home_journey_background_image']) }}" alt="" loading="lazy">@endif
    <div class="container">
    <div class="hp-section-head"><div><span class="hp-kicker" data-content-keys="home_block_13_vi">{{ $site['home_block_13_vi'] }}</span><h2 id="process-heading" data-content-keys="home_block_14_vi">{{ $site['home_block_14_vi'] }}</h2></div><p data-content-keys="home_block_15_vi">{{ $site['home_block_15_vi'] }}</p></div>
    <ol class="hp-steps"><li><span>01</span><h3 data-content-keys="home_block_16_vi">{{ $site['home_block_16_vi'] }}</h3><p data-content-keys="home_block_17_vi">{{ $site['home_block_17_vi'] }}</p></li><li><span>02</span><h3 data-content-keys="home_block_18_vi">{{ $site['home_block_18_vi'] }}</h3><p data-content-keys="home_block_19_vi">{{ $site['home_block_19_vi'] }}</p></li><li><span>03</span><h3 data-content-keys="home_block_20_vi">{{ $site['home_block_20_vi'] }}</h3><p data-content-keys="home_block_21_vi">{{ $site['home_block_21_vi'] }}</p></li></ol>
</div></section>
@if($latestNews->isNotEmpty())
<section class="hp-updates hp-photo-section">
    <img class="hp-section-background" src="{{ !empty($site['home_updates_image']) ? asset('storage/'.$site['home_updates_image']) : asset('images/hero-healthcare-team.png') }}" alt="" loading="lazy">
    <div class="container"><div class="hp-section-head"><div><span class="hp-kicker" data-content-keys="home_block_22_vi">{{ $site['home_block_22_vi'] }}</span><h2 data-content-keys="home_block_23_vi">{{ $site['home_block_23_vi'] }}</h2></div><a class="hp-inline-link" href="{{ route('news') }}">{{ __('Xem tất cả') }} <span aria-hidden="true">↗</span></a></div>
    <div class="hp-news-grid">@foreach($latestNews as $post)<article class="hp-news-item">@if($post->cover_image)<a href="{{ route('news.show', $post->slug) }}" tabindex="-1" aria-hidden="true"><img src="{{ asset('storage/'.$post->cover_image) }}" alt="" loading="lazy"></a>@endif<div><time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('d.m.Y') }}</time><h3><a href="{{ route('news.show', $post->slug) }}">{{ $post->localized('title') }}</a></h3><p>{{ $post->localized('excerpt') }}</p><a class="hp-inline-link" href="{{ route('news.show', $post->slug) }}">{{ __('Đọc câu chuyện') }} <span aria-hidden="true">→</span></a></div></article>@endforeach</div>
</div></section>
@endif
<section class="container hp-final"><span class="hp-kicker" data-content-keys="home_block_24_vi">{{ $site['home_block_24_vi'] }}</span><h2 data-content-keys="home_block_25_vi">{{ $site['home_block_25_vi'] }}</h2><p data-content-keys="home_block_26_vi">{{ $site['home_block_26_vi'] }}</p><div class="hp-actions"><a class="hp-button" href="{{ route('jobs.index') }}">{{ __('Xem cơ hội nghề nghiệp') }} <span aria-hidden="true">↗</span></a><a class="hp-inline-link" href="{{ route('contact.create') }}">{{ __('Liên hệ nhân sự') }} <span aria-hidden="true">→</span></a></div></section>
</div>
@endsection
