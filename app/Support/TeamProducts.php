<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Collection;

class TeamProducts
{
    public const PREFIX = 'team_product_item_';

    public static function all(): Collection
    {
        return collect(SiteSetting::values())->filter(fn ($value, $key) => str_starts_with($key, self::PREFIX))
            ->map(function ($value, $key) {
                $product = json_decode($value, true);
                return is_array($product) ? array_merge($product, ['id' => substr($key, strlen(self::PREFIX))]) : null;
            })->filter()->sortBy([['sort_order', 'asc'], ['name', 'asc']])->values();
    }

    public static function find(string $id): array
    {
        $product = self::all()->firstWhere('id', $id);
        abort_unless($product, 404);

        return $product;
    }
}
