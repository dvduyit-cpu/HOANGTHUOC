<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function values(): array
    {
        if (! Schema::hasTable('site_settings')) {
            return [];
        }

        return static::query()->pluck('value', 'key')->all();
    }

    public static function putMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        }
    }
}
