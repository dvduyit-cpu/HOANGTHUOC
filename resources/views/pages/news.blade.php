@extends('layouts.app')
@section('title', ($locale === 'en' ? 'News' : 'Tin tức').' | '.($site['site_title'] ?? $site['company_name'] ?? 'Hoang Thuoc Medical'))
@section('content')
@include('partials.section-banner', ['bannerClass' => 'news-hero', 'imageKey' => 'news_hero_image', 'eyebrowKey' => 'news_block_1_vi', 'titleKey' => 'news_block_2_vi'])
<section class="container news-intro"><p data-content-keys="news_block_3_vi">{{ $site['news_block_3_vi'] }}</p></section>
<section class="container section news-grid news-editorial-grid">
    @forelse($posts as $post)
        @php($isLatest = $posts->currentPage() === 1 && $loop->first)
        @include('partials.news-list-card', ['isLatest' => $isLatest])
    @empty
        <div class="empty">{{ $locale === 'en' ? 'There are no published posts yet.' : 'Chưa có bài viết được đăng.' }}</div>
    @endforelse
</section>
<div class="container pagination">{{ $posts->links() }}</div>
@endsection
