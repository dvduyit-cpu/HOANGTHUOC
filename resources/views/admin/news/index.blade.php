@extends('layouts.admin')
@section('title', 'Tin tức')
@section('content')
<div class="page-head"><div><h1>Tin tức</h1><p class="muted">Quản lý bài viết hiển thị trên website.</p></div><a class="btn" href="{{ route('admin.news.create') }}">+ Viết bài mới</a></div>
<div class="table-wrap"><table><thead><tr><th>Tiêu đề</th><th>Trạng thái</th><th>Ngày đăng</th><th></th></tr></thead><tbody>@forelse($posts as $post)<tr><td><strong>{{ $post->title_vi }}</strong><br><small>{{ $post->title_en }}</small></td><td><span class="tag">{{ $post->status }}</span></td><td>{{ $post->published_at?->format('d/m/Y') ?: '—' }}</td><td class="actions"><a href="{{ route('admin.news.edit', $post) }}">Sửa</a><form method="post" action="{{ route('admin.news.destroy', $post) }}" onsubmit="return confirm('Xóa bài viết này?')">@csrf @method('DELETE')<button class="danger-link">Xóa</button></form></td></tr>@empty<tr><td colspan="4">Chưa có bài viết.</td></tr>@endforelse</tbody></table></div><div class="pagination">{{ $posts->links() }}</div>
@endsection
