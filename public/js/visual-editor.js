(() => {
    'use strict';
    const form = document.getElementById('content-editor-form');
    if (!form) return;
    const frame = document.getElementById('content-preview-frame');
    const status = document.getElementById('preview-status');
    const saveState = document.getElementById('editor-save-state');
    const locale = document.getElementById('preview-locale');
    const config = JSON.parse(document.getElementById('visual-editor-config').textContent);
    const fields = new Map([...form.querySelectorAll('[data-editor-field]')].map(node => [node.dataset.key, node]));
    const uploads = new Map();
    const list = document.getElementById('editor-blocks');
    const dialog = document.getElementById('editor-media-dialog');
    const search = document.getElementById('editor-field-search');
    let timer, pending, revision = 0, scrollTop = 0, activeKey = null, group = 'all', selectedKeys = [], dirty = false, submitting = false, mediaKey = null, dragItem = null;
    const imageTargets = {
        home_hero_image: '.hp-hero', culture_image: '.hp-culture', home_process_image: '#cung-dong-hanh', home_updates_image: '.hp-updates',
        home_journey_background_image: '.hp-process',
        about_hero_image: '.page-hero-about', about_story_image: '.about-story-image', about_team_background_image: '.team-invite', team_portrait_image: '.team-portrait', team_group_image: '.team-group-image',
        careers_hero_image: '.careers-hero', news_hero_image: '.news-hero', contact_hero_image: '.contact-hero',
    };
    const control = key => fields.get(key)?.querySelector(`[name="settings[${key}]"]:not([type="hidden"])`) || form.elements.namedItem(`settings[${key}]`);
    const baseKey = key => key.replace(/_(alignment|font_size|text_color|text_width|image_fit|image_x|image_y|image_height|image_overlay)$/, '').replace(/_en$/, '_vi');
    const localizedKey = key => locale.value === 'en' && fields.has(key.replace(/_vi$/, '_en')) ? key.replace(/_vi$/, '_en') : key;
    function markDirty() {dirty = true; saveState.textContent = 'Có thay đổi chưa lưu'; saveState.classList.add('is-dirty');}
    function setTab(tab) {
        form.querySelectorAll('[data-editor-pane]').forEach(node => node.hidden = node.dataset.editorPane !== tab);
        form.querySelectorAll('[data-editor-tab]').forEach(button => {button.classList.toggle('is-active', button.dataset.editorTab === tab); button.setAttribute('aria-pressed', String(button.dataset.editorTab === tab));});
    }
    function filterFields() {
        const term = search.value.trim().toLocaleLowerCase();
        let count = 0;
        fields.forEach((node, key) => {
            node.hidden = !((group === 'all' || node.dataset.group === group) && (!selectedKeys.length || selectedKeys.some(selected => baseKey(key) === baseKey(selected))) && (node.dataset.language === 'all' || node.dataset.language === locale.value) && (!term || node.textContent.toLocaleLowerCase().includes(term)));
            if (!node.hidden) count++;
        });
        document.getElementById('editor-no-fields').hidden = count > 0;
    }
    function selectBlock(id, reveal = true) {
        group = id; selectedKeys = []; activeKey = null; search.value = '';
        document.getElementById('editor-selection-title').textContent = config.blocks[id]?.name || 'Thiết lập chung';
        list.querySelectorAll('[data-block-id]').forEach(node => node.classList.toggle('is-selected', node.dataset.blockId === id));
        filterFields(); setTab('fields');
        if (reveal && config.blocks[id]) {
            const doc = frame.contentDocument;
            doc?.querySelectorAll('[data-preview-active]').forEach(node => node.removeAttribute('data-preview-active'));
            const target = doc?.querySelector(config.blocks[id].selector);
            if (target) {target.setAttribute('data-preview-active', ''); target.scrollIntoView({block: 'center', behavior: 'instant'});}
        }
    }
    function selectKey(key, keys = [key]) {
        const node = fields.get(key);
        if (!node) return;
        group = node.dataset.group; selectedKeys = keys; activeKey = key; search.value = '';
        document.getElementById('editor-selection-title').textContent = node.querySelector('.editor-field-label').textContent;
        filterFields(); setTab('fields'); node.scrollIntoView({block: 'nearest', behavior: 'instant'});
    }
    function findTarget(key) {
        const doc = frame.contentDocument;
        if (!doc) return null;
        const base = baseKey(key);
        let target = [...doc.querySelectorAll('[data-content-keys]')].find(node => node.dataset.contentKeys.split(/\s+/).includes(base));
        if (!target && imageTargets[base]) target = doc.querySelector(imageTargets[base]);
        if (!target) {
            const value = control(key)?.value?.replace(/\s+/g, ' ').trim();
            if (value) target = [...doc.querySelectorAll('main h1,main h2,main h3,main p,main span,footer p,footer a')].find(node => node.textContent.replace(/\s+/g, ' ').trim() === value);
        }
        return target;
    }
    function revealActive() {
        const doc = frame.contentDocument;
        if (!doc || !activeKey) return;
        doc.querySelectorAll('[data-preview-active]').forEach(node => node.removeAttribute('data-preview-active'));
        const target = findTarget(activeKey);
        if (!target) return;
        target.setAttribute('data-preview-active', '');
        frame.contentWindow.scrollTo({top: Math.max(0, target.getBoundingClientRect().top + frame.contentWindow.scrollY - frame.clientHeight / 3), behavior: 'instant'});
    }
    function syncLayout() {
        const input = document.getElementById('editor-layout');
        if (input) input.value = JSON.stringify([...list.children].map(node => ({id: node.dataset.blockId, visible: node.querySelector('[data-block-visible]').checked})));
        [...list.children].forEach((node, index) => {node.querySelector('[data-move-block="up"]').disabled = index === 0; node.querySelector('[data-move-block="down"]').disabled = index === list.children.length - 1;});
    }
    function applyUploads(doc) {
        uploads.forEach(({url}, key) => {
            const target = doc.querySelector(`[data-content-keys~="${key}"]`) || doc.querySelector(imageTargets[key]);
            if (!target) return;
            let img = target.tagName === 'IMG' ? target : target.querySelector('img');
            if (!img) {
                img = doc.createElement('img'); img.alt = ''; img.className = ({culture_image: 'culture-background', about_team_background_image: 'team-invite-background', home_journey_background_image: 'hp-journey-background'})[key] || 'editable-hero-image'; target.prepend(img);
                if (key === 'culture_image') target.classList.add('hp-culture-photo');
                if (key === 'about_team_background_image') target.classList.add('team-invite-photo');
                if (key === 'home_journey_background_image') target.classList.add('hp-process-photo');
            }
            img.src = url;
        });
    }
    function editText(node, key) {
        const input = control(key);
        if (!input || node.querySelector('[data-content-keys]')) return;
        node.contentEditable = 'plaintext-only'; node.focus();
        const original = input.value;
        const onInput = () => {input.value = node.innerText.replace(/\r\n/g, '\n'); markDirty();};
        const onKey = event => {
            if (event.key === 'Escape') {input.value = original; node.textContent = original; node.blur();}
            else if (event.key === 'Enter' && (event.ctrlKey || event.metaKey || input.tagName === 'INPUT')) {event.preventDefault(); node.blur();}
        };
        node.addEventListener('input', onInput); node.addEventListener('keydown', onKey);
        node.addEventListener('blur', () => {node.removeEventListener('input', onInput); node.removeEventListener('keydown', onKey); node.removeAttribute('contenteditable'); if (input.value !== original) schedule();}, {once: true});
    }
    frame.addEventListener('load', () => {
        const doc = frame.contentDocument;
        if (!doc?.body) return;
        const style = doc.createElement('style');
        style.textContent = '[data-content-keys]{cursor:pointer}[data-content-keys]:hover{outline:1px dashed #2580b4;outline-offset:3px}[data-preview-active]{outline:2px solid #2580b4!important;outline-offset:4px}[contenteditable]{cursor:text!important;min-height:1em}.site-reveal{opacity:1!important;transform:none!important}';
        doc.head.append(style); applyUploads(doc);
        Object.entries(imageTargets).forEach(([key, selector]) => {if (fields.has(key)) doc.querySelectorAll(selector).forEach(node => node.dataset.editorImageKey = key);});
        fields.forEach((field, key) => {
            if (!['text', 'textarea', 'email'].includes(field.dataset.type) || field.dataset.language === (locale.value === 'vi' ? 'en' : 'vi')) return;
            const target = findTarget(key);
            if (target && !target.dataset.contentKeys) target.dataset.contentKeys = baseKey(key);
        });
        frame.contentWindow.scrollTo(0, scrollTop); revealActive();
        doc.addEventListener('click', event => {
            if (event.target.closest('[contenteditable]')) return;
            event.preventDefault();
            const keyed = event.target.closest('[data-content-keys]');
            if (keyed) {
                const keys = keyed.dataset.contentKeys.split(/\s+/).map(localizedKey).filter(key => fields.has(key));
                const key = keys.find(item => ['text', 'textarea', 'email', 'image'].includes(fields.get(item).dataset.type)) || keys[0];
                if (key) {
                    selectKey(key, keys); doc.querySelectorAll('[data-preview-active]').forEach(node => node.removeAttribute('data-preview-active')); keyed.setAttribute('data-preview-active', '');
                    if (['text', 'textarea', 'email'].includes(fields.get(key).dataset.type)) editText(keyed, key); else control(key)?.focus();
                    return;
                }
            }
            const image = event.target.closest('[data-editor-image-key]');
            if (image) {selectKey(image.dataset.editorImageKey); control(image.dataset.editorImageKey)?.focus(); return;}
            for (const [id, block] of Object.entries(config.blocks)) if (event.target.closest(block.selector)) {selectBlock(id, false); break;}
        });
        doc.addEventListener('submit', event => event.preventDefault());
        frame.dataset.editorReady = 'true';
        status.textContent = dirty ? 'Đã cập nhật bản xem trước — chưa xuất bản.' : 'Bấm vào nội dung trên trang để bắt đầu chỉnh sửa.';
    });
    async function refresh() {
        pending?.abort(); pending = new AbortController();
        const current = ++revision, data = new FormData(form);
        data.delete('_method');
        for (const key of [...data.keys()]) if (key.startsWith('uploads[')) data.delete(key);
        data.set('preview_locale', locale.value); status.textContent = 'Đang cập nhật bản xem trước…';
        try {
            const response = await fetch(form.dataset.previewUrl, {method: 'POST', body: data, signal: pending.signal, headers: {Accept: 'application/json'}});
            if (!response.ok) {const error = await response.json().catch(() => ({})); throw new Error(Object.values(error.errors || {}).flat()[0] || 'Không tải được bản xem trước. Vui lòng thử lại.');}
            const html = await response.text(); if (current !== revision) return;
            scrollTop = frame.contentWindow?.scrollY || 0; delete frame.dataset.editorReady; frame.srcdoc = html;
            status.textContent = 'Đang dựng bản xem trước…';
        } catch (error) {if (error.name !== 'AbortError' && current === revision) status.textContent = error.message;}
    }
    function schedule() {clearTimeout(timer); pending?.abort(); revision++; timer = setTimeout(refresh, 500);}
    form.addEventListener('input', event => {if (event.target.name?.startsWith('settings[')) {markDirty(); schedule();}});
    form.addEventListener('change', event => {
        const key = event.target.name?.match(/^uploads\[([^\]]+)\]$/)?.[1];
        if (key) {
            if (uploads.has(key)) URL.revokeObjectURL(uploads.get(key).url); uploads.delete(key);
            const file = event.target.files[0];
            if (file && file.size > 5 * 1024 * 1024) {event.target.value = ''; status.textContent = 'Ảnh quá lớn. Vui lòng chọn ảnh tối đa 5 MB.'; return;}
            if (file) uploads.set(key, {url: URL.createObjectURL(file)});
            const thumbnail = fields.get(key).querySelector('[data-field-thumbnail]'); thumbnail.hidden = !file; if (file) thumbnail.src = uploads.get(key).url;
            activeKey = key; markDirty(); schedule();
        } else if (event.target.name?.startsWith('settings[')) {
            if (event.target.matches('[data-image-select]')) {
                const field = event.target.closest('[data-editor-field]');
                const imageKey = field.dataset.key;
                field.querySelector('input[type="file"]').value = '';
                if (uploads.has(imageKey)) {URL.revokeObjectURL(uploads.get(imageKey).url); uploads.delete(imageKey);}
                const thumbnail = field.querySelector('[data-field-thumbnail]');
                const url = event.target.selectedOptions[0]?.dataset.imageUrl;
                thumbnail.hidden = !url;
                if (url) thumbnail.src = url;
            }
            markDirty(); schedule();
        }
    });
    form.addEventListener('focusin', event => {const key = event.target.name?.match(/^settings\[([^\]]+)\]$/)?.[1]; if (key && !key.startsWith('page_layout_')) {activeKey = key; revealActive();}});
    form.addEventListener('submit', () => {submitting = true; clearTimeout(timer); saveState.textContent = 'Đang lưu…';});
    form.querySelectorAll('[data-editor-tab]').forEach(button => button.addEventListener('click', () => setTab(button.dataset.editorTab)));
    form.querySelectorAll('[data-select-block]').forEach(button => button.addEventListener('click', () => selectBlock(button.dataset.selectBlock)));
    list.addEventListener('change', event => {
        if (event.target.matches('[data-block-visible]') && event.target.checked && event.target.closest('[data-block-id]').dataset.blockId === 'people' && control('team_group_enabled')) control('team_group_enabled').value = '1';
        syncLayout(); markDirty(); schedule();
    });
    list.addEventListener('click', event => {
        const button = event.target.closest('[data-move-block]'); if (!button) return;
        const item = button.closest('[data-block-id]');
        if (button.dataset.moveBlock === 'up' && item.previousElementSibling) list.insertBefore(item, item.previousElementSibling);
        if (button.dataset.moveBlock === 'down' && item.nextElementSibling) list.insertBefore(item.nextElementSibling, item);
        syncLayout(); markDirty(); schedule(); button.focus();
    });
    list.addEventListener('dragstart', event => {dragItem = event.target.closest('[data-block-id]'); if (dragItem) {event.dataTransfer.setData('text/plain', dragItem.dataset.blockId); event.dataTransfer.effectAllowed = 'move'; dragItem.classList.add('is-dragging');}});
    list.addEventListener('dragover', event => {if (!dragItem) return; event.preventDefault(); const target = event.target.closest('[data-block-id]'); if (target && target !== dragItem) list.insertBefore(dragItem, event.clientY < target.getBoundingClientRect().top + target.offsetHeight / 2 ? target : target.nextElementSibling);});
    list.addEventListener('drop', event => {event.preventDefault(); syncLayout(); markDirty(); schedule();});
    list.addEventListener('dragend', () => {dragItem?.classList.remove('is-dragging'); dragItem = null; syncLayout(); markDirty(); schedule();});
    document.getElementById('editor-show-all').addEventListener('click', () => {group = 'all'; selectedKeys = []; search.value = ''; document.getElementById('editor-selection-title').textContent = 'Tất cả nội dung'; filterFields();});
    search.addEventListener('input', () => {group = 'all'; selectedKeys = []; filterFields();});
    locale.addEventListener('change', () => {if (activeKey) activeKey = localizedKey(baseKey(activeKey)); filterFields(); schedule();});
    document.getElementById('preview-device').addEventListener('change', event => frame.parentElement.classList.toggle('mobile', event.target.value === 'mobile'));
    const pageSelector = document.getElementById('editor-page'), originalPage = pageSelector.value;
    pageSelector.addEventListener('change', () => {if (dirty && !window.confirm('Có thay đổi chưa lưu. Bạn muốn rời trang này?')) {pageSelector.value = originalPage; return;} submitting = true; window.location.href = pageSelector.value;});
    window.addEventListener('beforeunload', event => {if (dirty && !submitting) {event.preventDefault(); event.returnValue = '';}});
    form.querySelectorAll('[data-open-media]').forEach(button => button.addEventListener('click', () => {mediaKey = button.dataset.openMedia; dialog.showModal();}));
    document.getElementById('editor-close-media').addEventListener('click', () => dialog.close());
    document.getElementById('editor-media-search').addEventListener('input', event => dialog.querySelectorAll('[data-pick-media]').forEach(button => button.hidden = !button.dataset.pickMedia.toLocaleLowerCase().includes(event.target.value.toLocaleLowerCase())));
    dialog.querySelectorAll('[data-pick-media]').forEach(button => button.addEventListener('click', () => {
        const field = fields.get(mediaKey); control(mediaKey).value = button.dataset.pickMedia; field.querySelector('input[type="file"]').value = '';
        if (uploads.has(mediaKey)) {URL.revokeObjectURL(uploads.get(mediaKey).url); uploads.delete(mediaKey);}
        const thumbnail = field.querySelector('[data-field-thumbnail]'); thumbnail.src = button.dataset.imageUrl; thumbnail.hidden = false; activeKey = mediaKey;
        dialog.close(); markDirty(); schedule();
    }));
    form.querySelectorAll('[data-text-color-picker]').forEach(picker => picker.addEventListener('input', () => {const input = picker.parentElement.querySelector('input[name]'); input.value = picker.value; input.dispatchEvent(new Event('input', {bubbles: true}));}));
    form.querySelectorAll('[data-image-adjustment]').forEach(slider => {
        const input = slider.parentElement.querySelector('input[name]');
        slider.addEventListener('input', () => {input.value = slider.value; activeKey = input.name.match(/\[([^\]]+)\]/)[1]; input.dispatchEvent(new Event('input', {bubbles: true}));});
        input.addEventListener('input', () => {if (input.value !== '') slider.value = input.value;});
    });
    form.querySelectorAll('[data-reset-style]').forEach(button => button.addEventListener('click', () => {const input = button.parentElement.querySelector('input[name]'); input.value = ''; input.dispatchEvent(new Event('input', {bubbles: true}));}));
    form.querySelectorAll('[data-website-color]').forEach(picker => {
        const hex = picker.parentElement.querySelector('[data-color-hex]'); picker.addEventListener('input', () => hex.value = picker.value);
        hex.addEventListener('input', () => {const valid = /^#[0-9a-fA-F]{6}$/.test(hex.value); hex.setCustomValidity(valid ? '' : 'Nhập mã màu gồm # và 6 ký tự HEX.'); if (valid) {picker.value = hex.value; markDirty(); schedule();}});
    });
    document.getElementById('reset-website-colors')?.addEventListener('click', () => {form.querySelectorAll('[data-website-color]').forEach(picker => {picker.value = picker.dataset.default; picker.parentElement.querySelector('[data-color-hex]').value = picker.value;}); markDirty(); schedule();});
    syncLayout(); filterFields(); setTab(list.children.length ? 'blocks' : 'fields'); refresh();
})();
