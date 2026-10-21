@extends('layouts.app')
@section('title', ($locale === 'en' ? 'News' : 'Tin tức').' | '.($site['site_title'] ?? $site['company_name'] ?? 'Hoang Thuoc Medical'))
@section('content')
@include('partials.section-banner', ['bannerClass' => 'news-hero', 'imageKey' => 'news_hero_image', 'eyebrowKey' => 'news_block_1_vi', 'titleKey' => 'news_block_2_vi'])
<section class="container news-intro"><p data-content-keys="news_block_3_vi">{{ $site['news_block_3_vi'] }}</p></section>
<section class="container section news-grid">@forelse($posts as $post)<article class="news-card"><div class="news-art {{ $loop->first ? 'art-one' : 'art-two' }}" @if($post->cover_image) style="background-image:url('{{ asset('storage/'.$post->cover_image) }}')" @endif></div><span>{{ $post->published_at?->format('d.m.Y') }}</span><h3>{{ $post->localized('title') }}</h3><p>{{ $post->localized('excerpt') }}</p><a class="link" href="{{ route('news.show', $post->slug) }}">{{ $locale === 'en' ? 'Read more' : 'Đọc thêm' }} →</a></article>@empty<div class="empty">{{ $locale === 'en' ? 'There are no published posts yet.' : 'Chưa có bài viết được đăng.' }}</div>@endforelse</section>
@endsection
