@extends('layouts.app')
@section('title', 'Cơ hội việc làm')
@section('content')
<section class="hero">
    <div class="container">
        <span class="eyebrow">CƠ HỘI NGHỀ NGHIỆP</span>
        <h1>Tìm vị trí phù hợp với bạn</h1>
        <p>Khám phá các vị trí đang tuyển và gửi CV trực tiếp cho bộ phận nhân sự.</p>
        <form class="search" method="get" action="{{ route('home') }}">
            <input name="q" value="{{ $keyword }}" placeholder="Tên vị trí, địa điểm...">
            <button class="btn" type="submit">Tìm việc</button>
        </form>
    </div>
</section>
<section class="container section">
    <div class="section-head"><h2>Vị trí đang tuyển</h2><span>{{ $jobs->total() }} vị trí</span></div>
    <div class="grid jobs-grid">
        @forelse($jobs as $job)
            <article class="card job-card">
                @if($job->image_path)<img src="{{ asset('storage/'.$job->image_path) }}" alt="{{ $job->title }}">@endif
                <div class="card-body">
                    <div class="tag">{{ strtoupper($job->employment_type) }}</div>
                    <h3><a href="{{ route('jobs.show', $job->slug) }}">{{ $job->title }}</a></h3>
                    <p class="muted">📍 {{ $job->location }}</p>
                    <p>{{ $job->summary }}</p>
                    <div class="job-foot">
                        <span>{{ $job->salary ?: 'Thỏa thuận' }}</span>
                        <a class="link" href="{{ route('jobs.show', $job->slug) }}">Xem chi tiết →</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="empty">Chưa có vị trí phù hợp.</div>
        @endforelse
    </div>
    <div class="pagination">{{ $jobs->links() }}</div>
</section>
@endsection
