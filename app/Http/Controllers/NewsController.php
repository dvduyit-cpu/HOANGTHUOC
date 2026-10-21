<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        return view('pages.news', ['posts' => News::public()->latest('published_at')->latest('id')->paginate(9)]);
    }

    public function show(string $slug): View
    {
        return view('pages.news-show', ['post' => News::public()->where('slug', $slug)->firstOrFail()]);
    }
}
