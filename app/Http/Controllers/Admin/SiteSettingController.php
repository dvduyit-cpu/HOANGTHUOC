<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', ['settings' => SiteSetting::values()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $values = $request->validate([
            'admin_name' => ['required', 'string', 'max:120'],
            'site_title' => ['required', 'string', 'max:160'],
            'site_description' => ['nullable', 'string', 'max:500'],
            'admin_footer' => ['nullable', 'string', 'max:500'],
            'admin_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'admin_header_color' => ['sometimes', 'required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'admin_sidebar_color' => ['sometimes', 'required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'admin_font' => ['required', 'in:system,arial,serif'],
            'admin_font_size' => ['required', 'integer', 'between:14,20'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'remove_logo' => ['sometimes', 'boolean'],
        ]);

        unset($values['logo'], $values['remove_logo']);
        $uploadedPath = null;

        if ($request->hasFile('logo')) {
            $uploadedPath = $request->file('logo')->store('branding', 'public');
            $values['site_logo'] = $uploadedPath;
        } elseif ($request->boolean('remove_logo')) {
            $values['site_logo'] = '';
        }

        try {
            DB::transaction(fn () => SiteSetting::putMany($values));
        } catch (\Throwable $exception) {
            if ($uploadedPath !== null) {
                Storage::disk('public')->delete($uploadedPath);
            }
            throw $exception;
        }

        return back()->with('success', 'Đã lưu cấu hình trang và giao diện quản trị.');
    }
}
