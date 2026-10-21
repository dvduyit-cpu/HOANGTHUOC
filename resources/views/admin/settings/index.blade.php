@extends('layouts.admin')
@section('title', 'Nội dung website')
@section('content')
<div class="page-head"><div><h1>Quản lý nội dung</h1><p class="muted">Chọn trang để chỉnh trực tiếp chữ và ảnh, kéo thả bố cục, lưu nháp rồi xuất bản.</p></div><a class="btn" href="{{ route('home') }}" target="_blank" rel="noopener">Xem website</a></div>
<div class="content-section-grid">@foreach($sections as $key => $item)<a class="content-section-card" href="{{ route('admin.content.edit', $key) }}"><span class="content-section-icon">{{ $loop->iteration }}</span><div><h2>{{ $item['title'] }}</h2><p>{{ $item['description'] }}</p><strong>Chỉnh sửa →</strong></div></a>@endforeach</div>
@endsection
