@extends('layouts.app')
@section('title', 'Trang chủ | '.($site['site_title'] ?? 'Hoang Thuoc Medical'))
@section('content')
@php
    $heroImage = !empty($site['home_hero_image']) ? asset('storage/'.$site['home_hero_image']) : asset('images/hero-healthcare-team.png');
@endphp
<div class="home-premium">
<section class="hp-hero" aria-labelledby="home-heading">
    <img class="hp-hero-image" src="{{ $heroImage }}" alt="Đội ngũ chuyên môn y tế Hoang Thuoc Medical" fetchpriority="high">
    <div class="hp-hero-shade"></div>
    <div class="container hp-hero-content">
        <span class="hp-kicker hp-kicker-light"><i></i>{{ $site['home_eyebrow_vi'] ?? 'HOANG THUOC MEDICAL CAREERS' }}</span>
        <h1 id="home-heading">{{ ($site['home_title_vi'] ?? '') ?: 'Cùng kiến tạo những giá trị chăm sóc bền vững' }}</h1>
        <p>{{ ($site['home_body_vi'] ?? '') ?: 'Mang chuyên môn và sự tận tâm của bạn đến một hành trình nghề nghiệp có ý nghĩa.' }}</p>
        <div class="hp-actions"><a class="hp-button hp-button-white" href="{{ route('jobs.index') }}">Khám phá cơ hội <span aria-hidden="true">↗</span></a><a class="hp-text-link" href="{{ route('about') }}">Tìm hiểu về chúng tôi <span aria-hidden="true">→</span></a></div>
    </div>
    <div class="container hp-hero-bottom"><span>CHUYÊN MÔN VỮNG VÀNG. GIÁ TRỊ BỀN LÂU.</span><a href="#cung-dong-hanh">Khám phá thêm <span aria-hidden="true">↓</span></a></div>
</section>
<section class="hp-opportunity" aria-label="Cơ hội nghề nghiệp"><div class="container hp-opportunity-inner"><div><span class="hp-status-dot"></span><strong>{{ $openPositions > 0 ? 'Chúng tôi đang tìm kiếm đồng đội' : 'Kết nối cho hành trình tiếp theo' }}</strong></div><a href="{{ route('jobs.index') }}">{{ $openPositions }} vị trí đang mở <span aria-hidden="true">→</span></a></div></section>

<section class="container hp-intro" id="cung-dong-hanh" aria-labelledby="intro-heading">
    <div><span class="hp-kicker">01 / CÙNG ĐỒNG HÀNH</span><h2 id="intro-heading">{{ ($site['home_intro_title_vi'] ?? '') ?: 'Một công việc ý nghĩa. Một hành trình xứng đáng.' }}</h2></div>
    <div class="hp-intro-copy"><p>{{ ($site['home_intro_body_vi'] ?? '') ?: 'Chúng tôi tin rằng những giá trị tốt đẹp bắt đầu từ con người. Cùng nhau, chúng ta học hỏi, kết nối và đóng góp bằng chuyên môn của mình.' }}</p><a class="hp-inline-link" href="{{ route('about') }}">Câu chuyện của chúng tôi <span aria-hidden="true">↗</span></a></div>
</section>
<section class="container hp-values" aria-label="Tinh thần làm việc">
    <article><span class="hp-value-number">01</span><h3>Tận tâm với con người</h3><p>Đặt sự lắng nghe, thấu hiểu và trách nhiệm vào từng công việc. Mỗi đóng góp đều hướng đến những giá trị chăm sóc thiết thực.</p></article>
    <article><span class="hp-value-number">02</span><h3>Vững vàng chuyên môn</h3><p>Trân trọng năng lực, nuôi dưỡng tinh thần học hỏi và chủ động hoàn thiện bản thân qua từng trải nghiệm làm việc.</p></article>
    <article><span class="hp-value-number">03</span><h3>Cùng nhau tiến xa</h3><p>Kết nối bằng sự tôn trọng và tin cậy. Chia sẻ góc nhìn, hỗ trợ đồng đội và cùng tìm ra những cách làm tốt hơn.</p></article>
