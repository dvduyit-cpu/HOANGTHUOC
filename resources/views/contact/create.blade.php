@extends('layouts.app')
@section('title', 'Liên hệ tuyển dụng')
@section('content')
<section class="container narrow section">
    <div class="card"><div class="card-body">
        <span class="eyebrow">LIÊN HỆ</span>
        <h1>Liên hệ bộ phận tuyển dụng</h1>
        <p class="muted">Gửi câu hỏi, bộ phận nhân sự sẽ phản hồi qua email hoặc điện thoại.</p>
        <form method="post" action="{{ route('contact.store') }}">
            @csrf
            <label>Họ và tên<input name="name" value="{{ old('name') }}" required></label>
            <label>Email<input type="email" name="email" value="{{ old('email') }}" required></label>
            <label>Số điện thoại<input name="phone" value="{{ old('phone') }}"></label>
            <label>Chủ đề<input name="subject" value="{{ old('subject') }}" required></label>
            <label>Nội dung<textarea name="message" rows="7" required>{{ old('message') }}</textarea></label>
            @foreach($errors->all() as $error)<small class="field-error">{{ $error }}</small>@endforeach
            <button class="btn" type="submit">Gửi liên hệ</button>
        </form>
    </div></div>
</section>
@endsection
