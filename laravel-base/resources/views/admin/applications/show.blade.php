@extends('layouts.admin')
@section('title','Hồ sơ '.$application->name)
@section('content')
<div class="page-head"><div><h1>{{ $application->name }}</h1><p class="muted">Ứng tuyển: {{ $application->job?->title }}</p></div><a class="btn" href="{{ route('admin.applications.download',$application) }}">Tải CV</a></div>
<div class="card"><div class="card-body"><dl class="details"><dt>Email</dt><dd>{{ $application->email }}</dd><dt>Điện thoại</dt><dd>{{ $application->phone }}</dd><dt>Lời nhắn</dt><dd>{!! nl2br(e($application->cover_letter ?: '—')) !!}</dd><dt>File CV</dt><dd>{{ $application->cv_original_name }}</dd></dl><form method="post" action="{{ route('admin.applications.update',$application) }}">@csrf @method('PATCH')<label>Trạng thái<select name="status">@foreach(['new'=>'Mới','reviewed'=>'Đã xem','shortlisted'=>'Tiềm năng','rejected'=>'Không phù hợp'] as $k=>$v)<option value="{{ $k }}" @selected($application->status===$k)>{{ $v }}</option>@endforeach</select></label><button class="btn" type="submit">Cập nhật</button></form></div></div>
@endsection
