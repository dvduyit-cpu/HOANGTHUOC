@extends('layouts.admin')
@section('title','Tài khoản & phân quyền')
@section('content')
<div class="page-head"><div><h1>Tài khoản & phân quyền</h1><p class="muted">Tạo tài khoản, đổi mật khẩu, khóa và cấp quyền theo từng mục.</p></div><a class="btn" href="{{ route('admin.users.create') }}">+ Cấp tài khoản</a></div>
<div class="table-wrap"><table><thead><tr><th>Họ tên</th><th>Email</th><th>Vai trò</th><th>Quyền truy cập</th><th>Trạng thái</th><th></th></tr></thead><tbody>
@foreach($users as $user)<tr><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->isAdmin() ? 'Quản trị viên' : 'Editor' }}</td><td>{{ $user->isAdmin() ? 'Toàn quyền' : (collect(\App\Models\User::PERMISSIONS)->filter(fn ($label, $key) => in_array($key, $user->permissions ?? ['jobs', 'news', 'content']))->implode(', ') ?: 'Chưa cấp quyền') }}</td><td>{{ $user->is_active ? 'Hoạt động' : 'Đã khóa' }}</td><td><a href="{{ route('admin.users.edit',$user) }}">Sửa quyền</a></td></tr>@endforeach
</tbody></table></div><div class="pagination">{{ $users->links() }}</div>
@endsection
