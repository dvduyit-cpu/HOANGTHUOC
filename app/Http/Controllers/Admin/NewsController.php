<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View { return view('admin.news.index', ['posts' => News::latest()->paginate(15)]); }
    public function create(): View { return view('admin.news.create', ['post' => new News()]); }
    public function edit(News $news): View { return view('admin.news.edit', ['post' => $news]); }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->slug($data['title_vi']);
        $data['published_at'] = $data['status'] === 'published' ? now() : null;
        if ($request->hasFile('cover_image')) $data['cover_image'] = $request->file('cover_image')->store('news', 'public');
        News::create($data);
        return redirect()->route('admin.news.index')->with('success', 'Đã tạo bài viết.');
    }

    public function update(Request $request, News $news): RedirectResponse
    {
        $data = $this->validated($request);
        if ($news->title_vi !== $data['title_vi']) $data['slug'] = $this->slug($data['title_vi'], $news->id);
        if ($data['status'] === 'published' && ! $news->published_at) $data['published_at'] = now();
        if ($request->hasFile('cover_image')) {
            if ($news->cover_image) Storage::disk('public')->delete($news->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('news', 'public');
        }
        $news->update($data);
        return redirect()->route('admin.news.index')->with('success', 'Đã cập nhật bài viết.');
    }

    public function destroy(News $news): RedirectResponse
    {
        if ($news->cover_image) Storage::disk('public')->delete($news->cover_image);
        $news->delete();
        return back()->with('success', 'Đã xóa bài viết.');
    }

    private function validated(Request $request): array
    {
        return $request->validate(['title_vi'=>['required','string','max:190'],'title_en'=>['nullable','string','max:190'],'excerpt_vi'=>['required','string','max:600'],'excerpt_en'=>['nullable','string','max:600'],'content_vi'=>['required','string','max:30000'],'content_en'=>['nullable','string','max:30000'],'status'=>['required','in:draft,published'],'cover_image'=>['nullable',File::image()->max('4mb')]]);
    }

    private function slug(string $title, ?int $ignore = null): string
    {
        $base = Str::slug($title) ?: 'tin-tuc'; $slug = $base; $number = 2;
        while (News::where('slug', $slug)->when($ignore, fn($q) => $q->where('id','!=',$ignore))->exists()) $slug = $base.'-'.$number++;
        return $slug;
    }
}
