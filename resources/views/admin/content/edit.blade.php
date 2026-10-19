@extends('layouts.admin')
@section('title', $sectionData['title'])
@section('content')
<div class="page-head"><div><a class="back" href="{{ route('admin.content.index') }}">← Quản lý nội dung</a><h1>{{ $sectionData['title'] }}</h1><p class="muted">{{ $sectionData['description'] }}</p></div><a class="btn" href="{{ route('home') }}" target="_blank">Xem website</a></div>
<p class="muted">Chỉ cần nhập nội dung tiếng Việt. Khách chọn EN trên website để xem bản dịch tự động qua Google Translate.</p>
<form class="admin-settings" method="post" action="{{ route('admin.content.update', $section) }}">@csrf @method('PUT')
@if($section === 'trang-chu')
<section class="settings-group">
<h2>Hình ảnh slider trang chủ</h2>
<p>Chọn ảnh từ kho hoặc <a href="{{ route('admin.media.index') }}">tải ảnh mới lên kho hình ảnh</a>. Ảnh cũ vẫn được giữ trong kho khi thay ảnh slider.</p>
<label>Lọc kho ảnh theo năm <select id="hero-year"><option value="">Tất cả các năm</option>@foreach($images->pluck('year')->unique()->sortDesc() as $year)<option value="{{ $year }}">{{ $year }}</option>@endforeach</select></label>
<div class="media-grid">
<label class="media-card"><input type="radio" name="settings[home_hero_image]" value="" @checked(!old('settings.home_hero_image', $settings['home_hero_image'] ?? ''))><img src="{{ asset('images/hero-healthcare-team.png') }}" alt="Ảnh mặc định">Ảnh mặc định</label>
@foreach($images as $image)
<label class="media-card" data-media-year="{{ $image['year'] }}"><input type="radio" name="settings[home_hero_image]" value="{{ $image['path'] }}" @checked(old('settings.home_hero_image', $settings['home_hero_image'] ?? '') === $image['path'])><img src="{{ $image['url'] }}" alt="{{ basename($image['path']) }}" loading="lazy"><span>{{ basename($image['path']) }} · {{ $image['year'] }}</span></label>
@endforeach
</div>
</section>
@endif
<section class="settings-group settings-section"><div class="form-grid">@foreach($fields as $key => $field)<label class="{{ $field[1] === 'textarea' ? 'span-2' : '' }}">{{ $field[0] }}@if($field[1] === 'textarea')<textarea name="settings[{{ $key }}]" rows="4">{{ old('settings.'.$key, $settings[$key] ?? '') }}</textarea>@else<input type="{{ $field[1] }}" name="settings[{{ $key }}]" value="{{ old('settings.'.$key, $settings[$key] ?? '') }}">@endif</label>@endforeach</div></section>
@if($errors->any())<div class="alert error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<button class="btn" type="submit">Lưu thay đổi</button></form>
@endsection
