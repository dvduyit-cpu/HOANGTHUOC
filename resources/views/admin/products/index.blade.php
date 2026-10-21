@extends('layouts.admin')
@section('title', 'Slider Giải pháp y khoa')
@section('content')
<div class="page-head"><div><h1>Slider Giải pháp y khoa</h1><p class="muted">Quản lý ảnh slider ở cuối trang Giới thiệu. Mỗi ảnh được hiển thị lớn, lần lượt theo thứ tự bạn chọn.</p></div><a class="btn" href="{{ route('admin.products.create') }}">Thêm sản phẩm / nhóm ảnh</a></div>
<p><a href="{{ route('about') }}" target="_blank" rel="noopener">Xem trang giới thiệu ↗</a></p>
<form class="admin-settings" method="post" action="{{ route('admin.content.update', 'gioi-thieu') }}">
@csrf @method('PUT')
<section class="settings-group"><h2>Kiểu hiển thị</h2>
<label>Giải pháp y khoa @include('admin.content.product-layout-field', ['key' => 'medical_products_layout'])</label>
<p class="muted">Chọn 3 sản phẩm để xem cạnh nhau trên máy tính. Trên điện thoại có thể vuốt ngang để xem thêm.</p>
<button type="submit" class="btn">Lưu kiểu hiển thị</button></section>
</form>
<form class="admin-settings" method="post" enctype="multipart/form-data" action="{{ route('admin.products.store') }}">
@csrf
<section class="settings-group"><h2>Thêm ảnh vào slider</h2><p class="muted">Chọn cùng lúc 3, 4 hoặc nhiều ảnh. Ảnh mới sẽ được thêm vào slider sau khi lưu.</p>
<div class="form-grid">
<label>Tên nhóm ảnh<input name="name" value="{{ old('name', 'Giải pháp y khoa') }}" maxlength="190" required></label>
<label>Thứ tự nhóm ảnh<input type="number" name="sort_order" min="0" max="99999" value="{{ old('sort_order', min(99999, (int) ($products->max('sort_order') ?? -1) + 1)) }}" required></label>
<label class="span-2">Chọn ảnh slider<input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp,image/gif" required><small>Tối đa 20 ảnh mỗi lần, mỗi ảnh tối đa 5 MB. Ảnh được giữ nguyên tỉ lệ, không cắt.</small></label>
</div><input type="hidden" name="is_published" value="1"><button class="btn" type="submit">Thêm ảnh vào slider</button>
</section></form>
@if($errors->any())<div class="alert error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@forelse($products as $product)
<section class="settings-group"><div class="page-head"><div><h2>{{ $product['name'] }}</h2><p>Thứ tự: {{ $product['sort_order'] }} · {{ $product['is_published'] ? 'Đang hiển thị' : 'Đang ẩn' }}</p></div><a class="btn" href="{{ route('admin.products.edit', $product['id']) }}">Chỉnh sửa</a></div>
@if(!empty($product['image_path']))<img src="{{ asset('storage/'.$product['image_path']) }}" alt="{{ $product['name'] }}" style="width:160px;height:120px;object-fit:contain">@endif
@foreach($product['gallery_paths'] ?? [] as $imagePath)<img src="{{ asset('storage/'.$imagePath) }}" alt="{{ $product['name'] }} — ảnh {{ $loop->iteration }}" loading="lazy" style="width:160px;height:120px;object-fit:contain">@endforeach
<p class="muted">Chọn “Chỉnh sửa” để thêm, thay hoặc bỏ chọn từng ảnh; đổi thứ tự và bật / tắt hiển thị nhóm ảnh.</p>
<p>{{ $product['description'] ?? '' }}</p>
<form method="post" action="{{ route('admin.products.destroy', $product['id']) }}">@csrf @method('DELETE')<button type="submit" class="btn">Gỡ nhóm ảnh khỏi slider</button></form></section>
@empty<div class="empty">Chưa có sản phẩm. Bấm “Thêm sản phẩm” để bắt đầu.</div>@endforelse
@endsection
