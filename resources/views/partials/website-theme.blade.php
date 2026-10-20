@php
    $websiteColors = [];
    foreach (config('page-content.mau-sac') as $key => $field) {
        $candidate = $site[$key] ?? $field[2];
        $websiteColors[$key] = is_string($candidate) && preg_match('/^#[0-9a-fA-F]{6}$/', $candidate) ? $candidate : $field[2];
    }
@endphp
<style>
:root {
    --web-primary: {{ $websiteColors['web_primary_color'] }};
    --web-heading: {{ $websiteColors['web_heading_color'] }};
    --web-text: {{ $websiteColors['web_text_color'] }};
    --web-background: {{ $websiteColors['web_background_color'] }};
    --web-surface: {{ $websiteColors['web_surface_color'] }};
    --web-dark: {{ $websiteColors['web_dark_color'] }};
    --brand: var(--web-primary); --brand2: var(--web-primary); --blue: var(--web-primary);
    --ink: var(--web-heading); --navy: var(--web-dark); --muted: var(--web-text); --sky: var(--web-surface);
}
</style>
<link rel="stylesheet" href="{{ asset('css/website-theme.css') }}?v={{ filemtime(public_path('css/website-theme.css')) }}">
