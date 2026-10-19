@extends('layouts.admin')
@section('title', 'Sửa bài viết')
@section('content')
<div class="page-head"><h1>Sửa bài viết</h1><a class="link" href="{{ route('admin.news.index') }}">← Quay lại</a></div><form method="post" enctype="multipart/form-data" action="{{ route('admin.news.update', $post) }}">@method('PUT') @include('admin.news._form')</form>
@endsection
