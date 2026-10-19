@extends('layouts.admin')
@section('title','Tổng quan')
@section('content')
<div class="page-head"><div><h1>Tổng quan</h1><p class="muted">Quản lý website tuyển dụng.</p></div><a class="btn" href="{{ route('admin.jobs.create') }}">+ Đăng tin mới</a></div>
<div class="stats"><div class="stat"><strong>{{ $jobCount }}</strong><span>Tổng tin</span></div><div class="stat"><strong>{{ $publishedCount }}</strong><span>Đang hiển thị</span></div><div class="stat"><strong>{{ $applicationCount }}</strong><span>Hồ sơ</span></div><div class="stat"><strong>{{ $userCount }}</strong><span>Tài khoản quản trị</span></div></div>
@endsection
