@extends('layouts.admin')
@section('title','Cấp tài khoản')
@section('content')<h1>Cấp tài khoản</h1><div class="card"><div class="card-body"><form method="post" action="{{ route('admin.users.store') }}">@include('admin.users._form')</form></div></div>@endsection
