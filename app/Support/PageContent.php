<?php

namespace App\Support;

class PageContent
{
    public static function defaults(): array
    {
        $values = config('medical-content', []);
        foreach (config('page-content', []) as $fields) {
            foreach ($fields as $key => $field) {
                $values[$key] = $field[2];
            }
        }

        return array_replace($values, config('english-content', []));
    }

    public static function localized(array $saved): array
    {
        $values = array_replace(self::defaults(), $saved);
        if (app()->getLocale() === 'en') {
            foreach ($values as $key => $value) {
                if (str_ends_with($key, '_vi')) {
                    $englishKey = substr($key, 0, -3).'_en';
                    $values[$key] = filled($values[$englishKey] ?? null) ? $values[$englishKey] : $value;
                    $values[$englishKey] = $values[$key];
                }
            }
        }

        return $values;
    }
}
