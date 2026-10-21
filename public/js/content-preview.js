(() => {
    const form = document.getElementById('content-editor-form');
    if (!form) return;
    const frame = document.getElementById('content-preview-frame');
    const status = document.getElementById('preview-status');
    const locale = document.getElementById('preview-locale');
    let timer, pending, revision = 0, scrollTop = 0, activeKey = null;
    function revealActive() {
        const doc = frame.contentDocument;
        if (!doc || !activeKey) return;
        doc.querySelectorAll('[data-preview-active]').forEach(node => node.removeAttribute('data-preview-active'));
        const contentKey = activeKey.replace(/_alignment$/, '');
        const keys = [contentKey, contentKey.replace(/_en$/, '_vi')];
        let target = [...doc.querySelectorAll('[data-content-keys]')].find(node =>
            keys.some(key => node.dataset.contentKeys.split(/\s+/).includes(key)));
        const imageTargets = {
            home_hero_image: '.hp-hero', culture_image: '.hp-culture',
            home_process_image: '#cung-dong-hanh', home_standards_white_background: '#cung-dong-hanh', home_updates_image: '.hp-updates',
            about_hero_image: '.page-hero-about', careers_hero_image: '.careers-hero', news_hero_image: '.news-hero',
            about_story_image: '.about-story-image',
            contact_hero_image: '.contact-hero',
            team_portrait_image: '.team-portrait, .team-founder, .team-story',
            web_primary_color: '.hp-button:not(.hp-button-white), .hp-inline-link',
            web_heading_color: '.hp-intro h2', web_text_color: '.hp-intro-copy p',
            web_background_color: '.hp-intro', web_surface_color: '.hp-culture',
            web_dark_color: '.hp-hero',
        };
        if (!target && imageTargets[activeKey]) target = doc.querySelector(imageTargets[activeKey]);
        if (!target) {
            const control = form.elements.namedItem(`settings[${activeKey}]`);
            const value = control?.value?.replace(/\s+/g, ' ').trim();
            if (value) target = [...doc.querySelectorAll('main h1,main h2,main h3,main h4,main p,main span,footer p,footer a')]
                .find(node => node.textContent.replace(/\s+/g, ' ').trim() === value);
        }
        if (!target) return;
        target.setAttribute('data-preview-active', '');
        // Scroll only inside the demo so the editing control keeps focus.
        const top = target.getBoundingClientRect().top + frame.contentWindow.scrollY;
        frame.contentWindow.scrollTo({top: Math.max(0, top - frame.clientHeight / 3), behavior: 'instant'});
    }
    frame.addEventListener('load', () => {
        const doc = frame.contentDocument;
        if (!doc) return;
        const style = doc.createElement('style');
        style.textContent = '[data-preview-active]{outline:3px solid #f59e0b!important;outline-offset:5px;background-color:#fef3c7!important;box-shadow:0 0 0 8px #f59e0b22!important}';
        doc.head.append(style);
        frame.contentWindow.scrollTo(0, scrollTop);
        revealActive();
        doc.addEventListener('click', event => {
            if (event.target.closest('a')) event.preventDefault();
        });
        doc.addEventListener('submit', event => event.preventDefault());
    });
    async function refresh() {
        pending?.abort();
        pending = new AbortController();
        const current = ++revision;
        const data = new FormData(form);
        data.delete('_method');
        for (const key of [...data.keys()]) {
            if (key.startsWith('uploads[')) data.delete(key);
        }
        data.set('preview_locale', locale.value);
        status.textContent = 'Đang cập nhật bản xem trước…';
        try {
            const response = await fetch(form.dataset.previewUrl, {
                method: 'POST', body: data, signal: pending.signal,
                headers: {Accept: 'application/json'},
            });
            if (!response.ok) {
                const error = await response.json().catch(() => ({}));
                throw new Error(Object.values(error.errors || {}).flat()[0] || 'Không tải được bản xem trước. Vui lòng tải lại trang.');
            }
            const html = await response.text();
            if (current !== revision) return;
            scrollTop = frame.contentWindow?.scrollY || 0;
            frame.srcdoc = html;
            status.textContent = 'Đã cập nhật bản xem trước — chưa lưu.';
        } catch (error) {
            if (error.name !== 'AbortError' && current === revision) status.textContent = error.message;
        }
    }
    function schedule() {
        clearTimeout(timer);
        pending?.abort();
        revision++;
        timer = setTimeout(refresh, 400);
    }
    form.addEventListener('input', schedule);
    form.querySelectorAll('[data-website-color]').forEach(picker => {
        const hex = picker.parentElement.querySelector('[data-color-hex]');
        picker.addEventListener('input', () => { hex.value = picker.value; });
        hex.addEventListener('input', () => {
            const valid = /^#[0-9a-fA-F]{6}$/.test(hex.value);
            hex.setCustomValidity(valid ? '' : 'Nhập mã màu gồm # và 6 ký tự HEX.');
            if (valid) {
                picker.value = hex.value;
                activeKey = picker.name.match(/\[([^\]]+)\]/)[1];
            }
        });
        hex.addEventListener('focus', () => {
            activeKey = picker.name.match(/\[([^\]]+)\]/)[1];
            revealActive();
        });
    });
    document.getElementById('reset-website-colors')?.addEventListener('click', () => {
        form.querySelectorAll('[data-website-color]').forEach(picker => {
            picker.value = picker.dataset.default;
            const hex = picker.parentElement.querySelector('[data-color-hex]');
            hex.value = picker.value;
            hex.setCustomValidity('');
        });
        schedule();
    });
    form.addEventListener('change', schedule);
    form.addEventListener('focusin', event => {
        const match = event.target.name?.match(/^(?:settings|uploads)\[([^\]]+)\]$/);
        if (!match) return;
        activeKey = match[1];
        const language = activeKey.match(/_(vi|en)$/)?.[1];
        if (language && locale.value !== language) {
            locale.value = language;
            schedule();
        } else revealActive();
    });
    locale.addEventListener('change', schedule);
    document.getElementById('preview-device').addEventListener('change', event => {
        frame.parentElement.classList.toggle('mobile', event.target.value === 'mobile');
    });
    refresh();
})();
