# Kiến trúc

Public:
- `/`: danh sách việc làm
- `/viec-lam/{slug}`: chi tiết + form nộp CV
- `/lien-he`: form liên hệ

Admin:
- `/admin/login`: đăng nhập
- `/admin`: dashboard
- `/admin/jobs`: editor/admin CRUD tin
- `/admin/applications`: admin xem hồ sơ, tải CV
- `/admin/users`: admin cấp/khóa tài khoản và vai trò

Vai trò:
- admin: toàn quyền
- editor: chỉ quản lý nội dung tuyển dụng

Lưu trữ:
- `public` disk: ảnh tin tuyển dụng
- `local` disk: CV private

Email:
- CV -> email riêng của từng tin (nếu có), nếu không dùng `HR_EMAIL`
- Liên hệ -> `HR_EMAIL`
