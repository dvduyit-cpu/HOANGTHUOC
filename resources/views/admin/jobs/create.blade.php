@extends('layouts.admin')
@section('title','Đăng tin mới')
@section('content')<div class="page-head"><h1>Đăng tin tuyển dụng</h1></div><div class="card"><div class="card-body"><form method="post" enctype="multipart/form-data" action="{{ route('admin.jobs.store') }}">@include('admin.jobs._form')</form></div></div>@endsection
