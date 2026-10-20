<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\TeamProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index', ['products' => TeamProducts::all()]);
    }

    public function create()
    {
        return view('admin.products.form', ['product' => [], 'images' => MediaController::images()]);
    }

    public function edit(string $product)
    {
        return view('admin.products.form', ['product' => TeamProducts::find($product), 'images' => MediaController::images()]);
    }

    public function store(Request $request)
    {
        return $this->save($request, (string) Str::uuid());
    }

    public function update(Request $request, string $product)
    {
        TeamProducts::find($product);

        return $this->save($request, $product);
    }

    private function save(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'name_en' => ['nullable', 'string', 'max:190'],
            'description' => ['nullable', 'string', 'max:5000'],
            'description_en' => ['nullable', 'string', 'max:5000'],
            'image_path' => ['nullable', 'string', Rule::in(MediaController::images()->pluck('path')->all())],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:99999'],
            'is_published' => ['required', 'boolean'],
        ]);
        unset($data['image']);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('media/'.now()->timezone('Asia/Bangkok')->format('Y'), 'public');
        }
        SiteSetting::putMany([TeamProducts::PREFIX.$id => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)]);

        return redirect()->route('admin.products.index')->with('success', 'Đã lưu sản phẩm.');
    }

    public function destroy(string $product)
    {
        TeamProducts::find($product);
        SiteSetting::where('key', TeamProducts::PREFIX.$product)->delete();

        return back()->with('success', 'Đã xóa sản phẩm. Ảnh vẫn được giữ trong kho.');
    }
}
