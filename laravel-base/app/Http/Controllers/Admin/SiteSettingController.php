<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', ['settings' => SiteSetting::values(), 'fields' => $this->fields()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [];
        foreach (array_keys($this->fields()) as $key) {
            $rules['settings.'.$key] = ['nullable', 'string', 'max:20000'];
        }

        $values = $request->validate($rules)['settings'] ?? [];
        SiteSetting::putMany($values);

        return back()->with('success', 'Đã lưu nội dung website.');
    }

    private function fields(): array
    {
        return [
            'company_name' => ['Thông tin doanh nghiệp', 'Tên thương hiệu', 'text'],
            'company_legal_name' => ['Thông tin doanh nghiệp', 'Tên pháp lý doanh nghiệp', 'text'],
            'company_email' => ['Thông tin doanh nghiệp', 'Email nhận hồ sơ / liên hệ', 'email'],
            'company_phone' => ['Thông tin doanh nghiệp', 'Số điện thoại', 'text'],
            'company_tax_id' => ['Thông tin doanh nghiệp', 'Mã số thuế', 'text'],
            'company_address_vi' => ['Thông tin doanh nghiệp', 'Địa chỉ (Tiếng Việt)', 'text'],
            'company_address_en' => ['Thông tin doanh nghiệp', 'Address (English)', 'text'],
            'footer_description_vi' => ['Thông tin doanh nghiệp', 'Mô tả chân trang (Tiếng Việt)', 'textarea'],
            'footer_description_en' => ['Thông tin doanh nghiệp', 'Footer description (English)', 'textarea'],
            'home_eyebrow_vi' => ['Trang chủ', 'Nhãn hero (Tiếng Việt)', 'text'],
            'home_eyebrow_en' => ['Trang chủ', 'Hero label (English)', 'text'],
            'home_title_vi' => ['Trang chủ', 'Tiêu đề hero (Tiếng Việt)', 'textarea'],
            'home_title_en' => ['Trang chủ', 'Hero title (English)', 'textarea'],
            'home_body_vi' => ['Trang chủ', 'Mô tả hero (Tiếng Việt)', 'textarea'],
            'home_body_en' => ['Trang chủ', 'Hero description (English)', 'textarea'],
            'home_intro_title_vi' => ['Trang chủ', 'Tiêu đề giới thiệu (Tiếng Việt)', 'textarea'],
            'home_intro_title_en' => ['Trang chủ', 'Introduction title (English)', 'textarea'],
            'home_intro_body_vi' => ['Trang chủ', 'Mô tả giới thiệu (Tiếng Việt)', 'textarea'],
            'home_intro_body_en' => ['Trang chủ', 'Introduction description (English)', 'textarea'],
            'culture_eyebrow_vi' => ['Trang chủ', 'Nhãn văn hóa (Tiếng Việt)', 'text'],
            'culture_eyebrow_en' => ['Trang chủ', 'Culture label (English)', 'text'],
            'culture_title_vi' => ['Trang chủ', 'Tiêu đề văn hóa (Tiếng Việt)', 'textarea'],
            'culture_title_en' => ['Trang chủ', 'Culture title (English)', 'textarea'],
            'culture_body_vi' => ['Trang chủ', 'Mô tả văn hóa (Tiếng Việt)', 'textarea'],
            'culture_body_en' => ['Trang chủ', 'Culture description (English)', 'textarea'],
            'about_eyebrow_vi' => ['Giới thiệu', 'Nhãn trang (Tiếng Việt)', 'text'],
            'about_eyebrow_en' => ['Giới thiệu', 'Page label (English)', 'text'],
            'about_title_vi' => ['Giới thiệu', 'Tiêu đề (Tiếng Việt)', 'textarea'],
            'about_title_en' => ['Giới thiệu', 'Title (English)', 'textarea'],
            'about_body_vi' => ['Giới thiệu', 'Nội dung (Tiếng Việt)', 'textarea'],
            'about_body_en' => ['Giới thiệu', 'Content (English)', 'textarea'],
            'about_story_title_vi' => ['Giới thiệu', 'Tiêu đề câu chuyện (Tiếng Việt)', 'textarea'],
            'about_story_title_en' => ['Giới thiệu', 'Story title (English)', 'textarea'],
            'contact_title_vi' => ['Liên hệ', 'Tiêu đề (Tiếng Việt)', 'text'],
            'contact_title_en' => ['Liên hệ', 'Title (English)', 'text'],
            'contact_body_vi' => ['Liên hệ', 'Mô tả (Tiếng Việt)', 'textarea'],
            'contact_body_en' => ['Liên hệ', 'Description (English)', 'textarea'],
        ];
    }
}
