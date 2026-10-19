(() => {
    'use strict';
    const homeLink = document.querySelector('[data-home-link]');
    const careersLink = document.querySelector('[data-careers-link]');
    if (!homeLink || !careersLink) return;

    const updateNavigation = () => {
        if (window.location.pathname === new URL(homeLink.href).pathname) {
            const careersSelected = window.location.hash === new URL(careersLink.href).hash;
            homeLink.classList.toggle('active', !careersSelected);
            careersLink.classList.toggle('active', careersSelected);
        }
        document.querySelectorAll('.primary-nav a').forEach(link => {
            if (link.classList.contains('active')) {
                link.setAttribute('aria-current', link === careersLink && window.location.hash ? 'location' : 'page');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    };

    window.addEventListener('hashchange', updateNavigation);
    window.addEventListener('pageshow', updateNavigation);
    updateNavigation();
})();
