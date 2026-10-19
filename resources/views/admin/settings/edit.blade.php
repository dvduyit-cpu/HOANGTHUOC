@extends('layouts.admin')
@section('title', 'Cấu hình trang')
@section('content')
<div class="page-head"><div><div class="eyebrow">NHẬN DIỆN & GIAO DIỆN</div><h1>Cấu hình trang</h1><p class="muted">Thiết lập thương hiệu website và không gian làm việc của đội ngũ.</p></div><a class="ghost-btn" href="{{ route('home') }}" target="_blank" rel="noopener"><x-admin-icon name="globe" size="17" /> Xem website</a></div>
<nav class="settings-tabs" aria-label="Cấu hình website"><a class="active" href="{{ route('admin.settings.edit') }}" aria-current="page">Cài đặt chung & giao diện</a><a href="{{ route('admin.content.index') }}">Nội dung website</a></nav>
<form method="post" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="admin-settings" data-settings-form>
    @csrf @method('PUT')
    <section class="settings-group">
        <div class="form-section-title"><span><x-admin-icon name="settings" /></span><div><h2>Cài đặt chung</h2><p>Tên hệ thống và chân trang áp dụng cho toàn bộ khu vực quản trị.</p></div></div>
        <div class="form-grid">
            <label class="span-2">Tên hệ thống <b>*</b><input name="admin_name" required maxlength="120" value="{{ old('admin_name', $settings['admin_name'] ?? 'Hoang Thuoc Medical') }}"></label>
            <div class="branding-preview span-2"><img data-logo-preview src="{{ !empty($settings['site_logo']) ? asset('storage/'.$settings['site_logo']) : asset('images/hoang-thuoc-logo.png') }}" alt="Logo hiện tại"><div><strong>{{ $settings['admin_name'] ?? 'Hoang Thuoc Medical' }}</strong><small>Logo dùng cho website, quản trị và trang đăng nhập</small></div></div>
            <label class="span-2">Tải logo mới<input type="file" name="logo" accept="image/png,image/jpeg,image/webp" data-logo-input><small>PNG, JPG hoặc WebP, tối đa 2 MB. Để trống để giữ logo hiện tại.</small></label>
            @if(!empty($settings['site_logo']))<label class="check span-2"><input type="checkbox" name="remove_logo" value="1" @checked(old('remove_logo'))> Dùng lại logo mặc định</label>@endif
            <label class="span-2">Nội dung chân trang quản trị<textarea name="admin_footer" rows="2" maxlength="500">{{ old('admin_footer', $settings['admin_footer'] ?? 'Hoang Thuoc Medical · Hệ thống quản trị website tuyển dụng') }}</textarea><small>Để trống nếu muốn ẩn nội dung chân trang.</small></label>
        </div>
    </section>
    <section class="settings-group">
        <div class="form-section-title"><span><x-admin-icon name="globe" /></span><div><h2>Thông tin website</h2><p>Tiêu đề mặc định và mô tả dùng cho các trang chưa có nội dung riêng.</p></div></div>
        <div class="form-grid">
            <label class="span-2">Tên website <b>*</b><input name="site_title" required maxlength="160" value="{{ old('site_title', $settings['site_title'] ?? 'Hoang Thuoc Medical') }}"></label>
            <label class="span-2">Mô tả website<textarea name="site_description" maxlength="500" rows="3">{{ old('site_description', $settings['site_description'] ?? 'Hoang Thuoc Medical Careers') }}</textarea></label>
        </div>
        <div class="info-note">Thông tin doanh nghiệp, địa chỉ, liên hệ và nội dung song ngữ được quản lý tại <a href="{{ route('admin.content.index') }}">Nội dung website →</a></div>
    </section>
    <section class="settings-group">
        <div class="form-section-title"><span>Aa</span><div><h2>Giao diện quản trị</h2><p>Thiết lập chung cho các tài khoản quản trị và biên tập viên. Xem trước trước khi lưu.</p></div></div>
        <label>Màu chủ đạo</label>
        <div class="theme-swatches">
            @foreach(['#2563eb' => 'Xanh dương', '#0f766e' => 'Xanh ngọc', '#166534' => 'Xanh lá', '#7c3aed' => 'Tím', '#be185d' => 'Hồng', '#c2410c' => 'Cam', '#b91c1c' => 'Đỏ', '#334155' => 'Xám'] as $color => $label)
                <button type="button" class="theme-swatch" data-color="{{ $color }}" style="--swatch: {{ $color }}" aria-pressed="false"><span></span>{{ $label }}</button>
            @endforeach
        </div>
        <div class="form-grid">
            <label>Tự chọn màu<input type="color" name="admin_color" id="admin-color" value="{{ old('admin_color', $settings['admin_color'] ?? '#2563eb') }}" required></label>
            <label>Mã màu HEX<input type="text" id="admin-color-hex" value="{{ old('admin_color', $settings['admin_color'] ?? '#2563eb') }}" pattern="#[0-9a-fA-F]{6}" maxlength="7" spellcheck="false" required></label>
            <label>Phông chữ<select name="admin_font" id="admin-font">@foreach(['system' => 'Hệ thống · Segoe UI', 'arial' => 'Arial', 'serif' => 'Georgia'] as $key => $label)<option value="{{ $key }}" @selected(old('admin_font', $settings['admin_font'] ?? 'system') === $key)>{{ $label }}</option>@endforeach</select></label>
            <label>Cỡ chữ<select name="admin_font_size" id="admin-font-size">@foreach(range(14, 20) as $size)<option value="{{ $size }}" @selected((int) old('admin_font_size', $settings['admin_font_size'] ?? 16) === $size)>{{ $size }} px{{ $size === 16 ? ' · Mặc định' : '' }}</option>@endforeach</select></label>
        </div>
        <div class="theme-preview"><div><strong>Xem trước giao diện</strong><p>Màu sắc và phông chữ áp dụng ngay để bạn xem thử.</p></div><button class="btn" type="button">Nút thao tác</button><span class="link">Liên kết điều hướng</span></div>
        <button class="ghost-btn" type="button" data-reset-theme>Khôi phục giao diện mặc định</button>
    </section>
    <div class="settings-save"><span>Thay đổi được áp dụng sau khi lưu.</span><button class="btn" type="submit"><x-admin-icon name="check" size="18" /> Lưu cấu hình</button></div>
</form>
@endsection
