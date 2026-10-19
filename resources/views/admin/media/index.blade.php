@extends('layouts.admin')
@section('title', 'Kho hình ảnh')
@section('content')
<div class="page-head"><div><h1>Kho hình ảnh</h1><p class="muted">Kho chung gồm logo, ảnh có sẵn trên website và ảnh đã tải lên, được phân loại theo năm. Bạn có thể chọn lại ảnh để làm slider trang chủ.</p></div><a class="btn" href="{{ route('admin.content.edit', 'trang-chu') }}">Chỉnh sửa ảnh slider</a></div>
<form class="settings-group" method="post" action="{{ route('admin.media.store') }}" enctype="multipart/form-data">
    @csrf
    <label>Tải ảnh lên (JPG, PNG, WebP, GIF; tối đa 5 MB/ảnh, 20 ảnh/lần)<input type="file" name="images[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple required></label>
    <button class="btn" type="submit">Tải lên kho ảnh</button>
</form>
<form method="get" class="form-grid"><label>Lọc theo năm <select name="year"><option value="">Tất cả các năm</option>@foreach($years as $value)<option value="{{ $value }}" @selected((string) $year === (string) $value)>{{ $value }}</option>@endforeach</select></label><label>Thư mục <select name="folder"><option value="">Tất cả thư mục</option>@foreach($folders as $value)<option value="{{ $value }}" @selected($folder === $value)>{{ $value }}</option>@endforeach</select></label><div><button class="btn" type="submit">Lọc ảnh</button></div></form>
@forelse($groups as $directory => $images)
<details class="settings-group" open>
<summary><strong>📁 {{ $directory }}</strong> · {{ $images->count() }} ảnh</summary>
<div class="media-grid">
@foreach($images as $image)
    <figure class="media-card"><a href="{{ $image['url'] }}" target="_blank" rel="noopener"><img src="{{ $image['url'] }}" alt="{{ basename($image['path']) }}" loading="lazy"></a><figcaption>{{ basename($image['path']) }}<br><small>Năm {{ $image['year'] }}</small></figcaption>
    @if($usedPaths->contains($image['path']))
        <small>Đang sử dụng — thay ảnh trước khi xóa.</small>
    @else
        <form method="post" action="{{ route('admin.media.destroy') }}" data-confirm-delete-media>
            @csrf @method('DELETE')
            <input type="hidden" name="path" value="{{ $image['path'] }}">
            <button type="submit" class="ghost-btn">Xóa ảnh</button>
        </form>
    @endif
    </figure>
@endforeach
</div>
</details>
@empty<p>Chưa có ảnh phù hợp với bộ lọc.</p>@endforelse
@endsection