</section>
<section class="hp-culture" aria-labelledby="culture-heading"><div class="container hp-culture-inner">
    <div class="hp-culture-mark" aria-hidden="true"><span>Chuyên môn.</span><span>Tận tâm.</span><span>Đồng hành.</span><i>H<span>+</span></i></div>
    <div class="hp-culture-copy"><span class="hp-kicker">02 / CON NGƯỜI & VĂN HÓA</span><h2 id="culture-heading">{{ ($site['culture_title_vi'] ?? '') ?: 'Chúng tôi tin rằng con người là trọng tâm.' }}</h2><p>{{ ($site['culture_body_vi'] ?? '') ?: 'Một hành trình nghề nghiệp tốt được tạo nên từ đội ngũ cùng chí hướng, sự tin cậy và những trải nghiệm đáng tự hào mỗi ngày.' }}</p><a class="hp-inline-link" href="{{ route('news') }}">Nhịp sống tại {{ $site['company_name'] ?? 'Hoang Thuoc Medical' }} <span aria-hidden="true">↗</span></a></div>
</div></section>
<section class="container hp-process" aria-labelledby="process-heading">
    <div class="hp-section-head"><div><span class="hp-kicker">03 / BẮT ĐẦU HÀNH TRÌNH</span><h2 id="process-heading">Cơ hội mới bắt đầu<br>từ một kết nối.</h2></div><p>Tìm hiểu công việc phù hợp và chia sẻ với chúng tôi về bạn.</p></div>
    <ol class="hp-steps"><li><span>01</span><h3>Khám phá vị trí</h3><p>Tìm hiểu yêu cầu, công việc và quyền lợi tại trang tuyển dụng.</p></li><li><span>02</span><h3>Gửi hồ sơ của bạn</h3><p>Ứng tuyển trực tiếp tại vị trí bạn quan tâm với CV và lời giới thiệu.</p></li><li><span>03</span><h3>Kết nối cùng nhân sự</h3><p>Bộ phận tuyển dụng xem xét hồ sơ và liên hệ khi phù hợp.</p></li></ol>
</section>
@if($latestNews->isNotEmpty())
<section class="hp-updates"><div class="container"><div class="hp-section-head"><div><span class="hp-kicker">GÓC CHIA SẺ</span><h2>Câu chuyện & cập nhật</h2></div><a class="hp-inline-link" href="{{ route('news') }}">Xem tất cả <span aria-hidden="true">↗</span></a></div>
    <div class="hp-news-grid">@foreach($latestNews as $post)<article class="hp-news-item">@if($post->cover_image)<a href="{{ route('news.show', $post->slug) }}" tabindex="-1" aria-hidden="true"><img src="{{ asset('storage/'.$post->cover_image) }}" alt="" loading="lazy"></a>@endif<div><time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('d.m.Y') }}</time><h3><a href="{{ route('news.show', $post->slug) }}">{{ $post->localized('title') }}</a></h3><p>{{ $post->localized('excerpt') }}</p><a class="hp-inline-link" href="{{ route('news.show', $post->slug) }}">Đọc câu chuyện <span aria-hidden="true">→</span></a></div></article>@endforeach</div>
</div></section>
@endif
<section class="container hp-final"><span class="hp-kicker">BƯỚC TIẾP THEO CỦA BẠN</span><h2>Sẵn sàng viết tiếp<br>câu chuyện nghề nghiệp?</h2><p>Mang năng lực và sự tận tâm của bạn đến với đội ngũ chúng tôi.</p><div class="hp-actions"><a class="hp-button" href="{{ route('jobs.index') }}">Xem cơ hội nghề nghiệp <span aria-hidden="true">↗</span></a><a class="hp-inline-link" href="{{ route('contact.create') }}">Liên hệ nhân sự <span aria-hidden="true">→</span></a></div></section>
</div>
@endsection
