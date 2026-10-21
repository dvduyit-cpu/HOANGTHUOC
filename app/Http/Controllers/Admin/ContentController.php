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
            'settings' => array_replace(\App\Support\PageContent::defaults(), SiteSetting::values()),
            'images' => MediaController::images(),
            'section' => $section,
            'sectionData' => $this->sections()[$section],
            'fields' => $this->fieldsFor($section),
        ]);
    }

    public function update(Request $request, string $section): RedirectResponse
    {
        abort_unless(array_key_exists($section, $this->sections()), 404);

        $fields = $this->fieldsFor($section);
        $data = $request->validate($this->contentRules($fields));
        $values = $data['settings'] ?? [];
        foreach ($fields as $key => $field) {
            if ($field[1] === 'image' && $request->hasFile('uploads.'.$key)) {
                $values[$key] = $request->file('uploads.'.$key)->store('media/'.now()->timezone('Asia/Bangkok')->format('Y'), 'public');
            }
        }
        SiteSetting::putMany($values);

        return back()->with('success', 'Đã lưu thay đổi.');
    }

    public function preview(Request $request, string $section): \Illuminate\Http\Response
    {
        abort_unless(array_key_exists($section, $this->sections()), 404);
        $data = $request->validate($this->contentRules($this->fieldsFor($section)));
        $request->attributes->set('content_preview', $data['settings'] ?? []);
        $locale = $request->validate(['preview_locale' => ['required', 'in:vi,en']])['preview_locale'];
        app()->setLocale($locale);
        $view = match ($section) {
            'gioi-thieu' => view('pages.about'),
            'doi-ngu' => view('pages.team'),
            'tuyen-dung' => app(\App\Http\Controllers\JobController::class)->index($request),
            'tin-tuc' => app(\App\Http\Controllers\NewsController::class)->index(),
            'lien-he' => view('contact.create'),
            default => app(\App\Http\Controllers\HomeController::class)->index(),
        };

        return response($view->render())->header('Cache-Control', 'no-store');
    }

    private function contentRules(array $fields): array
    {
        $rules = [];
        foreach ($fields as $key => $field) {
            $rules['settings.'.$key] = $field[1] === 'image'
                ? ['nullable', 'string', \Illuminate\Validation\Rule::in(MediaController::images()->pluck('path')->all())]
                : ['nullable', 'string', 'max:20000'];
            if (in_array($field[1], ['toggle', 'checkbox'], true)) {
                $rules['settings.'.$key] = ['sometimes', 'required', 'in:0,1'];
            }
            if ($field[1] === 'number') {
                $rules['settings.'.$key] = ['nullable', 'integer', 'between:8,96'];
            }
            if ($field[1] === 'alignment') {
                $rules['settings.'.$key] = ['nullable', 'in:inherit,left,center,right,justify'];
            }
            if ($field[1] === 'product_layout') {
                $rules['settings.'.$key] = ['sometimes', 'required', 'in:single,three'];
            }
            if ($field[1] === 'color') {
                $rules['settings.'.$key] = ['sometimes', 'required', 'regex:/^#[0-9a-fA-F]{6}$/'];
            }
            if ($field[1] === 'image') {
                $rules['uploads.'.$key] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'];
            }
        }
        return $rules;
    }

    private function sections(): array
    {
        $sections = [
            'thuong-hieu' => ['title' => 'Thương hiệu & doanh nghiệp', 'description' => 'Tên thương hiệu, thông tin pháp lý và nội dung hiển thị ở chân trang.', 'fields' => ['company_name', 'company_legal_name', 'company_email', 'company_phone', 'company_tax_id', 'company_address_vi', 'company_address_en', 'footer_description_vi', 'footer_description_en']],
            'trang-chu' => ['title' => 'Nội dung trang chủ', 'description' => 'Chỉnh sửa banner, phần giới thiệu và văn hóa doanh nghiệp trên trang chủ.', 'fields' => ['culture_image', 'home_eyebrow_vi', 'home_eyebrow_en', 'home_title_vi', 'home_title_en', 'home_body_vi', 'home_body_en', 'home_intro_title_vi', 'home_intro_title_en', 'home_intro_body_vi', 'home_intro_body_en', 'culture_eyebrow_vi', 'culture_eyebrow_en', 'culture_title_vi', 'culture_title_en', 'culture_body_vi', 'culture_body_en']],
            'doi-ngu' => ['title' => 'Đội ngũ', 'description' => 'Chỉnh sửa ảnh chân dung, ảnh đội ngũ và mô tả bên trên Hành trình phát triển.', 'fields' => ['team_portrait_image']],
            'gioi-thieu' => ['title' => 'Nội dung trang giới thiệu', 'description' => 'Chỉnh sửa thông điệp và câu chuyện thương hiệu trên trang Giới thiệu.', 'fields' => ['about_eyebrow_vi', 'about_eyebrow_en', 'about_title_vi', 'about_title_en', 'about_body_vi', 'about_body_en', 'about_story_title_vi', 'about_story_title_en']],
            'lien-he' => ['title' => 'Nội dung trang liên hệ', 'description' => 'Chỉnh sửa lời chào và phần mô tả ở trang Liên hệ.', 'fields' => ['contact_title_vi', 'contact_title_en', 'contact_body_vi', 'contact_body_en']],
        ];
        foreach (['tuyen-dung' => 'Trang tuyển dụng', 'tin-tuc' => 'Trang tin tức', 'mau-sac' => 'Màu sắc website'] as $key => $title) {
            $sections[$key] = ['title' => $title, 'description' => 'Chỉnh sửa nội dung hiển thị trên trang.', 'fields' => []];
        }
        $routes = ['trang-chu' => 'home', 'gioi-thieu' => 'about', 'doi-ngu' => 'team', 'tuyen-dung' => 'jobs.index', 'tin-tuc' => 'news', 'lien-he' => 'contact.create', 'thuong-hieu' => 'home'];
        $routes['mau-sac'] = 'home';
        foreach ($sections as $key => &$section) {
            $section['fields'] = array_merge($section['fields'], array_keys(config('page-content.'.$key, [])));
            $section['route'] = $routes[$key];
        }
        unset($section);
        $sections['trang-chu']['fields'][] = 'home_hero_image';
        return $sections;
    }

    private function fieldsFor(string $section): array
    {
        $fields = $this->fields();
        $fields['home_hero_image'] = ['Ảnh banner trang chủ', 'image'];
        foreach (config('page-content', []) as $pageFields) {
            $fields = array_merge($fields, $pageFields);
        }

        $result = [];
        foreach ($this->sections()[$section]['fields'] as $key) {
            if (str_ends_with($key, '_en')) {
                continue;
            }
            $result[$key] = $fields[$key];
            if (str_ends_with($key, '_vi') && in_array($fields[$key][1], ['text', 'textarea'], true)) {
                $englishKey = substr($key, 0, -3).'_en';
                $result[$englishKey] = [$fields[$key][0].' — English', $fields[$key][1]];
            }
        }
        $result['content_alignment_'.$section] = ['Canh chữ mặc định cho toàn trang', 'alignment'];
        foreach ($result as $key => $field) {
            if (in_array($field[1], ['text', 'textarea'], true) && !str_ends_with($key, '_en')) {
                $result[$key.'_alignment'] = ['Canh chữ: '.$field[0], 'alignment'];
            }
        }
        return $result;
    }

    private function fields(): array
    {
        return [
            'culture_image' => ['Ảnh bệnh nhân / văn hóa', 'image'], 'team_portrait_image' => ['Khối 1 — Ảnh bên trái (chân dung / đội ngũ)', 'image'],
            'company_name' => ['Tên thương hiệu', 'text'], 'company_legal_name' => ['Tên pháp lý doanh nghiệp', 'text'], 'company_email' => ['Email nhận hồ sơ / liên hệ', 'email'], 'company_phone' => ['Số điện thoại', 'text'], 'company_tax_id' => ['Mã số thuế', 'text'], 'company_address_vi' => ['Địa chỉ (Tiếng Việt)', 'text'], 'company_address_en' => ['Address (English)', 'text'], 'footer_description_vi' => ['Mô tả chân trang (Tiếng Việt)', 'textarea'], 'footer_description_en' => ['Footer description (English)', 'textarea'],
            'home_eyebrow_vi' => ['Nhãn banner (Tiếng Việt)', 'text'], 'home_eyebrow_en' => ['Banner label (English)', 'text'], 'home_title_vi' => ['Tiêu đề banner (Tiếng Việt)', 'textarea'], 'home_title_en' => ['Banner title (English)', 'textarea'], 'home_body_vi' => ['Mô tả banner (Tiếng Việt)', 'textarea'], 'home_body_en' => ['Banner description (English)', 'textarea'], 'home_intro_title_vi' => ['Tiêu đề giới thiệu (Tiếng Việt)', 'textarea'], 'home_intro_title_en' => ['Introduction title (English)', 'textarea'], 'home_intro_body_vi' => ['Mô tả giới thiệu (Tiếng Việt)', 'textarea'], 'home_intro_body_en' => ['Introduction description (English)', 'textarea'], 'culture_eyebrow_vi' => ['Nhãn văn hóa (Tiếng Việt)', 'text'], 'culture_eyebrow_en' => ['Culture label (English)', 'text'], 'culture_title_vi' => ['Tiêu đề văn hóa (Tiếng Việt)', 'textarea'], 'culture_title_en' => ['Culture title (English)', 'textarea'], 'culture_body_vi' => ['Mô tả văn hóa (Tiếng Việt)', 'textarea'], 'culture_body_en' => ['Culture description (English)', 'textarea'],
            'about_eyebrow_vi' => ['Nhãn trang (Tiếng Việt)', 'text'], 'about_eyebrow_en' => ['Page label (English)', 'text'], 'about_title_vi' => ['Tiêu đề (Tiếng Việt)', 'textarea'], 'about_title_en' => ['Title (English)', 'textarea'], 'about_body_vi' => ['Nội dung (Tiếng Việt)', 'textarea'], 'about_body_en' => ['Content (English)', 'textarea'], 'about_story_title_vi' => ['Tiêu đề câu chuyện (Tiếng Việt)', 'textarea'], 'about_story_title_en' => ['Story title (English)', 'textarea'],
            'contact_title_vi' => ['Tiêu đề (Tiếng Việt)', 'text'], 'contact_title_en' => ['Title (English)', 'text'], 'contact_body_vi' => ['Mô tả (Tiếng Việt)', 'textarea'], 'contact_body_en' => ['Description (English)', 'textarea'],
        ];
    }
}
