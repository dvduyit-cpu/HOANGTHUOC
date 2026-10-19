(() => {
    'use strict';
    const homeLink = document.querySelector('[data-home-link]');
    const careersLink = document.querySelector('[data-careers-link]');
    if (!homeLink || !careersLink) return;

    const updateNavigation = () => {
        document.querySelectorAll('.primary-nav a').forEach(link => {
            if (link.classList.contains('active')) {
                link.setAttribute('aria-current', 'page');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    };

    window.addEventListener('hashchange', updateNavigation);
    window.addEventListener('pageshow', updateNavigation);
    updateNavigation();
})();
