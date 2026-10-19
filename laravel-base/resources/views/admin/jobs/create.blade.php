@extends('layouts.admin')
@section('title', 'Đăng tin tuyển dụng')
@section('content')
<div class="page-head"><h1>Đăng tin tuyển dụng</h1><a class="link" href="{{ route('admin.jobs.index') }}">← Quay lại</a></div><form method="post" enctype="multipart/form-data" action="{{ route('admin.jobs.store') }}">@include('admin.jobs._form')</form>
@endsection
