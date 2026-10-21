<select name="settings[{{ $key }}]">
    @foreach(['inherit' => 'Theo mặc định', 'left' => 'Canh trái', 'center' => 'Canh giữa', 'right' => 'Canh phải', 'justify' => 'Canh đều hai bên'] as $alignment => $label)
        <option value="{{ $alignment }}" @selected(old('settings.'.$key, $settings[$key] ?? 'inherit') === $alignment)>{{ $label }}</option>
    @endforeach
</select>
