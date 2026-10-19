<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\News;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'openPositions' => Job::public()->count(),
            'latestNews' => News::public()->latest('published_at')->limit(2)->get(),
        ]);
    }
}
