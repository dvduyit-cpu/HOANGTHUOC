@extends('layouts.admin')
@section('title','Tin tuyển dụng')
@section('content')
<div class="page-head"><div><h1>Tin tuyển dụng</h1><p class="muted">Tạo, chỉnh sửa và đóng tin.</p></div><a class="btn" href="{{ route('admin.jobs.create') }}">+ Đăng tin</a></div>
<div class="table-wrap"><table><thead><tr><th>Vị trí</th><th>Trạng thái</th><th>Hạn nộp</th><th>Người tạo</th><th></th></tr></thead><tbody>
@foreach($jobs as $job)<tr><td><strong>{{ $job->title }}</strong><br><small>{{ $job->location }}</small></td><td><span class="tag">{{ $job->status }}</span></td><td>{{ $job->expires_at?->format('d/m/Y') ?: '—' }}</td><td>{{ $job->creator?->name }}</td><td class="actions"><a href="{{ route('admin.jobs.edit',$job) }}">Sửa</a><form method="post" action="{{ route('admin.jobs.destroy',$job) }}" onsubmit="return confirm('Xóa tin này?')">@csrf @method('DELETE')<button class="danger-link">Xóa</button></form></td></tr>@endforeach
</tbody></table></div><div class="pagination">{{ $jobs->links() }}</div>
@endsection
