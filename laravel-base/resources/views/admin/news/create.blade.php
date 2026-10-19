@extends('layouts.admin')
@section('title', 'Viết bài mới')
@section('content')
<div class="page-head"><h1>Viết bài mới</h1><a class="link" href="{{ route('admin.news.index') }}">← Quay lại</a></div><form method="post" enctype="multipart/form-data" action="{{ route('admin.news.store') }}">@include('admin.news._form') </form>
@endsection
