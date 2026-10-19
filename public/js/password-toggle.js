(() => {
    'use strict';
    const eye = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/><path class="password-eye-slash" d="m3 3 18 18"/></svg>';
    document.querySelectorAll('input[type="password"]').forEach((input, index) => {
        if (!input.id) input.id = `password-field-${index}`;
        const wrapper = document.createElement('span');
        wrapper.className = 'password-field';
        input.before(wrapper);
        wrapper.append(input);
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'password-toggle';
        button.innerHTML = eye;
        button.setAttribute('aria-controls', input.id);
        button.setAttribute('aria-label', 'Hiện mật khẩu');
        button.setAttribute('aria-pressed', 'false');
        wrapper.append(button);
        const hide = () => {
            input.type = 'password';
            button.setAttribute('aria-label', 'Hiện mật khẩu');
            button.setAttribute('aria-pressed', 'false');
        };
        button.addEventListener('click', () => {
            const visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            button.setAttribute('aria-label', visible ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
            button.setAttribute('aria-pressed', String(visible));
        });
        input.form?.addEventListener('submit', hide);
        window.addEventListener('pageshow', hide);
    });
})();
