@extends('layouts.admin')
@section('title', $sectionData['title'])
@section('content')
<div class="page-head"><div><a class="back" href="{{ route('admin.content.index') }}">← Quản lý nội dung</a><h1>{{ $sectionData['title'] }}</h1><p class="muted">{{ $sectionData['description'] }}</p></div><a class="btn" href="{{ route($sectionData['route']) }}" target="_blank" rel="noopener">Xem trang này</a></div>
<p class="muted">Nhập tiếng Việt và bản tiếng Anh tương ứng. Khách chọn EN / VI để đổi ngôn ngữ ngay trên website. Nội dung mới cần có bản tiếng Anh để hiển thị đầy đủ.</p>
@if($section === 'gioi-thieu')<p><a class="btn" href="{{ route('admin.products.index') }}">Quản lý sản phẩm cuối trang giới thiệu →</a></p>@endif
<link rel="stylesheet" href="{{ asset('css/content-preview.css') }}">
<div class="content-editor">
<form id="content-editor-form" class="admin-settings" data-preview-url="{{ route('admin.content.preview', $section) }}" method="post" enctype="multipart/form-data" action="{{ route('admin.content.update', $section) }}">@csrf @method('PUT')
<section class="settings-group settings-section"><div class="form-grid">@foreach($fields as $key => $field)<label class="{{ $field[1] === 'textarea' ? 'span-2' : '' }}">{{ $field[0] }}@if($field[1] === 'image')<select name="settings[{{ $key }}]"><option value="">Chưa chọn ảnh</option>@foreach($images as $image)<option value="{{ $image['path'] }}" @selected(old('settings.'.$key, $settings[$key] ?? '') === $image['path'])>{{ basename($image['path']) }}</option>@endforeach</select>@php($chosenImage = old('settings.'.$key, $settings[$key] ?? ''))
@if($chosenImage)<img src="{{ asset('storage/'.$chosenImage) }}" alt="Ảnh đang chọn" style="width:100%;max-width:260px;height:150px;object-fit:cover;margin:10px 0">@endif
<span>Tải ảnh mới (tối đa 5 MB)</span><input type="file" name="uploads[{{ $key }}]" accept="image/jpeg,image/png,image/webp,image/gif"><small>Chọn ảnh trong kho hoặc tải ảnh mới. Ảnh tải mới sẽ được ưu tiên. Để trống lựa chọn để dùng ảnh mặc định / ẩn ảnh.</small>@elseif($field[1] === 'textarea')<textarea name="settings[{{ $key }}]" rows="4">{{ old('settings.'.$key, $settings[$key] ?? '') }}</textarea>@else<input type="{{ $field[1] }}" @if($field[1] === 'number') min="8" max="96" step="1" @endif name="settings[{{ $key }}]" value="{{ old('settings.'.$key, $settings[$key] ?? '') }}">@endif</label>@endforeach</div></section>
@if($errors->any())<div class="alert error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<button class="btn" type="submit">Lưu thay đổi</button></form>
<aside class="content-preview" aria-label="Xem trước website">
    <div class="preview-toolbar"><strong>Xem trước website</strong><select id="preview-locale" aria-label="Ngôn ngữ xem trước"><option value="vi">Tiếng Việt</option><option value="en">English</option></select><select id="preview-device" aria-label="Kích thước xem trước"><option value="desktop">Máy tính</option><option value="mobile">Điện thoại</option></select></div>
    <p id="preview-status" role="status">Đang tải bản xem trước…</p>
    <small>Nội dung chỉ được công khai khi bấm Lưu thay đổi. Ảnh tải mới hiển thị sau khi lưu.</small>
    <div class="preview-viewport"><iframe id="content-preview-frame" title="Bản xem trước website" sandbox="allow-same-origin" src="about:blank"></iframe></div>
</aside>
</div>
<script src="{{ asset('js/content-preview.js') }}" defer></script>
@endsection
