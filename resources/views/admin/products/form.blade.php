@extends('layouts.admin')
@section('title', empty($product['id']) ? 'Thêm sản phẩm' : 'Chỉnh sửa sản phẩm')
@section('content')
<div class="page-head"><div><a href="{{ route('admin.products.index') }}">← Danh sách sản phẩm</a><h1>@yield('title')</h1></div></div>
<form class="admin-settings" method="post" enctype="multipart/form-data" action="{{ empty($product['id']) ? route('admin.products.store') : route('admin.products.update', $product['id']) }}">@csrf @if(!empty($product['id'])) @method('PUT') @endif
<section class="settings-group"><div class="form-grid">
<label>Tên sản phẩm<input name="name" value="{{ old('name', $product['name'] ?? '') }}" maxlength="190" required></label>
<label>Tên tiếng Anh<input name="name_en" value="{{ old('name_en', $product['name_en'] ?? '') }}" maxlength="190"></label>
<label>Mô tả<textarea name="description" rows="5" maxlength="5000">{{ old('description', $product['description'] ?? '') }}</textarea></label>
<label>Mô tả tiếng Anh<textarea name="description_en" rows="5" maxlength="5000">{{ old('description_en', $product['description_en'] ?? '') }}</textarea></label>
<label>Ảnh từ kho<select name="image_path"><option value="">Không dùng ảnh</option>@foreach($images as $image)<option value="{{ $image['path'] }}" @selected(old('image_path', $product['image_path'] ?? '') === $image['path'])>{{ basename($image['path']) }}</option>@endforeach</select></label>
<label>Hoặc tải ảnh mới<input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"><small>Tối đa 5 MB. Ảnh tải mới được ưu tiên.</small></label>
<label class="span-2">Ảnh bổ sung trong slider
<input type="hidden" name="gallery_paths[]" value="">
<select name="gallery_paths[]" multiple size="6">
@foreach($images as $image)<option value="{{ $image['path'] }}" @selected(in_array($image['path'], old('gallery_paths', $product['gallery_paths'] ?? []), true))>{{ basename($image['path']) }}</option>@endforeach
</select><small>Các ảnh được chọn sẽ xuất hiện lần lượt sau ảnh chính. Bỏ chọn để gỡ ảnh khỏi slider.</small></label>
<label class="span-2">Tải nhiều ảnh vào slider<input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp,image/gif"><small>Có thể chọn cùng lúc 3, 4 hoặc nhiều ảnh; tối đa 20 ảnh mỗi lần, mỗi ảnh tối đa 5 MB.</small></label>
@if(!empty($product['image_path']))<img src="{{ asset('storage/'.$product['image_path']) }}" alt="Ảnh hiện tại" style="max-width:240px;height:160px;object-fit:contain">@endif
<label>Thứ tự hiển thị<input type="number" name="sort_order" min="0" max="99999" value="{{ old('sort_order', $product['sort_order'] ?? 0) }}" required><small>Số nhỏ hiển thị trước.</small></label>
<label>Trạng thái<select name="is_published"><option value="1" @selected((string) old('is_published', $product['is_published'] ?? 1) === '1')>Hiển thị</option><option value="0" @selected((string) old('is_published', $product['is_published'] ?? 1) === '0')>Ẩn</option></select></label>
</div></section><button class="btn" type="submit">Lưu sản phẩm</button></form>
@endsection
