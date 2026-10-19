<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title_vi');
            $table->string('title_en')->nullable();
            $table->string('slug')->unique();
            $table->text('excerpt_vi');
            $table->text('excerpt_en')->nullable();
            $table->longText('content_vi');
            $table->longText('content_en')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('news'); Schema::dropIfExists('site_settings'); }
};
