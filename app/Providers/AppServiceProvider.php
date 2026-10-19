<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! class_exists(\finfo::class)) {
            // Local disk construction must also work on hosting without fileinfo.
            // This fallback is for storage metadata, not upload validation.
            \Illuminate\Support\Facades\Storage::extend('local', fn ($app, $config) => \App\Support\HostingFilesystem::make($config));
        }

        RateLimiter::for('admin-login', function (Request $request) {
            return Limit::perMinute(5)->by(Str::transliterate(Str::lower((string) $request->input('email')).'|'.$request->ip()));
        });

        View::composer('*', function ($view): void {
            $view->with('site', SiteSetting::values());
            $view->with('locale', app()->getLocale());
        });
    }
}
