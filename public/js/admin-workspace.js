(() => {
    'use strict';
    // Recheck authentication when Back restores an old admin page from browser memory.
    window.addEventListener('pageshow', event => {
        if (event.persisted) window.location.reload();
    });
    const root = document.documentElement;
    const toggle = document.querySelector('[data-toggle-menu]');
    const overlay = document.querySelector('[data-close-menu]');
    const sidebar = document.getElementById('sidebar');
    const mobile = window.matchMedia('(max-width: 1050px)');
    const preferenceKey = `recruitment-menu-${document.body.dataset.adminUser}`;
    const readPreference = key => {
        try { return localStorage.getItem(key); } catch { return null; }
    };
    const savePreference = (key, value) => {
        try { localStorage.setItem(key, value); } catch { /* Storage may be disabled. */ }
    };
    if (readPreference(preferenceKey) === 'collapsed') document.body.classList.add('menu-collapsed');
    document.querySelectorAll('.nav-group').forEach((group, index) => {
        const key = `${preferenceKey}-group-${index}`;
        if (readPreference(key) === 'closed') group.open = false;
        group.addEventListener('toggle', () => savePreference(key, group.open ? 'open' : 'closed'));
    });
    const updateMenu = () => {
        const opened = mobile.matches ? document.body.classList.contains('menu-open') : !document.body.classList.contains('menu-collapsed');
        toggle?.setAttribute('aria-expanded', String(opened));
        if (overlay) overlay.hidden = !mobile.matches || !opened;
        if (sidebar) sidebar.inert = !opened;
    };
    toggle?.addEventListener('click', () => {
        document.body.classList.toggle(mobile.matches ? 'menu-open' : 'menu-collapsed');
        if (!mobile.matches) savePreference(preferenceKey, document.body.classList.contains('menu-collapsed') ? 'collapsed' : 'open');
        updateMenu();
        if (mobile.matches && document.body.classList.contains('menu-open')) sidebar?.querySelector('a')?.focus();
    });
    const closeMenu = () => {
        document.body.classList.remove('menu-open');
        updateMenu();
        toggle?.focus();
    };
    overlay?.addEventListener('click', closeMenu);
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            if (document.body.classList.contains('menu-open')) closeMenu();
            document.querySelectorAll('.profile-menu[open]').forEach(menu => menu.open = false);
        }
    });
    document.addEventListener('click', event => {
        document.querySelectorAll('.profile-menu[open]').forEach(menu => {
            if (!menu.contains(event.target)) menu.open = false;
        });
    });
    mobile.addEventListener('change', updateMenu);
    updateMenu();

    const colorInput = document.getElementById('admin-color');
    const hexInput = document.getElementById('admin-color-hex');
    const fontInput = document.getElementById('admin-font');
    const sizeInput = document.getElementById('admin-font-size');
    const setContrast = color => {
        if (!/^#[0-9a-f]{6}$/i.test(color)) return;
        const channels = [1, 3, 5].map(index => parseInt(color.slice(index, index + 2), 16) / 255).map(value => value <= .04045 ? value / 12.92 : ((value + .055) / 1.055) ** 2.4);
        const luminance = .2126 * channels[0] + .7152 * channels[1] + .0722 * channels[2];
        root.style.setProperty('--on-primary', luminance > .179 ? '#18243a' : '#ffffff');
    };
    setContrast(getComputedStyle(root).getPropertyValue('--primary').trim());
    const setColor = color => {
        if (!/^#[0-9a-f]{6}$/i.test(color)) return;
        root.style.setProperty('--primary', color);
        setContrast(color);
        if (colorInput) colorInput.value = color;
        if (hexInput) hexInput.value = color.toUpperCase();
        document.querySelectorAll('[data-color]').forEach(button => button.setAttribute('aria-pressed', String(button.dataset.color.toLowerCase() === color.toLowerCase())));
    };
    document.querySelectorAll('[data-color]').forEach(button => button.addEventListener('click', () => setColor(button.dataset.color)));
    colorInput?.addEventListener('input', () => setColor(colorInput.value));
    hexInput?.addEventListener('input', () => setColor(hexInput.value));
    const fonts = {system: '"Segoe UI", system-ui, sans-serif', arial: 'Arial, sans-serif', serif: 'Georgia, "Times New Roman", serif'};
    const setTypography = () => {
        root.style.setProperty('--app-font', fonts[fontInput.value] || fonts.system);
        root.style.setProperty('--font-scale', Number(sizeInput.value) / 16);
    };
    fontInput?.addEventListener('change', setTypography);
    sizeInput?.addEventListener('change', setTypography);
    document.querySelector('[data-reset-theme]')?.addEventListener('click', () => {
        setColor('#2563eb');
        fontInput.value = 'system';
        sizeInput.value = '16';
        setTypography();
    });
    if (colorInput) {
        setColor(colorInput.value);
        setTypography();
    }
    document.querySelector('[data-logo-input]')?.addEventListener('change', event => {
        const file = event.target.files[0];
        if (!file || !['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) return;
        const reader = new FileReader();
        reader.addEventListener('load', () => document.querySelector('[data-logo-preview]').src = reader.result);
        reader.readAsDataURL(file);
    });
})();

document.getElementById('hero-year')?.addEventListener('change', event => {
    document.querySelectorAll('[data-media-year]').forEach(card => {
        card.hidden = Boolean(event.target.value && card.dataset.mediaYear !== event.target.value);
    });
});

document.querySelectorAll('[data-confirm-delete-media]').forEach(form => {
    form.addEventListener('submit', event => {
        if (!window.confirm('Xóa ảnh này khỏi kho? Thao tác này không thể hoàn tác.')) {
            event.preventDefault();
        }
    });
});
