<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'jobCount' => Job::count(),
            'publishedCount' => Job::where('status', 'published')->count(),
            'applicationCount' => Application::count(),
            'userCount' => User::count(),
        ]);
    }
}
