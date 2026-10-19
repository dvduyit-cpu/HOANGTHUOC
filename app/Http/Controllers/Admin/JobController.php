<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;

class JobController extends Controller
{
    public function index(): View
    {
        $jobs = Job::with('creator')->latest()->paginate(15);
        return view('admin.jobs.index', compact('jobs'));
    }

    public function create(): View
    {
        return view('admin.jobs.create', ['job' => new Job()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['created_by'] = $request->user()->id;
        $data['published_at'] = $data['status'] === 'published' ? now() : null;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('jobs', 'public');
        }

        Job::create($data);
        return redirect()->route('admin.jobs.index')->with('success', 'Đã tạo tin tuyển dụng.');
    }

    public function edit(Job $job): View
    {
        return view('admin.jobs.edit', compact('job'));
    }

    public function update(Request $request, Job $job): RedirectResponse
    {
        $data = $this->validated($request);

        if ($job->title !== $data['title']) {
            $data['slug'] = $this->uniqueSlug($data['title'], $job->id);
        }

        if ($data['status'] === 'published' && ! $job->published_at) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('image')) {
            if ($job->image_path) {
                Storage::disk('public')->delete($job->image_path);
            }
            $data['image_path'] = $request->file('image')->store('jobs', 'public');
        }

        $job->update($data);
        return redirect()->route('admin.jobs.index')->with('success', 'Đã cập nhật tin tuyển dụng.');
    }

    public function destroy(Job $job): RedirectResponse
    {
        if ($job->image_path) {
            Storage::disk('public')->delete($job->image_path);
        }
        $job->delete();
        return back()->with('success', 'Đã xóa tin tuyển dụng.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'location' => ['required', 'string', 'max:190'],
            'employment_type' => ['required', Rule::in(['full-time', 'part-time', 'contract', 'internship'])],
            'salary' => ['nullable', 'string', 'max:190'],
            'summary' => ['required', 'string', 'max:500'],
            'description' => ['required', 'string', 'max:20000'],
            'requirements' => ['nullable', 'string', 'max:15000'],
            'benefits' => ['nullable', 'string', 'max:15000'],
            'contact_email' => ['nullable', 'email:rfc', 'max:190'],
            'status' => ['required', Rule::in(['draft', 'published', 'closed'])],
            'expires_at' => ['nullable', 'date'],
            'image' => ['nullable', File::image()->max('4mb')],
        ]);
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'viec-lam';
        $slug = $base;
        $i = 2;

        while (Job::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
