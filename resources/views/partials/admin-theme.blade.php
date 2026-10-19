@php
    $theme = preg_match('/^#[0-9a-fA-F]{6}$/', $site['admin_color'] ?? '') ? $site['admin_color'] : '#2563eb';
    $fonts = ['system' => '"Segoe UI", system-ui, sans-serif', 'arial' => 'Arial, sans-serif', 'serif' => 'Georgia, "Times New Roman", serif'];
    $font = $fonts[$site['admin_font'] ?? 'system'] ?? $fonts['system'];
    $fontSize = max(14, min(20, (int) ($site['admin_font_size'] ?? 16)));
    $channels = array_map(fn ($value) => $value / 255, sscanf($theme, '#%02x%02x%02x'));
    $linear = array_map(fn ($value) => $value <= 0.04045 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4, $channels);
    $luminance = $linear[0] * 0.2126 + $linear[1] * 0.7152 + $linear[2] * 0.0722;
@endphp
<style>:root { --primary: {{ $theme }}; --on-primary: {{ $luminance > 0.179 ? '#18243a' : '#ffffff' }}; --app-font: {!! $font !!}; --font-scale: {{ $fontSize / 16 }}; }</style>
