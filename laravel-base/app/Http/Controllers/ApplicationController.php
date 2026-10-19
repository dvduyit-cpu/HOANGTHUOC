<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationReceived;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\File;
use Throwable;

class ApplicationController extends Controller
{
    public function store(Request $request, string $slug): RedirectResponse
    {
        $job = Job::public()->where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['required', 'string', 'max:30'],
            'cover_letter' => ['nullable', 'string', 'max:5000'],
            'cv' => ['required', File::types(['pdf', 'doc', 'docx'])->max('5mb')],
        ], [
            'cv.required' => 'Vui lòng chọn CV.',
            'cv.max' => 'CV không được vượt quá 5MB.',
        ]);

        $file = $request->file('cv');
        $cvPath = $file->store('cvs', 'local');

        $application = Application::create([
            'job_id' => $job->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'cover_letter' => $validated['cover_letter'] ?? null,
            'cv_path' => $cvPath,
            'cv_original_name' => $file->getClientOriginalName(),
            'status' => 'new',
        ]);

        try {
            Mail::to($job->contact_email ?: config('recruitment.hr_email'))
                ->send(new ApplicationReceived($application));
            $application->update(['email_sent_at' => now()]);
        } catch (Throwable $e) {
            Log::error('Không thể gửi email CV', [
                'application_id' => $application->id,
                'message' => $e->getMessage(),
            ]);
        }

        return back()->with('success', 'Hồ sơ của bạn đã được tiếp nhận. Cảm ơn bạn đã ứng tuyển!');
    }
}
