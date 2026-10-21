@php
    $contentSection = request()->routeIs('admin.content.preview') ? request()->route('section') : match (true) {
        request()->routeIs('home') => 'trang-chu',
        request()->routeIs('about') => 'gioi-thieu',
        request()->routeIs('team') => 'doi-ngu',
        request()->routeIs('jobs.*') => 'tuyen-dung',
        request()->routeIs('news*') => 'tin-tuc',
        request()->routeIs('contact.*') => 'lien-he',
        default => '',
    };
    $validAlignments = ['left', 'center', 'right', 'justify'];
    $pageAlignment = $site['content_alignment_'.$contentSection] ?? 'justify';
    if ($pageAlignment === 'inherit' || $pageAlignment === '') {
        $pageAlignment = 'justify';
    }
@endphp
<style>
    :where(main p, main .prose, main .article-content, .footer p) {
        text-align: justify;
        text-justify: inter-word;
        text-wrap: wrap;
    }
@if(in_array($pageAlignment, $validAlignments, true))
    main, main :is(p,.eyebrow,.hp-kicker,.hp-value-number,.ecg-timeline li) { text-align: {{ $pageAlignment }}; }
    main :is(h1,h2,h3,h4,h5,h6) { text-align: {{ $pageAlignment === 'justify' ? 'left' : $pageAlignment }}; }
    .hp-final :is(h2,h3), .team-product-card h3, .ecg-timeline h3 { text-align: center; }
@endif
@foreach($site as $alignmentKey => $alignmentValue)
    @if(str_ends_with($alignmentKey, '_alignment') && is_string($alignmentValue) && in_array($alignmentValue, $validAlignments, true))
        @php($textKey = substr($alignmentKey, 0, -10))
        @if(preg_match('/^[a-z0-9_]+$/', $textKey))
            [data-content-keys~="{{ $textKey }}"] { text-align: {{ $alignmentValue }}; }
            @if($alignmentValue === 'justify')
                :is(h1,h2,h3,h4,h5,h6)[data-content-keys~="{{ $textKey }}"] { text-align: left; }
            @endif
        @endif
    @endif
@endforeach
    #about-story-heading { text-align: left; }
</style>
