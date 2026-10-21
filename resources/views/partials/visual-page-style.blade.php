@php
    $visualSection = request()->routeIs('admin.content.preview') ? request()->route('section') : match (true) {
        request()->routeIs('home') => 'trang-chu', request()->routeIs('about') => 'gioi-thieu',
        request()->routeIs('team') => 'doi-ngu', request()->routeIs('jobs.index') => 'tuyen-dung',
        request()->routeIs('news') => 'tin-tuc', request()->routeIs('contact.create') => 'lien-he', default => '',
    };
    $visualBlocks = \App\Support\VisualEditor::blocks($visualSection);
    $visualLayout = \App\Support\VisualEditor::layout($visualSection, $site);
    $visualRoot = config('visual-editor.'.$visualSection.'.root', 'main');
@endphp
<style>
@if($visualBlocks)
    {{ $visualRoot }} { display: flex; flex-direction: column; }
    {{ $visualRoot }} > * { order: 100; }
    @foreach($visualLayout as $position => $item)
        {{ $visualRoot }} > {{ $visualBlocks[$item['id']]['selector'] }} { order: {{ $position }}; @if(!$item['visible']) display: none !important; @endif }
        @if($visualSection === 'tin-tuc' && $item['id'] === 'news' && !$item['visible'])
            main > .pagination { display: none !important; }
        @endif
    @endforeach
@endif
@foreach($site as $styleKey => $styleValue)
    @if(preg_match('/^([a-z0-9_]+)_font_size$/', $styleKey, $styleMatch) && filter_var($styleValue, FILTER_VALIDATE_INT) !== false && (int) $styleValue >= 8 && (int) $styleValue <= 96)
        [data-content-keys~="{{ $styleMatch[1] }}"] { font-size: {{ (int) $styleValue }}px !important; }
    @elseif(preg_match('/^([a-z0-9_]+)_text_color$/', $styleKey, $styleMatch) && is_string($styleValue) && preg_match('/^#[0-9a-fA-F]{6}$/', $styleValue))
        [data-content-keys~="{{ $styleMatch[1] }}"] { color: {{ $styleValue }} !important; }
    @endif
@endforeach
@foreach(\App\Support\VisualEditor::imageAdjustments($site) as $adjustment)
    @php($imageValues = $adjustment['values'])
    @php($imageTarget = $adjustment['target'])
    {{ $imageTarget['image'] }} {
        @if(isset($imageValues['fit'])) object-fit: {{ $imageValues['fit'] }} !important; @endif
        @if(isset($imageValues['x']) || isset($imageValues['y'])) object-position: {{ $imageValues['x'] ?? 50 }}% {{ $imageValues['y'] ?? 50 }}% !important; @endif
        @if(isset($imageValues['height']) && !isset($imageTarget['frame'])) height: {{ $imageValues['height'] }}px !important; max-height: none !important; aspect-ratio: auto !important; @endif
    }
    @if(isset($imageValues['height'], $imageTarget['frame']))
        {{ $imageTarget['frame'] }} { min-height: {{ $imageValues['height'] }}px !important; }
    @endif
    @if(isset($imageValues['overlay'], $imageTarget['overlay']))
        {{ $imageTarget['overlay'] }} { background: rgba(16,47,67,{{ $imageValues['overlay'] / 100 }}) !important; }
    @endif
@endforeach
</style>
