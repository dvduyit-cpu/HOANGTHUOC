<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', ['sections' => $this->sections()]);
    }

    public function edit(string $section): View
    {
        abort_unless(array_key_exists($section, $this->sections()), 404);

        return view('admin.content.edit', [
            'settings' => SiteSetting::values(),
            'section' => $section,
            'sectionData' => $this->sections()[$section],
            'fields' => $this->fieldsFor($section),
        ]);
    }

    public function update(Request $request, string $section): RedirectResponse
    {
        abort_unless(array_key_exists($section, $this->sections()), 404);

        $rules = [];
        foreach (array_keys($this->fieldsFor($section)) as $key) {
            $rules['settings.'.$key] = ['nullable', 'string', 'max:20000'];
        }

        SiteSetting::putMany($request->validate($rules)['settings'] ?? []);

        return back()->with('success', 'Đã lưu thay đổi.');
    }

    private function sections(): array
    {
        return [
            'thuong-hieu' => ['title' => 'Thương hiệu & doanh nghiệp', 'description' => 'Tên thương hiệu, thông tin pháp lý và nội dung hiển thị ở chân trang.', 'fields' => ['company_name', 'company_legal_name', 'company_email', 'company_phone', 'company_tax_id', 'company_address_vi', 'company_address_en', 'footer_description_vi', 'footer_description_en']],
            'trang-chu' => ['title' => 'Nội dung trang chủ', 'description' => 'Chỉnh sửa banner, phần giới thiệu và văn hóa doanh nghiệp trên trang chủ.', 'fields' => ['home_eyebrow_vi', 'home_eyebrow_en', 'home_title_vi', 'home_title_en', 'home_body_vi', 'home_body_en', 'home_intro_title_vi', 'home_intro_title_en', 'home_intro_body_vi', 'home_intro_body_en', 'culture_eyebrow_vi', 'culture_eyebrow_en', 'culture_title_vi', 'culture_title_en', 'culture_body_vi', 'culture_body_en']],
            'gioi-thieu' => ['title' => 'Nội dung trang giới thiệu', 'description' => 'Chỉnh sửa thông điệp và câu chuyện thương hiệu trên trang Giới thiệu.', 'fields' => ['about_eyebrow_vi', 'about_eyebrow_en', 'about_title_vi', 'about_title_en', 'about_body_vi', 'about_body_en', 'about_story_title_vi', 'about_story_title_en']],
            'lien-he' => ['title' => 'Nội dung trang liên hệ', 'description' => 'Chỉnh sửa lời chào và phần mô tả ở trang Liên hệ.', 'fields' => ['contact_title_vi', 'contact_title_en', 'contact_body_vi', 'contact_body_en']],
        ];
    }

    private function fieldsFor(string $section): array
    {
        $fields = $this->fields();

        return collect($this->sections()[$section]['fields'])
            ->mapWithKeys(fn (string $key) => [$key => $fields[$key]])
            ->all();
    }

    private function fields(): array
    {
        return [
            'company_name' => ['Tên thương hiệu', 'text'], 'company_legal_name' => ['Tên pháp lý doanh nghiệp', 'text'], 'company_email' => ['Email nhận hồ sơ / liên hệ', 'email'], 'company_phone' => ['Số điện thoại', 'text'], 'company_tax_id' => ['Mã số thuế', 'text'], 'company_address_vi' => ['Địa chỉ (Tiếng Việt)', 'text'], 'company_address_en' => ['Address (English)', 'text'], 'footer_description_vi' => ['Mô tả chân trang (Tiếng Việt)', 'textarea'], 'footer_description_en' => ['Footer description (English)', 'textarea'],
            'home_eyebrow_vi' => ['Nhãn banner (Tiếng Việt)', 'text'], 'home_eyebrow_en' => ['Banner label (English)', 'text'], 'home_title_vi' => ['Tiêu đề banner (Tiếng Việt)', 'textarea'], 'home_title_en' => ['Banner title (English)', 'textarea'], 'home_body_vi' => ['Mô tả banner (Tiếng Việt)', 'textarea'], 'home_body_en' => ['Banner description (English)', 'textarea'], 'home_intro_title_vi' => ['Tiêu đề giới thiệu (Tiếng Việt)', 'textarea'], 'home_intro_title_en' => ['Introduction title (English)', 'textarea'], 'home_intro_body_vi' => ['Mô tả giới thiệu (Tiếng Việt)', 'textarea'], 'home_intro_body_en' => ['Introduction description (English)', 'textarea'], 'culture_eyebrow_vi' => ['Nhãn văn hóa (Tiếng Việt)', 'text'], 'culture_eyebrow_en' => ['Culture label (English)', 'text'], 'culture_title_vi' => ['Tiêu đề văn hóa (Tiếng Việt)', 'textarea'], 'culture_title_en' => ['Culture title (English)', 'textarea'], 'culture_body_vi' => ['Mô tả văn hóa (Tiếng Việt)', 'textarea'], 'culture_body_en' => ['Culture description (English)', 'textarea'],
            'about_eyebrow_vi' => ['Nhãn trang (Tiếng Việt)', 'text'], 'about_eyebrow_en' => ['Page label (English)', 'text'], 'about_title_vi' => ['Tiêu đề (Tiếng Việt)', 'textarea'], 'about_title_en' => ['Title (English)', 'textarea'], 'about_body_vi' => ['Nội dung (Tiếng Việt)', 'textarea'], 'about_body_en' => ['Content (English)', 'textarea'], 'about_story_title_vi' => ['Tiêu đề câu chuyện (Tiếng Việt)', 'textarea'], 'about_story_title_en' => ['Story title (English)', 'textarea'],
            'contact_title_vi' => ['Tiêu đề (Tiếng Việt)', 'text'], 'contact_title_en' => ['Title (English)', 'text'], 'contact_body_vi' => ['Mô tả (Tiếng Việt)', 'textarea'], 'contact_body_en' => ['Description (English)', 'textarea'],
        ];
    }
}
