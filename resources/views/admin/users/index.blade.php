@extends('layouts.admin')
@section('title','Tài khoản & quyền')
@section('content')
<div class="page-head"><div><h1>Tài khoản & quyền</h1><p class="muted">Admin cấp tài khoản cho người được phép đăng tin.</p></div><a class="btn" href="{{ route('admin.users.create') }}">+ Cấp tài khoản</a></div>
<div class="table-wrap"><table><thead><tr><th>Họ tên</th><th>Email</th><th>Vai trò</th><th>Trạng thái</th><th></th></tr></thead><tbody>@foreach($users as $user)<tr><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->role }}</td><td>{{ $user->is_active ? 'Hoạt động' : 'Đã khóa' }}</td><td><a href="{{ route('admin.users.edit',$user) }}">Sửa quyền</a></td></tr>@endforeach</tbody></table></div><div class="pagination">{{ $users->links() }}</div>
@endsection
