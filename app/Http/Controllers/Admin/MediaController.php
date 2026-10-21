<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\News;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MediaController extends Controller
{
    private static function usedPaths(): \Illuminate\Support\Collection
    {
        return collect(SiteSetting::values())->values()
            ->merge(\App\Support\TeamProducts::all()->pluck('image_path'))
            ->merge(\App\Support\TeamProducts::all()->pluck('gallery_paths')->flatten())
            ->merge(Job::query()->whereNotNull('image_path')->pluck('image_path'))
            ->merge(News::query()->whereNotNull('cover_image')->pluck('cover_image'))
            ->filter()->unique();
    }

    public static function images(): \Illuminate\Support\Collection
    {
        $disk = Storage::disk('public');

        return collect($disk->allFiles())->filter(fn ($path) => preg_match('/\.(jpe?g|png|webp|gif)$/i', $path))
            ->map(fn ($path) => [
                'path' => $path,
                'folder' => dirname($path) === '.' ? '/' : dirname($path),
                'url' => $disk->url($path),
                'year' => preg_match('#^media/(\d{4})/#', $path, $matches) ? $matches[1] : date('Y', $disk->lastModified($path)),
            ])->sortByDesc('path')->values();
    }

    public function index(Request $request)
    {
        $images = self::images();
        $years = $images->pluck('year')->unique()->sortDesc()->values();
        $year = $request->query('year', '');
        $folder = $request->query('folder', '');

        return view('admin.media.index', [
            'groups' => $images->when($year, fn ($items) => $items->where('year', $year))
                ->when($folder, fn ($items) => $items->where('folder', $folder))->groupBy('folder')->sortKeys(),
            'folders' => $images->pluck('folder')->unique()->sort()->values(),
            'folder' => $folder,
            'years' => $years,
            'year' => $year,
            'usedPaths' => self::usedPaths(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['images' => ['required', 'array', 'max:20'], 'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120']]);
        foreach ($request->file('images') as $image) {
            $image->store('media/'.now()->timezone('Asia/Bangkok')->format('Y'), 'public');
        }

        return back()->with('success', 'Đã tải ảnh lên kho. Bạn có thể chọn ảnh trong phần nội dung trang chủ.');
    }

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'path' => ['required', 'string', Rule::in(self::images()->pluck('path')->all())],
        ]);
        if (self::usedPaths()->contains($data['path'])) {
            return back()->withErrors(['path' => 'Ảnh đang được sử dụng. Hãy thay ảnh trong nội dung, logo, tin tuyển dụng hoặc bài viết trước khi xóa.']);
        }
        if (! Storage::disk('public')->delete($data['path'])) {
            return back()->withErrors(['path' => 'Không thể xóa ảnh. Vui lòng thử lại.']);
        }

        return back()->with('success', 'Đã xóa ảnh khỏi kho.');
    }
}
