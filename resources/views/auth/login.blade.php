<!doctype html>
<html lang="vi">
<head>
    @include('partials.favicon')
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập · {{ $site['admin_name'] ?? 'Hoang Thuoc Medical' }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin-workspace.css') }}?v={{ filemtime(public_path('css/admin-workspace.css')) }}">
    @include('partials.admin-theme')
</head>
<body>
<main class="admin-login">
    <section class="login-intro">
        <a class="brand" href="{{ route('home') }}"><span class="brand-logo"><img src="{{ !empty($site['site_logo']) ? asset('storage/'.$site['site_logo']) : asset('images/hoang-thuoc-logo.png') }}" alt=""></span><span><strong>{{ $site['admin_name'] ?? 'Hoang Thuoc Medical' }}</strong><small>QUẢN TRỊ WEBSITE</small></span></a>
        <span class="eyebrow">KHÔNG GIAN LÀM VIỆC</span><h1>Kết nối nhân tài.<br>Phát triển đội ngũ.</h1><p>Quản lý tuyển dụng, hồ sơ ứng viên và nội dung website trong cùng một không gian.</p>
    </section>
    <section class="login-form-wrap"><div class="login-form">
        <div class="eyebrow">CHÀO MỪNG TRỞ LẠI</div><h2>Đăng nhập quản trị</h2><p>Sử dụng tài khoản được cấp để tiếp tục.</p>
        @if($errors->any())<div class="alert error" role="alert">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
        <form method="post" action="{{ route('admin.login.store') }}">
            @csrf
            <label>Email<input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Email của bạn"></label>
            <label>Mật khẩu<input type="password" name="password" required autocomplete="current-password" placeholder="Nhập mật khẩu"></label>
            <label class="check"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Ghi nhớ đăng nhập</label>
            <button class="btn full" type="submit">Đăng nhập <span aria-hidden="true">→</span></button>
        </form>
        <a class="back" href="{{ route('home') }}">← Quay về website</a>
        <div class="login-footer">{{ $site['admin_footer'] ?? 'Hoang Thuoc Medical · Hệ thống quản trị website tuyển dụng' }}</div>
        <div class="login-footer">@include('partials.developer-credit')</div>
    </div></section>
</main>
</body>
</html>
