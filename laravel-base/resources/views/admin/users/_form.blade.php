@csrf
<div class="form-grid">
<label>Họ tên<input name="name" value="{{ old('name',$user->name) }}" required></label>
<label>Email<input type="email" name="email" value="{{ old('email',$user->email) }}" required></label>
<label>Mật khẩu<input type="password" name="password" {{ $user->exists ? '' : 'required' }}><small>{{ $user->exists ? 'Để trống nếu không đổi.' : 'Tối thiểu 10 ký tự.' }}</small></label>
<label>Xác nhận mật khẩu<input type="password" name="password_confirmation"></label>
<label>Vai trò<select name="role"><option value="editor" @selected(old('role',$user->role ?: 'editor')==='editor')>Editor – đăng/sửa tin</option><option value="admin" @selected(old('role',$user->role)==='admin')>Admin – toàn quyền</option></select></label>
<label>Trạng thái<select name="is_active"><option value="1" @selected((string)old('is_active',$user->exists ? (int)$user->is_active : 1)==='1')>Hoạt động</option><option value="0" @selected((string)old('is_active',$user->exists ? (int)$user->is_active : 1)==='0')>Khóa</option></select></label>
</div>
@if($errors->any())<div class="alert error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<button class="btn" type="submit">Lưu tài khoản</button>
