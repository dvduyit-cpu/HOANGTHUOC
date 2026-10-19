<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationController extends Controller
{
    public function index(): View
    {
        $applications = Application::with('job')->latest()->paginate(20);
        return view('admin.applications.index', compact('applications'));
    }

    public function show(Application $application): View
    {
        $application->load('job');
        return view('admin.applications.show', compact('application'));
    }

    public function update(Request $request, Application $application)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['new', 'reviewed', 'shortlisted', 'rejected'])],
        ]);
        $application->update($data);
        return back()->with('success', 'Đã cập nhật trạng thái hồ sơ.');
    }

    public function download(Application $application): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($application->cv_path), 404);
        return Storage::disk('local')->download($application->cv_path, $application->cv_original_name);
    }
}
