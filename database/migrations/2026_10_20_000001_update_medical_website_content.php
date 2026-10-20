<?php

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach (config('medical-content') as $key => $value) {
            SiteSetting::query()->where('key', $key)->update(['value' => $value]);
        }
    }

    public function down(): void
    {
        // Editorial content is retained when rolling back schema migrations.
    }
};
