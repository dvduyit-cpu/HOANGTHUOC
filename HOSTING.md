# Đưa website lên hosting

Hosting cần PHP 8.3+, các extension Laravel yêu cầu và PDO MySQL, MySQL/MariaDB.

1. Upload mã nguồn và `vendor` (hoặc chạy `composer install --no-dev --optimize-autoloader` trên host). Không upload `.git`, `laravel-base`, backup ZIP và `.env` máy cá nhân.
2. Trỏ document root tên miền vào thư mục `public`. Đặt phần còn lại của dự án ngoài vùng truy cập công khai.
3. Sao chép `.env.hosting.example` thành `.env` trên host. Điền tên miền HTTPS, database, SMTP, `HR_EMAIL`, email và mật khẩu admin (ít nhất 10 ký tự). Bật SSL cho tên miền. SMTP cổng 465 dùng `MAIL_SCHEME=smtps`.
4. Cho phép web server ghi vào `storage` và `bootstrap/cache`.
5. Chạy tại thư mục gốc:

```bash
php artisan optimize:clear
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force
php artisan storage:link
php artisan optimize
```

Chỉ chạy `key:generate` khi cài mới. Khi nâng cấp website đang chạy, giữ nguyên `APP_KEY`, sao lưu database và upload trước khi migrate. Seeder chỉ tạo admin chưa tồn tại; không ghi đè mật khẩu/quyền tài khoản hiện có. Chạy seeder trước `optimize` vì seeder đọc ADMIN từ `.env`.

CSS, JS và ảnh hiện có trong `public`, giao diện hiện tại không cần chạy Vite trên host. Nếu không có SSH, dùng Terminal trong bảng điều khiển hoặc nhờ hosting chạy các lệnh trên.

Đăng nhập tại `/admin/login`, vào **Tài khoản & phân quyền** để cấp tài khoản, đổi mật khẩu, khóa/mở và chọn quyền. Admin toàn quyền, Editor truy cập các mục đã chọn. Editor cũ chưa cấu hình quyền giữ quyền tuyển dụng, tin tức, nội dung như trước. Bỏ chọn tất cả thu hồi quyền quản lý. Khóa tài khoản chặn cả phiên đăng nhập hiện tại.

Sau triển khai, kiểm tra đăng nhập, phân quyền Editor, gửi liên hệ, nộp CV, email HR và ảnh upload. `.env` máy phát triển được giữ nguyên; mẫu hosting không chứa thông tin đăng nhập thật.
