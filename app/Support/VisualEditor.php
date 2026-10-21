<?php

namespace App\Support;

class VisualEditor
{
    public static function imageTargets(): array
    {
        return [
            'home_hero_image' => ['image' => '.hp-hero-image', 'frame' => '.hp-hero', 'overlay' => '.hp-hero-shade'],
            'home_process_image' => ['image' => '#cung-dong-hanh > .hp-section-background', 'frame' => '#cung-dong-hanh', 'overlay' => '#cung-dong-hanh:before'],
            'culture_image' => ['image' => '.culture-background', 'frame' => '.hp-culture', 'overlay' => '.hp-culture-photo:before'],
            'home_updates_image' => ['image' => '.hp-updates > .hp-section-background', 'frame' => '.hp-updates', 'overlay' => '.hp-updates:before'],
            'home_journey_background_image' => ['image' => '.hp-journey-background', 'frame' => '.hp-process', 'overlay' => '.hp-process-photo:before'],
            'about_hero_image' => ['image' => '.page-hero-about > .editable-hero-image', 'frame' => '.page-hero-about', 'overlay' => '.page-hero-about:before'],
            'about_story_image' => ['image' => '.about-story-image'],
            'about_team_background_image' => ['image' => '.team-invite-background', 'frame' => '.team-invite', 'overlay' => '.team-invite-photo:before'],
            'team_portrait_image' => ['image' => '.team-portrait'],
            'team_group_image' => ['image' => '.team-group-image'],
            'careers_hero_image' => ['image' => '.careers-hero > .editable-hero-image', 'frame' => '.careers-hero', 'overlay' => '.careers-hero:before'],
            'news_hero_image' => ['image' => '.news-hero > .editable-hero-image', 'frame' => '.news-hero', 'overlay' => '.news-hero:before'],
            'contact_hero_image' => ['image' => '.contact-hero > .editable-hero-image', 'frame' => '.contact-hero', 'overlay' => '.contact-hero:before'],
        ];
    }

    public static function imageAdjustments(array $settings): array
    {
        $adjustments = [];
        foreach (self::imageTargets() as $key => $target) {
            $values = [];
            $fit = $settings[$key.'_image_fit'] ?? '';
            if (in_array($fit, ['cover', 'contain'], true)) $values['fit'] = $fit;
            foreach (['x' => [0, 100], 'y' => [0, 100], 'height' => [120, 1200], 'overlay' => [0, 90]] as $property => [$min, $max]) {
                $value = $settings[$key.'_image_'.$property] ?? null;
                if (filter_var($value, FILTER_VALIDATE_INT) !== false && (int) $value >= $min && (int) $value <= $max) $values[$property] = (int) $value;
            }
            if ($values) $adjustments[$key] = ['target' => $target, 'values' => $values];
        }
        return $adjustments;
    }

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
