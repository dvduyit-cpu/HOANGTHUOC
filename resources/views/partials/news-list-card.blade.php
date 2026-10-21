<article class="news-card {{ $isLatest ? 'news-card-latest' : 'news-card-compact' }}">
    <a class="news-image-link" href="{{ route('news.show', $post->slug) }}" tabindex="-1" aria-hidden="true">
        <img src="{{ $post->cover_image ? asset('storage/'.$post->cover_image) : asset('images/hero-healthcare-team.png') }}" alt="" loading="lazy">
    </a>
    <div class="news-card-copy">
        @if($isLatest)<span class="news-latest-label">{{ $locale === 'en' ? 'LATEST NEWS' : 'BẢN TIN MỚI NHẤT' }}</span>@endif
        <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('d.m.Y') }}</time>
        <h3><a href="{{ route('news.show', $post->slug) }}">{{ $post->localized('title') }}</a></h3>
        <p>{{ $post->localized('excerpt') }}</p>
        <a class="link" href="{{ route('news.show', $post->slug) }}">{{ $locale === 'en' ? 'Read more' : 'Đọc thêm' }} →</a>
    </div>
</article>
