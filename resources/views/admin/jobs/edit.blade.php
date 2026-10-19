@extends('layouts.admin')
@section('title','Sửa tin')
@section('content')<div class="page-head"><h1>Sửa: {{ $job->title }}</h1></div><div class="card"><div class="card-body"><form method="post" enctype="multipart/form-data" action="{{ route('admin.jobs.update',$job) }}">@method('PUT') @include('admin.jobs._form')</form></div></div>@endsection
