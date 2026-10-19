@extends('layouts.admin')
@section('title', 'Nội dung & thương hiệu')
@section('content')
<div class="page-head"><div><h1>Nội dung & thương hiệu</h1><p class="muted">Các thay đổi ở đây sẽ hiển thị trực tiếp trên website tiếng Việt và tiếng Anh.</p></div><a class="btn" href="{{ route('home') }}" target="_blank">Xem website</a></div>
<form class="admin-settings" method="post" action="{{ route('admin.settings.update') }}">@csrf @method('PUT')
@foreach(collect($fields)->groupBy(fn($field) => $field[0], true) as $group => $groupFields)
<section class="settings-group"><h2>{{ $group }}</h2><div class="form-grid">@foreach($groupFields as $key => $field)<label class="{{ $field[2] === 'textarea' ? 'span-2' : '' }}">{{ $field[1] }}@if($field[2] === 'textarea')<textarea name="settings[{{ $key }}]" rows="4">{{ old('settings.'.$key, $settings[$key] ?? '') }}</textarea>@else<input type="{{ $field[2] }}" name="settings[{{ $key }}]" value="{{ old('settings.'.$key, $settings[$key] ?? '') }}">@endif</label>@endforeach</div></section>
@endforeach
@if($errors->any())<div class="alert error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<button class="btn" type="submit">Lưu toàn bộ nội dung</button></form>
@endsection
