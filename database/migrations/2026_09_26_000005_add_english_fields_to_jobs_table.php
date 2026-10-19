<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title');
            $table->string('location_en')->nullable()->after('location');
            $table->string('salary_en')->nullable()->after('salary');
            $table->string('summary_en', 500)->nullable()->after('summary');
            $table->text('description_en')->nullable()->after('description');
            $table->text('requirements_en')->nullable()->after('requirements');
            $table->text('benefits_en')->nullable()->after('benefits');
        });
    }
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) { $table->dropColumn(['title_en','location_en','salary_en','summary_en','description_en','requirements_en','benefits_en']); });
    }
};
