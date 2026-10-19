@extends('layouts.admin')
@section('title', $sectionData['title'])
@section('content')
<div class="page-head"><div><a class="back" href="{{ route('admin.content.index') }}">← Quản lý nội dung</a><h1>{{ $sectionData['title'] }}</h1><p class="muted">{{ $sectionData['description'] }}</p></div><a class="btn" href="{{ route('home') }}" target="_blank">Xem website</a></div>
<form class="admin-settings" method="post" action="{{ route('admin.content.update', $section) }}">@csrf @method('PUT')
<section class="settings-group settings-section"><div class="form-grid">@foreach($fields as $key => $field)<label class="{{ $field[1] === 'textarea' ? 'span-2' : '' }}">{{ $field[0] }}@if($field[1] === 'textarea')<textarea name="settings[{{ $key }}]" rows="4">{{ old('settings.'.$key, $settings[$key] ?? '') }}</textarea>@else<input type="{{ $field[1] }}" name="settings[{{ $key }}]" value="{{ old('settings.'.$key, $settings[$key] ?? '') }}">@endif</label>@endforeach</div></section>
@if($errors->any())<div class="alert error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<button class="btn" type="submit">Lưu thay đổi</button></form>
@endsection
