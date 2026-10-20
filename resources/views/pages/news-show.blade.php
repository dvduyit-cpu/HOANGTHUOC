@extends('layouts.app')
@section('title', $post->localized('title').' | '.($site['site_title'] ?? $site['company_name'] ?? 'Hoang Thuoc Medical'))
@section('meta_description', $post->localized('excerpt'))
@section('content')
<article class="container narrow section article-detail" aria-labelledby="article-title">
    <a class="back" href="{{ route('news') }}">← {{ $locale === 'en' ? 'All news' : 'Tất cả tin tức' }}</a>
    <header class="article-header">
        @if($post->published_at)<time class="eyebrow dark" datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('d.m.Y') }}</time>@endif
        <h1 id="article-title">{{ $post->localized('title') }}</h1>
        @if(filled($post->localized('excerpt')))<p class="article-lead">{{ $post->localized('excerpt') }}</p>@endif
    </header>
    @if($post->cover_image)<figure class="article-cover"><img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->localized('title') }}" fetchpriority="high"></figure>@endif
    <div class="article-content">{!! nl2br(e($post->localized('content'))) !!}</div>
    <footer class="article-end"><a class="back" href="{{ route('news') }}">← {{ $locale === 'en' ? 'All news' : 'Tất cả tin tức' }}</a></footer>
</article>
@endsection
