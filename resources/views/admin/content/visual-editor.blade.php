<link rel="stylesheet" href="{{ asset('css/visual-editor.css') }}?v={{ filemtime(public_path('css/visual-editor.css')) }}">
<div class="visual-editor-heading">
    <div><a class="back" href="{{ route('admin.content.index') }}">← Quản lý nội dung</a><h1>Chỉnh sửa giao diện</h1><p class="muted">Bấm vào chữ hoặc ảnh trên trang bên phải để chỉnh sửa. Kéo các khối để đổi thứ tự.</p></div>
    <label>Trang đang chỉnh<select id="editor-page">@foreach($sections as $pageKey => $page)<option value="{{ route('admin.content.edit', $pageKey) }}" @selected($pageKey === $section)>{{ $page['title'] }}</option>@endforeach</select></label>
</div>
@if($errors->any())<div class="alert error" role="alert"><strong>Chưa lưu được. Vui lòng kiểm tra:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="visual-editor-actions">
    <span id="editor-save-state" role="status">{{ $draft ? 'Đang chỉnh bản nháp' : 'Đang chỉnh nội dung đã xuất bản' }}</span>
    <a class="ghost-btn" href="{{ route($sectionData['route']) }}" target="_blank" rel="noopener">Xem trang này ↗</a>
    <button class="ghost-btn" type="submit" form="content-editor-form" name="intent" value="draft">Lưu nháp</button>
    <button class="btn" type="submit" form="content-editor-form" name="intent" value="publish">Xuất bản thay đổi</button>
