(() => {
    'use strict';
    const preference = window.matchMedia('(prefers-reduced-motion: reduce)');
    const header = document.querySelector('.site-header');
    let queued = false;
    const updateHeader = () => {
        header?.classList.toggle('is-scrolled', window.scrollY > 16);
        queued = false;
    };
    window.addEventListener('scroll', () => {
        if (!queued) {
            queued = true;
            window.requestAnimationFrame(updateHeader);
        }
    }, {passive: true});
    updateHeader();

    if (preference.matches || !('IntersectionObserver' in window)) return;
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, {threshold: 0, rootMargin: '0px 0px -35px 0px'});
    const selector = [
        '.hp-intro > div', '.hp-values article', '.hp-culture-inner > div',
        '.hp-section-head', '.hp-steps li', '.hp-news-item', '.hp-final',
        '.job-card', '.news-card', '.about-story > div', '.value-grid article',
        '.contact-copy', '.contact-form-wrap',
    ].join(',');
    const targets = [...document.querySelectorAll(selector)];
    targets.forEach(element => {
        // Never hide content already on screen or containing the keyboard focus.
        if (element.getBoundingClientRect().top < window.innerHeight || element.contains(document.activeElement)) return;
        const index = [...element.parentElement.children].indexOf(element);
        element.style.setProperty('--reveal-delay', `${Math.min(index, 2) * 70}ms`);
        element.classList.add('site-reveal');
        observer.observe(element);
    });
    const revealAll = () => {
        observer.disconnect();
        targets.forEach(element => element.classList.add('is-visible'));
    };
    preference.addEventListener('change', event => {
        if (event.matches) revealAll();
    });
    window.addEventListener('beforeprint', revealAll);
    window.addEventListener('pageshow', event => {
        if (event.persisted) revealAll();
    });
})();
