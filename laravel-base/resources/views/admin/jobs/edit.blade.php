@extends('layouts.admin')
@section('title', 'Sửa tin tuyển dụng')
@section('content')
<div class="page-head"><h1>Sửa: {{ $job->title }}</h1><a class="link" href="{{ route('admin.jobs.index') }}">← Quay lại</a></div><form method="post" enctype="multipart/form-data" action="{{ route('admin.jobs.update', $job) }}">@method('PUT') @include('admin.jobs._form')</form>
@endsection
