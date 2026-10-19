<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tuyển dụng')</title>
    <meta name="description" content="@yield('meta_description', 'Cơ hội việc làm và thông tin tuyển dụng')">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="site-header">
    <div class="container nav">
        <a class="brand" href="{{ route('home') }}">TUYỂN DỤNG</a>
        <nav>
            <a href="{{ route('home') }}">Việc làm</a>
            <a href="{{ route('contact.create') }}">Liên hệ</a>
        </nav>
    </div>
</header>
<main>
    @if(session('success'))
        <div class="container alert success">{{ session('success') }}</div>
    @endif
    @yield('content')
</main>
<footer class="footer">
    <div class="container">© {{ date('Y') }} Tuyển dụng. Thông tin ứng viên được sử dụng cho mục đích tuyển dụng.</div>
</footer>
</body>
</html>