</div>
<div class="content-editor visual-editor">
    <aside class="editor-panel">
        <form id="content-editor-form" class="admin-settings" data-preview-url="{{ route('admin.content.preview', $section) }}" data-section="{{ $section }}" method="post" enctype="multipart/form-data" action="{{ route('admin.content.update', $section) }}">
            @csrf @method('PUT')
            <div class="editor-panel-tabs"><button type="button" class="is-active" data-editor-tab="blocks">Bố cục trang</button><button type="button" data-editor-tab="fields">Chỉnh nội dung</button></div>
            <div data-editor-pane="blocks">
                <p class="editor-help">Chọn một khối để sửa. Dùng tay nắm ⋮⋮ hoặc nút ↑ ↓ để sắp xếp; bỏ tích để ẩn.</p>
                <ol id="editor-blocks" class="editor-blocks">
                    @foreach($layout as $item)
                        <li draggable="true" data-block-id="{{ $item['id'] }}">
                            <span class="editor-drag-handle" aria-hidden="true">⋮⋮</span>
                            <button type="button" data-select-block="{{ $item['id'] }}">{{ $blocks[$item['id']]['name'] }}</button>
                            <input type="checkbox" data-block-visible aria-label="Hiển thị {{ $blocks[$item['id']]['name'] }}" @checked($item['visible'])>
                            <button type="button" data-move-block="up" aria-label="Đưa khối lên">↑</button><button type="button" data-move-block="down" aria-label="Đưa khối xuống">↓</button>
                        </li>
                    @endforeach
                </ol>
                @if($blocks)<input type="hidden" name="settings[page_layout_{{ $section }}]" id="editor-layout" value="{{ old('settings.page_layout_'.$section, json_encode($layout)) }}">@endif
                <button class="ghost-btn full" type="button" data-select-block="general">Thiết lập chung</button>
                @if(in_array($section, ['gioi-thieu', 'doi-ngu']))<a class="editor-manager-link" href="{{ route('admin.products.index') }}">Quản lý slider Giải pháp y khoa →</a>@endif
                @if($section === 'tin-tuc')<a class="editor-manager-link" href="{{ route('admin.news.index') }}">Quản lý bản tin →</a>@endif
                @if($section === 'tuyen-dung')<a class="editor-manager-link" href="{{ route('admin.jobs.index') }}">Quản lý vị trí tuyển dụng →</a>@endif
                <a class="editor-manager-link" href="{{ route('admin.content.edit', 'mau-sac') }}">Màu sắc website →</a>
            </div>
            <div data-editor-pane="fields">
                <div class="editor-field-heading"><h2 id="editor-selection-title">Nội dung trang</h2><button type="button" class="ghost-btn" id="editor-show-all">Xem tất cả</button></div>
                <label class="editor-search">Tìm mục cần sửa<input id="editor-field-search" type="search" placeholder="Nhập tiêu đề, ảnh, nội dung…"></label>
                <p id="editor-no-fields" class="editor-help" hidden>Phần này lấy nội dung từ danh sách riêng. Dùng liên kết quản lý ở thẻ Bố cục trang.</p>
                @foreach($fields as $key => $field)
                    @if($field[1] !== 'layout')@include('admin.content.editor-field')@endif
                @endforeach
                @if($section === 'mau-sac')<button class="ghost-btn" type="button" id="reset-website-colors">Khôi phục màu mặc định</button>@endif
            </div>
        </form>
        <details class="editor-history"><summary>Bản nháp & lịch sử phiên bản</summary>
            @if($draft)<p>Có bản nháp chưa xuất bản.</p><form method="post" action="{{ route('admin.content.discard-draft', $section) }}">@csrf @method('DELETE')<button class="ghost-btn" type="submit">Bỏ bản nháp đã lưu</button></form>@endif
            @forelse($history as $version)
                <form method="post" action="{{ route('admin.content.restore', $section) }}">@csrf<input type="hidden" name="version" value="{{ $version['id'] }}"><span>{{ \Carbon\Carbon::parse($version['saved_at'])->timezone('Asia/Bangkok')->format('d/m/Y H:i') }}</span><button class="ghost-btn" type="submit">Đưa vào nháp</button></form>
            @empty<p class="muted">Sau khi xuất bản, phiên bản trước sẽ xuất hiện tại đây.</p>@endforelse
        </details>
    </aside>
    <aside class="content-preview" aria-label="Xem trước website">
        <div class="preview-toolbar"><strong>Xem trước trực tiếp</strong><select id="preview-locale" aria-label="Ngôn ngữ xem trước"><option value="vi">Tiếng Việt</option><option value="en">English</option></select><select id="preview-device" aria-label="Kích thước xem trước"><option value="desktop">Máy tính</option><option value="mobile">Điện thoại</option></select></div>
        <p id="preview-status" role="status">Đang tải bản xem trước…</p>
        <small>Bấm chữ để gõ trực tiếp, bấm ảnh để thay. Website chỉ thay đổi khi bạn chọn Xuất bản.</small>
        <div class="preview-viewport"><iframe id="content-preview-frame" title="Bản xem trước website" sandbox="allow-same-origin" src="about:blank"></iframe></div>
    </aside>
</div>
<dialog id="editor-media-dialog" class="editor-media-dialog">
    <div class="editor-media-heading"><h2>Thư viện hình ảnh</h2><button type="button" class="ghost-btn" id="editor-close-media">Đóng</button></div>
    <label>Tìm ảnh<input id="editor-media-search" type="search" placeholder="Tên ảnh hoặc thư mục"></label>
    <div class="editor-media-grid">@foreach($images as $image)<button type="button" data-pick-media="{{ $image['path'] }}" data-image-url="{{ $image['url'] }}"><img src="{{ $image['url'] }}" alt="{{ basename($image['path']) }}" loading="lazy"><span>{{ basename($image['path']) }}</span></button>@endforeach</div>
    @if($images->isEmpty())<p>Chưa có ảnh trong kho. Đóng thư viện và chọn “Tải ảnh mới” ở bảng chỉnh sửa.</p>@endif
</dialog>
<script id="visual-editor-config" type="application/json">{!! json_encode(['blocks' => $blocks, 'section' => $section], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
<script src="{{ asset('js/visual-editor.js') }}?v={{ filemtime(public_path('js/visual-editor.js')) }}" defer></script>
