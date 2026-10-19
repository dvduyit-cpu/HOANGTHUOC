@props(['name' => 'layers', 'size' => 20])
@php
    $paths = [
        'dashboard' => 'M3 3h7v7H3z M14 3h7v7h-7z M3 14h7v7H3z M14 14h7v7h-7z',
        'briefcase' => 'M9 7V4h6v3 M3 7h18v13H3z M3 12l9 3 9-3 M12 12v5',
        'news' => 'M4 3h16v18H4z M8 7h8 M8 11h8 M8 15h3 M8 18h8',
        'layers' => 'm12 3 10 5-10 5L2 8z M2 12l10 5 10-5 M2 16l10 5 10-5',
        'folder' => 'M3 5h7l2 3h9v12H3z',
        'users' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M16 3a4 4 0 0 1 0 8 M22 21v-2a4 4 0 0 0-3-3.87 M13 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0',
        'settings' => 'M4 7h16 M4 17h16 M8 4v6 M16 14v6',
        'globe' => 'M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0 M3 12h18 M12 3c5 5 5 13 0 18-5-5-5-13 0-18',
        'menu' => 'M4 6h16 M4 12h16 M4 18h16',
        'home' => 'm3 10 9-7 9 7 M5 9v12h14V9 M9 21v-8h6v8',
        'logout' => 'M9 21H3V3h6 M13 8l4 4-4 4 M7 12h14',
        'check' => 'm5 12 4 4L19 6',
    ];
@endphp
<svg {{ $attributes }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $paths[$name] ?? $paths['layers'] }}" /></svg>
