@extends('layouts.app')

@section('title', $job->localized('title').' | '.($site['site_title'] ?? 'Hoang Thuoc Medical'))
@section('meta_description', $job->localized('summary'))

@section('content')
<section class="container section new-job-page">
    <a class="back" href="{{ route('home') }}">← {{ $locale === 'en' ? 'All jobs' : 'Tất cả việc làm' }}</a>

    <div class="job-layout">
        <article class="card detail-card">
            @if($job->image_path)
                <img class="hero-image" src="{{ asset('storage/'.$job->image_path) }}" alt="{{ $job->localized('title') }}">
            @endif
            <div class="card-body">
                <div class="tag">{{ strtoupper($job->employment_type) }}</div>
                <h1>{{ $job->localized('title') }}</h1>
                <p class="muted">📍 {{ $job->localized('location') }} · 💰 {{ $job->localized('salary') ?: ($locale === 'en' ? 'Negotiable' : 'Thỏa thuận') }}</p>
                @if($job->expires_at)
                    <p class="muted">{{ $locale === 'en' ? 'Deadline' : 'Hạn nộp' }}: {{ $job->expires_at->format('d/m/Y') }}</p>
                @endif
                <hr>
                <h2>{{ $locale === 'en' ? 'Job description' : 'Mô tả công việc' }}</h2>
                <div class="prose">{!! nl2br(e($job->localized('description'))) !!}</div>
                @if(filled($job->localized('requirements')))
                    <h2>{{ $locale === 'en' ? 'Requirements' : 'Yêu cầu' }}</h2>
                    <div class="prose">{!! nl2br(e($job->localized('requirements'))) !!}</div>
                @endif
                @if(filled($job->localized('benefits')))
                    <h2>{{ $locale === 'en' ? 'Benefits' : 'Quyền lợi' }}</h2>
                    <div class="prose">{!! nl2br(e($job->localized('benefits'))) !!}</div>
                @endif
            </div>
        </article>

        <aside class="card apply-card" id="apply">
            <div class="card-body">
                <h2>{{ $locale === 'en' ? 'Apply now' : 'Nộp CV' }}</h2>
                <p class="muted">{{ $locale === 'en' ? 'No account required. Maximum file size: 5MB.' : 'Không cần tạo tài khoản. CV tối đa 5MB.' }}</p>
                <form method="post" enctype="multipart/form-data" action="{{ route('applications.store', $job->slug) }}">
                    @csrf
                    <label>{{ $locale === 'en' ? 'Full name' : 'Họ và tên' }}<input name="name" value="{{ old('name') }}" required></label>
                    <label>Email<input type="email" name="email" value="{{ old('email') }}" required></label>
                    <label>{{ $locale === 'en' ? 'Phone number' : 'Số điện thoại' }}<input name="phone" value="{{ old('phone') }}" required></label>
                    <label>{{ $locale === 'en' ? 'Message' : 'Lời nhắn' }}<textarea name="cover_letter" rows="5">{{ old('cover_letter') }}</textarea></label>
                    <label>CV (PDF/DOC/DOCX)<input type="file" name="cv" accept=".pdf,.doc,.docx" required></label>
                    @foreach($errors->all() as $error)<small class="field-error">{{ $error }}</small>@endforeach
                    <button class="btn full" type="submit">{{ $locale === 'en' ? 'Submit application' : 'Gửi hồ sơ' }}</button>
                </form>
            </div>
        </aside>
    </div>
</section>
@endsection
