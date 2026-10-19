@extends('layouts.admin')
@section('title','Hồ sơ ứng viên')
@section('content')
<div class="page-head"><div><h1>Hồ sơ ứng viên</h1><p class="muted">CV được lưu private và chỉ Admin tải được.</p></div></div>
<div class="table-wrap"><table><thead><tr><th>Ứng viên</th><th>Vị trí</th><th>Ngày nộp</th><th>Email gửi HR</th><th>Trạng thái</th><th></th></tr></thead><tbody>@foreach($applications as $a)<tr><td><strong>{{ $a->name }}</strong><br><small>{{ $a->email }}</small></td><td>{{ $a->job?->title }}</td><td>{{ $a->created_at->format('d/m/Y H:i') }}</td><td>{{ $a->email_sent_at ? 'Đã gửi' : 'Chưa gửi/SMTP lỗi' }}</td><td>{{ $a->status }}</td><td><a href="{{ route('admin.applications.show',$a) }}">Xem</a></td></tr>@endforeach</tbody></table></div><div class="pagination">{{ $applications->links() }}</div>
@endsection
