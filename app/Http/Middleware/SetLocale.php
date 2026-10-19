<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Vietnamese is the source language; English is translated from the public page.
        app()->setLocale('vi');

        return $next($request);
    }
}
