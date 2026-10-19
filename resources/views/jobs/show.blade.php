@extends('layouts.app')
@section('title', $job->title)
@section('meta_description', $job->summary)
@section('content')
<section class="container section">
    <a class="back" href="{{ route('home') }}">← Tất cả việc làm</a>
    <div class="job-layout">
        <article class="card detail-card">
            @if($job->image_path)<img class="hero-image" src="{{ asset('storage/'.$job->image_path) }}" alt="{{ $job->title }}">@endif
            <div class="card-body">
                <div class="tag">{{ strtoupper($job->employment_type) }}</div>
                <h1>{{ $job->title }}</h1>
                <p class="muted">📍 {{ $job->location }} · 💰 {{ $job->salary ?: 'Thỏa thuận' }}</p>
                @if($job->expires_at)<p class="muted">Hạn nộp: {{ $job->expires_at->format('d/m/Y') }}</p>@endif
                <hr>
                <h2>Mô tả công việc</h2>
                <div class="prose">{!! nl2br(e($job->description)) !!}</div>
                @if($job->requirements)<h2>Yêu cầu</h2><div class="prose">{!! nl2br(e($job->requirements)) !!}</div>@endif
                @if($job->benefits)<h2>Quyền lợi</h2><div class="prose">{!! nl2br(e($job->benefits)) !!}</div>@endif
            </div>
        </article>
        <aside class="card apply-card" id="apply">
            <div class="card-body">
                <h2>Nộp CV</h2>
                <p class="muted">Không cần tạo tài khoản. CV tối đa 5MB.</p>
                @if($errors->any())<div class="alert error">Vui lòng kiểm tra lại thông tin.</div>@endif
                <form method="post" enctype="multipart/form-data" action="{{ route('applications.store', $job->slug) }}">
                    @csrf
                    <label>Họ và tên<input name="name" value="{{ old('name') }}" required></label>
                    <label>Email<input type="email" name="email" value="{{ old('email') }}" required></label>
                    <label>Số điện thoại<input name="phone" value="{{ old('phone') }}" required></label>
                    <label>Lời nhắn<textarea name="cover_letter" rows="5">{{ old('cover_letter') }}</textarea></label>
                    <label>CV (PDF/DOC/DOCX)<input type="file" name="cv" accept=".pdf,.doc,.docx" required></label>
                    @foreach($errors->all() as $error)<small class="field-error">{{ $error }}</small>@endforeach
                    <button class="btn full" type="submit">Gửi hồ sơ</button>
                </form>
            </div>
        </aside>
    </div>
</section>
@endsection
