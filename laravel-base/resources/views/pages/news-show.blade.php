@extends('layouts.app')
@section('title', $post->localized('title').' | '.($site['company_name'] ?? 'Hoang Thuoc Medical'))
@section('content')
<article class="container narrow section article-detail"><a class="back" href="{{ route('news') }}">← {{ $locale === 'en' ? 'All news' : 'Tất cả tin tức' }}</a><span class="eyebrow dark">{{ $post->published_at?->format('d.m.Y') }}</span><h1>{{ $post->localized('title') }}</h1><p class="article-lead">{{ $post->localized('excerpt') }}</p>@if($post->cover_image)<img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->localized('title') }}">@endif<div class="article-content">{!! nl2br(e($post->localized('content'))) !!}</div></article>
@endsection
