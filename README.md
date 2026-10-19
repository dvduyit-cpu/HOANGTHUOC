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

## Cài đặt nhanh
1. Tạo dự án Laravel 13 mới:
   ```bash
   composer create-project laravel/laravel:^13.0 recruitment
   cd recruitment
   ```
2. Sao chép toàn bộ file trong gói này vào đúng thư mục dự án (ghi đè file cùng tên nếu có).
3. Thiết lập `.env` theo `.env.recruitment.example`.
4. Chạy:
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   php artisan serve
   ```
5. Mở `/admin/login` để đăng nhập.

## Tài khoản quản trị ban đầu
Seeder lấy từ `.env`:
- `ADMIN_NAME`
- `ADMIN_EMAIL`
- `ADMIN_PASSWORD`

**Hãy đổi mật khẩu ngay khi triển khai thật.**

## Ghi chú SMTP Gmail
Nếu dùng Gmail, nên dùng App Password của tài khoản tổ chức/HR thay vì mật khẩu đăng nhập thông thường. Không commit `.env` lên Git.
