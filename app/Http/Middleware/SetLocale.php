<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', 'vi');
        app()->setLocale(! $request->is('admin', 'admin/*') && in_array($locale, ['vi', 'en'], true) ? $locale : 'vi');

        return $next($request);
    }
}
