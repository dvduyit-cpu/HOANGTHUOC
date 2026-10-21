<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->longText('value')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Refuse a rollback that could truncate saved drafts or version history.
        DB::table('site_settings')->select('id', 'value')->chunkById(100, function ($rows) {
            foreach ($rows as $row) {
                if (strlen($row->value ?? '') > 65535) {
                    throw new RuntimeException('Cannot shrink site_settings.value: saved data exceeds the TEXT limit.');
                }
            }
        });
        Schema::table('site_settings', function (Blueprint $table) {
            $table->text('value')->nullable()->change();
        });
    }
};
