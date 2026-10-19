<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\View\View;

class JobController extends Controller
{
    public function show(string $slug): View
    {
        $job = Job::public()->where('slug', $slug)->firstOrFail();
        return view('jobs.show', compact('job'));
    }
}
