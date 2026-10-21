<select id="field_{{ $key }}" name="settings[{{ $key }}]">
    <option value="three" @selected(old('settings.'.$key, $site[$key] ?? 'three') === 'three')>Hiển thị 3 sản phẩm cùng lúc</option>
    <option value="single" @selected(old('settings.'.$key, $site[$key] ?? 'three') === 'single')>Slider một ảnh lớn</option>
</select>
