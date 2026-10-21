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
    $pageAlignment = $site['content_alignment_'.$contentSection] ?? 'inherit';
@endphp
<style>
@if(in_array($pageAlignment, $validAlignments, true))
    main, main :is(h1,h2,h3,h4,p,.eyebrow,.hp-kicker,.hp-value-number,.ecg-timeline li) { text-align: {{ $pageAlignment }}; }
@endif
@foreach($site as $alignmentKey => $alignmentValue)
    @if(str_ends_with($alignmentKey, '_alignment') && is_string($alignmentValue) && in_array($alignmentValue, $validAlignments, true))
        @php($textKey = substr($alignmentKey, 0, -10))
        @if(preg_match('/^[a-z0-9_]+$/', $textKey))
            [data-content-keys~="{{ $textKey }}"] { text-align: {{ $alignmentValue }}; }
        @endif
    @endif
@endforeach
</style>
