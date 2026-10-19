@extends('layouts.admin')
@section('title', 'Quản lý nội dung')
@section('content')
<div class="page-head"><div><h1>Quản lý nội dung</h1><p class="muted">Mỗi phần của website được tách thành một trang riêng để dễ chỉnh sửa.</p></div><a class="btn" href="{{ route('home') }}" target="_blank">Xem website</a></div>
<div class="content-section-grid">@foreach($sections as $key => $item)<a class="content-section-card" href="{{ route('admin.content.edit', $key) }}"><span class="content-section-icon">{{ $loop->iteration }}</span><div><h2>{{ $item['title'] }}</h2><p>{{ $item['description'] }}</p><strong>Chỉnh sửa →</strong></div></a>@endforeach</div>
@endsection
