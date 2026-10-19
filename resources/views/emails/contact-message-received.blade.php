<h2>Liên hệ tuyển dụng mới</h2>
<p><strong>Họ tên:</strong> {{ $data['name'] }}</p>
<p><strong>Email:</strong> {{ $data['email'] }}</p>
<p><strong>Điện thoại:</strong> {{ $data['phone'] ?: 'Không cung cấp' }}</p>
<p><strong>Chủ đề:</strong> {{ $data['subject'] }}</p>
<p><strong>Nội dung:</strong><br>{!! nl2br(e($data['message'])) !!}</p>
