<input id="field_{{ $key }}" type="color" name="settings[{{ $key }}]" value="{{ old('settings.'.$key, $settings[$key] ?? $field[2]) }}" data-website-color data-default="{{ $field[2] }}">
<input type="text" value="{{ old('settings.'.$key, $settings[$key] ?? $field[2]) }}" data-color-hex aria-label="{{ $field[0] }} — Mã HEX" pattern="#[0-9a-fA-F]{6}" maxlength="7" spellcheck="false">
<small>Chọn màu hoặc nhập mã HEX, ví dụ #146a9b.</small>
