@extends('layouts.admin')
@section('title','Sửa tài khoản')
@section('content')<h1>Sửa tài khoản</h1><div class="card"><div class="card-body"><form method="post" action="{{ route('admin.users.update',$user) }}">@method('PUT') @include('admin.users._form')</form></div></div>@endsection
