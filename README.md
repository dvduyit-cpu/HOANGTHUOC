# Website tuyển dụng Laravel 13

Mô-đun tuyển dụng dành cho Laravel 13.

## Chức năng
- Ứng viên không cần đăng ký tài khoản.
- Xem danh sách / chi tiết việc làm đang tuyển.
- Tìm kiếm việc làm.
- Nộp CV PDF/DOC/DOCX; CV lưu private và được đính kèm email gửi HR.
- Trang liên hệ gửi email trực tiếp HR.
- Đăng nhập quản trị riêng, không có đăng ký công khai.
- Vai trò `admin`: toàn quyền, quản lý tài khoản, tin tuyển dụng, hồ sơ.
- Vai trò `editor`: được đăng/sửa/xóa tin tuyển dụng.
- Ảnh tin tuyển dụng lưu trên public disk.
- Hồ sơ ứng viên lưu DB để tránh mất dữ liệu nếu SMTP gặp lỗi.

## Yêu cầu
- PHP 8.3+
- Laravel 13.x
- MySQL / MariaDB
- SMTP (Gmail, Microsoft 365, mail hosting...)

## Chạy dự án đã hợp nhất
Dự án chạy trực tiếp tại thư mục gốc `recruitment-laravel13`, gồm trang tuyển dụng, giới thiệu, tin tức, liên hệ, song ngữ và quản trị.

```bash
php artisan serve
```

Mở `http://localhost:8000`; quản trị tại `/admin/login`.

## Cài đặt trên máy mới
1. Chạy `composer install` tại thư mục gốc.
2. Sao chép `.env.recruitment.example` thành `.env` và cấu hình database, SMTP, tài khoản quản trị.
3. Chạy:
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   php artisan serve
   ```

Đã hợp nhất và xóa thư mục `laravel-base` cũ; chỉnh sửa và chạy ứng dụng ở thư mục gốc. File `merge-backup-*.zip` chứa bản mã nguồn gốc trước khi ghi đè. Các bản dự phòng có thể chứa dữ liệu riêng, cần lưu ngoài thư mục public.

## Tài khoản quản trị ban đầu
Seeder lấy từ `.env`:
- `ADMIN_NAME`
- `ADMIN_EMAIL`
- `ADMIN_PASSWORD`

**Hãy đổi mật khẩu ngay khi triển khai thật.**

## Ghi chú SMTP Gmail
Nếu dùng Gmail, nên dùng App Password của tài khoản tổ chức/HR thay vì mật khẩu đăng nhập thông thường. Không commit `.env` lên Git.
