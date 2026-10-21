<?php

namespace App\Support;

class VisualEditor
{
    public static function blocks(string $section): array
    {
        return config('visual-editor.'.$section.'.blocks', []);
    }

    public static function layout(string $section, array $settings): array
    {
        $raw = $settings['page_layout_'.$section] ?? '[]';
        $saved = is_string($raw) ? json_decode($raw, true) : [];
        $blocks = self::blocks($section);
        $layout = [];
        foreach (is_array($saved) ? $saved : [] as $item) {
            if (is_array($item) && is_string($item['id'] ?? null) && isset($blocks[$item['id']]) && !isset($layout[$item['id']])) {
                $layout[$item['id']] = ['id' => $item['id'], 'visible' => ($item['visible'] ?? true) !== false];
            }
        }
        foreach ($blocks as $id => $block) {
            $layout[$id] ??= ['id' => $id, 'visible' => true];
        }
        return array_values($layout);
    }

    public static function group(string $section, string $key): string
    {
        foreach (self::blocks($section) as $id => $block) {
            foreach ($block['keys'] as $prefix) {
                if (str_starts_with($key, $prefix)) return $id;
            }
        }
        return 'general';
    }
}
