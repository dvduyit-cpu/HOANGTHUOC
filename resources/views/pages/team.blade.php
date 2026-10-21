@extends('layouts.app')
@section('title', __('Đội ngũ').' | Hoang Thuoc Medical')
@section('content')
<section class="page-hero"><div class="container"><span class="eyebrow" data-content-keys="team_block_1_vi">{{ $site['team_block_1_vi'] }}</span><h1 data-content-keys="team_block_2_vi">{{ $site['team_block_2_vi'] }}</h1></div></section>
<section class="container section team-story">
    <img class="team-portrait" data-content-keys="team_portrait_image" src="{{ !empty($site['team_portrait_image']) ? asset('storage/'.$site['team_portrait_image']) : asset('images/hero-healthcare-team.png') }}" alt="{{ !empty($site['team_portrait_image']) ? 'Chân dung anh Thuộc' : ($locale === 'en' ? 'Hoang Thuoc Medical team' : 'Đội ngũ Hoàng Thuộc Medical') }}" loading="lazy">
    <div><span class="eyebrow dark" data-content-keys="team_block_3_vi">{{ $site['team_block_3_vi'] }}</span><h2 data-content-keys="team_block_4_vi">{{ $site['team_block_4_vi'] }}</h2>
    <p data-content-keys="team_block_5_vi">{{ $site['team_block_5_vi'] }}</p>
    <p data-content-keys="team_block_6_vi">{{ $site['team_block_6_vi'] }}</p>
    <p data-content-keys="team_block_7_vi">{{ $site['team_block_7_vi'] }}</p></div>
</section>
@if((string) ($site['team_group_enabled'] ?? '1') === '1')
<section class="container section team-group">
    <div class="team-group-copy">
        <span class="eyebrow dark" data-content-keys="team_group_eyebrow_vi">{{ $site['team_group_eyebrow_vi'] }}</span>
        <h2 data-content-keys="team_group_title_vi">{{ $site['team_group_title_vi'] }}</h2>
    @if(!empty($site['team_group_description_vi']))
        <p class="team-group-description" data-content-keys="team_group_description_vi">{{ $site['team_group_description_vi'] }}</p>
    @endif
    </div>
        <div class="team-group-gallery">
            <img class="team-group-image" data-content-keys="team_group_image" src="{{ !empty($site['team_group_image']) ? asset('storage/'.$site['team_group_image']) : asset('images/hero-healthcare-team.png') }}" alt="{{ $locale === 'en' ? 'Hoang Thuoc Medical team' : 'Đội ngũ Hoàng Thuộc Medical' }}" loading="lazy">
        </div>
</section>
@endif
<section class="journey"><div class="container"><span class="eyebrow dark" data-content-keys="team_block_8_vi">{{ $site['team_block_8_vi'] }}</span><h2 data-content-keys="team_block_9_vi">{{ $site['team_block_9_vi'] }}</h2>
<div class="timeline-scroll" tabindex="0" role="region" aria-label="{{ $locale === 'en' ? 'Development timeline, scroll horizontally to explore' : 'Hành trình phát triển, vuốt ngang để xem' }}"><div class="ecg-timeline"><svg viewBox="0 0 1000 100" preserveAspectRatio="none" aria-hidden="true"><path d="M0 55 H100 l12 -12 12 12 H165 l15 -25 15 55 18 -75 20 45 H300 l12 -12 12 12 H365 l15 -25 15 55 18 -75 20 45 H500 l12 -12 12 12 H565 l15 -25 15 55 18 -75 20 45 H700 l12 -12 12 12 H765 l15 -25 15 55 18 -75 20 45 H900 l12 -12 12 12 H1000" /></svg>
<ol>@foreach(collect(range(1, 5))->map(fn ($i) => $site['team_stage_'.$i.'_vi']) as $stage)<li><span></span><h3>{{ $stage }}</h3></li>@endforeach</ol></div></div>
</div></section>
@endsection
