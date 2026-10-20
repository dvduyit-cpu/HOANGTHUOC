@php
    $adminName = $site['admin_name'] ?? 'Hoang Thuoc Medical';
@endphp
<!doctype html>
<html lang="vi">
<head>
    @include('partials.favicon')
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tổng quan') · {{ $adminName }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin-workspace.css') }}?v={{ filemtime(public_path('css/admin-workspace.css')) }}">
    <script defer src="{{ asset('js/admin-workspace.js') }}?v={{ filemtime(public_path('js/admin-workspace.js')) }}"></script>
    @include('partials.admin-theme')
    <link rel="stylesheet" href="{{ asset('css/admin-navigation.css') }}?v={{ filemtime(public_path('css/admin-navigation.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/admin-content.css') }}?v={{ filemtime(public_path('css/admin-content.css')) }}">
    <script defer src="{{ asset('js/password-toggle.js') }}?v={{ filemtime(public_path('js/password-toggle.js')) }}"></script>
</head>
<body class="admin-body" data-admin-user="{{ auth()->id() }}">
<a class="skip-link" href="#main-content">Đến nội dung chính</a>
<button class="sidebar-overlay" type="button" data-close-menu aria-label="Đóng menu" hidden></button>
<aside class="sidebar" id="sidebar" aria-label="Menu quản trị">
    <a class="brand" href="{{ route('admin.dashboard') }}"><span class="brand-logo"><img src="{{ !empty($site['site_logo']) ? asset('storage/'.$site['site_logo']) : asset('images/hoang-thuoc-logo.png') }}" alt=""></span><span><strong>{{ $adminName }}</strong><small>QUẢN TRỊ WEBSITE</small></span></a>
    <nav aria-label="Điều hướng chính">
        <details class="nav-group" open><summary>NỘI DUNG & TUYỂN DỤNG <span>⌄</span></summary>
            @foreach([['admin.dashboard', 'dashboard', 'Tổng quan', 'admin.dashboard'], ['admin.jobs.index', 'briefcase', 'Tin tuyển dụng', 'admin.jobs.*'], ['admin.news.index', 'news', 'Tin tức & bài viết', 'admin.news.*'], ['admin.content.index', 'layers', 'Nội dung website', 'admin.content.*']] as [$route, $icon, $label, $pattern])
                @if($route === 'admin.dashboard' || auth()->user()->hasPermission(explode('.', $route)[1]))
                <a class="nav-link {{ request()->routeIs($pattern) ? 'active' : '' }}" href="{{ route($route) }}" @if(request()->routeIs($pattern)) aria-current="page" @endif><x-admin-icon :name="$icon" /><span>{{ $label }}</span></a>
                @endif
            @endforeach
            @if(auth()->user()->hasPermission('content'))<a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}"><x-admin-icon name="layers" /><span>Sản phẩm</span></a><a class="nav-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}" href="{{ route('admin.media.index') }}"><x-admin-icon name="folder" /><span>Kho hình ảnh</span></a>@endif
            @if(auth()->user()->hasPermission('applications'))<a class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}" href="{{ route('admin.applications.index') }}" @if(request()->routeIs('admin.applications.*')) aria-current="page" @endif><x-admin-icon name="folder" /><span>Hồ sơ ứng viên</span></a>@endif
        </details>
        @if(auth()->user()->isAdmin())
        <details class="nav-group" open><summary>TÀI KHOẢN & HỆ THỐNG <span>⌄</span></summary>
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}" @if(request()->routeIs('admin.users.*')) aria-current="page" @endif><x-admin-icon name="users" /><span>Tài khoản & phân quyền</span></a>
            <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}" @if(request()->routeIs('admin.settings.*')) aria-current="page" @endif><x-admin-icon name="settings" /><span>Cấu hình trang</span></a>
        </details>
        @endif
    </nav>
    <div class="sidebar-bottom"><div class="help-card"><x-admin-icon name="globe" /><strong>Website của bạn</strong><p>Xem nội dung đã cập nhật trên trang công khai.</p><a href="{{ route('home') }}" target="_blank" rel="noopener">Mở website <span>↗</span></a></div><small>{{ $adminName }}</small></div>
</aside>
<div class="main-shell">
    <header class="topbar">
        <div class="topbar-left"><button class="icon-button" type="button" data-toggle-menu aria-label="Mở hoặc ẩn menu" aria-controls="sidebar" aria-expanded="true"><x-admin-icon name="menu" /></button><div class="topbar-heading"><strong>@yield('title', 'Tổng quan')</strong><nav class="header-breadcrumb" aria-label="Đường dẫn"><a href="{{ route('admin.dashboard') }}"><x-admin-icon name="home" size="14" /> Tổng quan</a><span>›</span><span aria-current="page">@yield('title', 'Tổng quan')</span></nav></div></div>
        <div class="topbar-right"><span class="today">{{ now()->timezone('Asia/Bangkok')->format('d/m/Y') }}</span><a class="icon-button" href="{{ route('home') }}" target="_blank" rel="noopener" aria-label="Xem website"><x-admin-icon name="globe" /></a><span class="topbar-divider"></span><details class="profile-menu"><summary><span class="avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span><span class="profile-name"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->isAdmin() ? 'Quản trị viên' : 'Biên tập viên' }}</small></span><span>⌄</span></summary><div class="profile-dropdown">@if(auth()->user()->isAdmin())<a href="{{ route('admin.settings.edit') }}">Cấu hình trang</a>@endif<form method="post" action="{{ route('admin.logout') }}">@csrf<button type="submit"><x-admin-icon name="logout" size="16" /> Đăng xuất</button></form></div></details></div>
    </header>
    <main class="content admin-main" id="main-content" tabindex="-1">
        @if(session('success'))<div class="alert success" role="status"><x-admin-icon name="check" /> {{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert error" role="alert"><strong>Vui lòng kiểm tra lại thông tin:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        @yield('content')
        <footer class="page-footer">{{ $site['admin_footer'] ?? 'Hoang Thuoc Medical · Hệ thống quản trị website tuyển dụng' }}<br>@include('partials.developer-credit')</footer>
    </main>
</div>
</body>
</html>
