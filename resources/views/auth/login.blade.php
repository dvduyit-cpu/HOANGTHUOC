@extends('layouts.app')
@section('title', 'Đăng nhập quản trị')
@section('content')
<section class="container auth-wrap section">
    <div class="card auth-card"><div class="card-body">
        <span class="eyebrow">QUẢN TRỊ</span><h1>Đăng nhập</h1>
        <form method="post" action="{{ route('admin.login.store') }}">
            @csrf
            <label>Email<input type="email" name="email" value="{{ old('email') }}" required autofocus></label>
            <label>Mật khẩu<input type="password" name="password" required></label>
            <label class="check"><input type="checkbox" name="remember" value="1"> Ghi nhớ đăng nhập</label>
            @foreach($errors->all() as $error)<small class="field-error">{{ $error }}</small>@endforeach
            <button class="btn full" type="submit">Đăng nhập</button>
        </form>
    </div></div>
</section>
@endsection
