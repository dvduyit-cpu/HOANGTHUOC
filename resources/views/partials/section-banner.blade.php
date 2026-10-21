<section class="page-hero editable-hero section-banner {{ $bannerClass ?? '' }}" aria-labelledby="section-banner-heading">
    <img class="editable-hero-image" src="{{ !empty($site[$imageKey]) ? asset('storage/'.$site[$imageKey]) : asset('images/hero-healthcare-team.png') }}" alt="" fetchpriority="high">
    <div class="container">
        <span class="eyebrow" data-content-keys="{{ $eyebrowKey }}">{{ $site[$eyebrowKey] ?? '' }}</span>
        <h1 id="section-banner-heading" data-content-keys="{{ $titleKey }}">{{ $site[$titleKey] ?? '' }}</h1>
    </div>
</section>
