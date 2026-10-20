# Đưa website lên hosting

## Cập nhật host đang chạy bằng Git

Chạy trong thư mục chứa `artisan` trên host:

```bash
git pull --ff-only admin master
php artisan optimize:clear
php artisan migrate --force
php artisan website:update-content --overwrite
php artisan optimize
```

Nếu remote trên host tên `origin`, dùng `git pull --ff-only origin master`.
`website:update-content --overwrite` cập nhật nội dung Việt/Anh được cung cấp,
giữ lựa chọn ảnh và sản phẩm đã chỉnh, đồng thời thêm ảnh sản phẩm mẫu nếu chưa có.
Lệnh in đường dẫn bản sao nội dung cũ trong `storage/app/private/deployment/backups`.
Muốn chỉ bổ sung mục còn thiếu và giữ toàn bộ nội dung đang dùng, bỏ `--overwrite`.
Giữ nguyên `.env` và `APP_KEY`; không chạy `migrate:fresh`, `db:seed` hay `key:generate`.
Database, tài khoản, tin tuyển dụng, bài viết và hồ sơ vẫn được giữ.

## Nhập toàn bộ dữ liệu hiện tại

Gói đã xuất nằm ở `storage/app/private/deployment/website.json`, gồm tài khoản (mật khẩu băm), quyền, cấu hình, nội dung, tin tuyển dụng, bài viết, hồ sơ ứng tuyển và các tệp ảnh/CV. Giữ gói ngoài thư mục `public`.

Upload mã nguồn và gói này lên host. Điền `.env` cho database MySQL **mới, trống**, tên miền và SMTP, sau đó chạy:

```bash
php artisan optimize:clear
php artisan key:generate
php artisan migrate --force
php artisan website:transfer import
php artisan storage:link
php artisan optimize
```

Chỉ chạy `key:generate` khi cài mới; giữ nguyên khóa nếu host đã có ứng dụng đang sử dụng. **Không chạy `db:seed` trước khi nhập gói.** Đăng nhập bằng tài khoản và mật khẩu đang dùng ở bản hiện tại. Lệnh nhập dừng nếu các bảng dữ liệu đã có nội dung hoặc tệp đích khác nội dung, không ghi đè.

Ảnh và CV được khôi phục từ gói vào storage; chúng vẫn là tệp, database lưu đường dẫn. Giao diện và dòng “Phát triển web Đặng Việt Duy” cố định nằm trong mã nguồn. Không nhập sessions, cache, hàng đợi hay lịch sử đăng nhập.

Để xuất lại sau khi thay đổi dữ liệu trên máy hiện tại:

```bash
php artisan website:transfer export
```

Sau khi nhập thành công, xóa gói `storage/app/private/deployment/website.json` trên host. Gói có dữ liệu tài khoản và hồ sơ ứng viên, chỉ chuyển qua kênh riêng.

## Cài mới với dữ liệu mẫu

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
