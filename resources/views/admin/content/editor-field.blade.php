@php
    $value = old('settings.'.$key, $settings[$key] ?? '');
    $language = str_ends_with($key, '_en') ? 'en' : (str_ends_with($key, '_vi') ? 'vi' : 'all');
@endphp
<div class="editor-field" data-editor-field data-key="{{ $key }}" data-type="{{ $field[1] }}" data-language="{{ $language }}" data-group="{{ \App\Support\VisualEditor::group($section, $key) }}">
    <label class="editor-field-label" for="field_{{ $key }}">{{ $field[0] }}</label>
    @if($field[1] === 'image')
        <div class="editor-image-control">
            <img data-field-thumbnail src="{{ $value ? asset('storage/'.$value) : '' }}" alt="Ảnh đang chọn" @if(!$value) hidden @endif>
            <select id="field_{{ $key }}" name="settings[{{ $key }}]" data-image-select>
                <option value="">Dùng ảnh mặc định / không chọn ảnh</option>
                @foreach($images as $image)<option value="{{ $image['path'] }}" data-image-url="{{ $image['url'] }}" @selected($value === $image['path'])>{{ basename($image['path']) }}</option>@endforeach
            </select>
            <button class="ghost-btn" type="button" data-open-media="{{ $key }}">Chọn ảnh từ thư viện</button>
            <label class="editor-upload">Hoặc tải ảnh mới<input type="file" name="uploads[{{ $key }}]" accept="image/jpeg,image/png,image/webp,image/gif"></label>
            <small>Tối đa 5 MB. Ảnh tải mới được ưu tiên; lưu nháp hoặc xuất bản để giữ ảnh.</small>
        </div>
    @elseif($field[1] === 'textarea')
        <textarea id="field_{{ $key }}" name="settings[{{ $key }}]" rows="4" maxlength="20000">{{ $value }}</textarea>
    @elseif($field[1] === 'alignment')
        @include('admin.content.alignment-field')
    @elseif($field[1] === 'product_layout')
        @include('admin.content.product-layout-field')
    @elseif($field[1] === 'color')
        @include('admin.content.color-field')
    @elseif($field[1] === 'text_color')
        <div class="editor-color-control"><input type="color" value="{{ preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? $value : '#102f43' }}" data-text-color-picker aria-label="Chọn màu chữ"><input id="field_{{ $key }}" name="settings[{{ $key }}]" value="{{ $value }}" placeholder="Màu mặc định" pattern="#[0-9a-fA-F]{6}" maxlength="7"><button type="button" data-reset-style class="ghost-btn">Mặc định</button></div>
    @elseif($field[1] === 'toggle')
        <select id="field_{{ $key }}" name="settings[{{ $key }}]"><option value="1" @selected((string) $value === '1')>Hiển thị</option><option value="0" @selected((string) $value === '0')>Ẩn</option></select>
    @elseif($field[1] === 'checkbox')
        <input type="hidden" name="settings[{{ $key }}]" value="0"><input id="field_{{ $key }}" type="checkbox" name="settings[{{ $key }}]" value="1" @checked((string) $value === '1')>
    @else
        <input id="field_{{ $key }}" type="{{ $field[1] }}" name="settings[{{ $key }}]" value="{{ $value }}" @if($field[1] === 'number') min="8" max="96" step="1" @endif>
        @if($field[1] === 'number')<small>Để trống để dùng cỡ chữ của giao diện.</small>@endif
    @endif
</div>
