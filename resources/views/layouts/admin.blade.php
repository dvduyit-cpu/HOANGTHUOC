<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>@yield('title','Quản trị')</title><link rel="stylesheet" href="{{ asset('css/app.css') }}"></head>
<body class="admin-body">
<header class="admin-top"><div class="container nav"><a class="brand" href="{{ route('admin.dashboard') }}">ADMIN TUYỂN DỤNG</a><div class="admin-user">{{ auth()->user()->name }} · {{ auth()->user()->role }} <form method="post" action="{{ route('admin.logout') }}">@csrf<button class="link-button">Đăng xuất</button></form></div></div></header>
<div class="container admin-layout">
<aside class="sidebar"><a href="{{ route('admin.dashboard') }}">Tổng quan</a><a href="{{ route('admin.jobs.index') }}">Tin tuyển dụng</a>@if(auth()->user()->isAdmin())<a href="{{ route('admin.applications.index') }}">Hồ sơ ứng viên</a><a href="{{ route('admin.users.index') }}">Tài khoản & quyền</a>@endif<a href="{{ route('home') }}" target="_blank">Xem website ↗</a></aside>
<main class="admin-main">@if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif @yield('content')</main>
</div></body></html>
