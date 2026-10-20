<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, ['vi', 'en'], true), 404);
        $request->session()->put('locale', $locale);

        $target = $request->string('redirect')->toString();
        $base = parse_url(route('home'));
        $parts = parse_url($target);
        if (! is_array($parts) || ! in_array($parts['scheme'] ?? '', ['http', 'https'], true)
            || ($parts['host'] ?? '') !== ($base['host'] ?? '')
            || ($parts['port'] ?? null) !== ($base['port'] ?? null)
            || isset($parts['user']) || isset($parts['pass'])
            || preg_match('#^/(admin|ngon-ngu)(/|$)#', $parts['path'] ?? '/')) {
            $target = route('home');
        }

        return redirect()->to($target);
    }
}
