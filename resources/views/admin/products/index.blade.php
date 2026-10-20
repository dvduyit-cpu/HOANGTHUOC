@extends('layouts.admin')
@section('title', 'Sản phẩm trang giới thiệu')
@section('content')
<div class="page-head"><div><h1>Sản phẩm</h1><p class="muted">Sản phẩm hiển thị ở cuối trang Giới thiệu, theo thứ tự bạn chọn.</p></div><a class="btn" href="{{ route('admin.products.create') }}">Thêm sản phẩm</a></div>
<p><a href="{{ route('about') }}" target="_blank" rel="noopener">Xem trang giới thiệu ↗</a></p>
@forelse($products as $product)
<section class="settings-group"><div class="page-head"><div><h2>{{ $product['name'] }}</h2><p>Thứ tự: {{ $product['sort_order'] }} · {{ $product['is_published'] ? 'Đang hiển thị' : 'Đang ẩn' }}</p></div><a class="btn" href="{{ route('admin.products.edit', $product['id']) }}">Chỉnh sửa</a></div>
@if(!empty($product['image_path']))<img src="{{ asset('storage/'.$product['image_path']) }}" alt="{{ $product['name'] }}" style="width:160px;height:120px;object-fit:contain">@endif
<p>{{ $product['description'] ?? '' }}</p>
<form method="post" action="{{ route('admin.products.destroy', $product['id']) }}">@csrf @method('DELETE')<button type="submit" class="btn">Xóa sản phẩm</button></form></section>
@empty<div class="empty">Chưa có sản phẩm. Bấm “Thêm sản phẩm” để bắt đầu.</div>@endforelse
@endsection
