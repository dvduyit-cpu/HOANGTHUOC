<h2>Có hồ sơ ứng tuyển mới</h2>
<p><strong>Vị trí:</strong> {{ $application->job->title }}</p>
<p><strong>Ứng viên:</strong> {{ $application->name }}</p>
<p><strong>Email:</strong> {{ $application->email }}</p>
<p><strong>Điện thoại:</strong> {{ $application->phone }}</p>
<p><strong>Lời nhắn:</strong><br>{!! nl2br(e($application->cover_letter ?: 'Không có')) !!}</p>
<p>CV được đính kèm trong email này.</p>
