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

        return redirect()->to($request->string('redirect')->toString() ?: route('home'));
    }
}
