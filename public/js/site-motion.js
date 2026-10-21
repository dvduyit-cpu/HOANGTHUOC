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

    const heroHeading = document.querySelector('.hp-hero h1');
    let headingAnimation;
    const animateHeading = () => {
        headingAnimation?.cancel();
        if (!heroHeading || preference.matches) return;
        headingAnimation = heroHeading.animate([
            {opacity: 0, transform: 'translateX(-100px)'},
            {opacity: 1, transform: 'translateX(0)'},
        ], {
            duration: 1800,
            delay: 150,
            easing: 'cubic-bezier(.22,.61,.36,1)',
            fill: 'backwards',
        });
    };
    animateHeading();
    // Observe the stationary container so the title's own movement cannot retrigger it.
    if (heroHeading && 'IntersectionObserver' in window) {
        let headingInView = true;
        const headingObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !headingInView) animateHeading();
                if (!entry.isIntersecting) headingAnimation?.cancel();
                headingInView = entry.isIntersecting;
            });
        }, {threshold: 0});
        headingObserver.observe(heroHeading.closest('.hp-hero-content'));
    }
    window.addEventListener('pageshow', event => {
        if (event.persisted) animateHeading();
    });
    window.addEventListener('beforeprint', () => headingAnimation?.cancel());
    preference.addEventListener('change', event => {
        if (event.matches) headingAnimation?.cancel();
    });

    if (preference.matches || !('IntersectionObserver' in window)) return;
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (preference.matches) return;
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
            } else if (!entry.target.contains(document.activeElement)) {
                entry.target.classList.remove('is-visible');
            }
        });
    }, {threshold: 0, rootMargin: '0px 0px -35px 0px'});
    const selector = [
        '.hp-intro > div', '.hp-values article', '.hp-culture-inner > div',
        '.hp-section-head', '.hp-steps li', '.hp-news-item', '.hp-final > *',
        '.job-card', '.news-card', '.about-story > *', '.team-story > *', '.team-group > *', '.value-grid article',
        '.contact-intro', '.contact-copy', '.contact-form-wrap', '.about-products > .eyebrow', '.about-products > h2', '.about-products .team-product-card',
        '.page-hero .container > *', '.page-title .container > *',
        '.values > .container > .eyebrow', '.team-invite .container > *', '.journey > .container > .eyebrow', '.journey h2', '.timeline-scroll',
        '.positions-section > .section-head', '.positions-section > .search',
        '.article-header', '.article-cover', '.article-content', '.article-end',
        '.detail-card .hero-image', '.detail-card .card-body > *', '.apply-card',
        '.pagination', '.jobs-grid > .empty', '.news-grid > .empty',
        '.careers-intro > p', '.news-intro > p',
    ].join(',');
    const targets = [...document.querySelectorAll(selector)].filter(element => !element.classList.contains('visually-hidden'));
    targets.forEach(element => {
        // Content with keyboard focus stays visible throughout interaction.
        if (element.contains(document.activeElement)) return;
        if (element.matches('.about-story > :first-child, .team-story > :first-child, .team-group > :first-child, .contact-intro, .contact-copy, .page-hero h1, .page-title h1, .article-header, .positions-section > .section-head')) {
            element.classList.add('site-reveal-from-left');
        }
        if (element.matches('.about-story > :last-child, .team-story > :last-child, .team-group > :last-child, .contact-form-wrap, .apply-card, .careers-hero p, .careers-intro > p, .news-intro > p, .positions-section > .search')) {
            element.classList.add('site-reveal-from-right');
        }
        if (element.matches('.hp-intro > div:first-child')) {
            element.classList.add('site-reveal-left');
        }
        const index = [...element.parentElement.children].indexOf(element);
        const stagger = element.matches('.hp-values article') ? 350 : (element.matches('.team-product-card') ? 180 : 240);
        element.style.setProperty('--reveal-delay', `${Math.min(index, 3) * stagger}ms`);
        element.classList.add('site-reveal');
        observer.observe(element);
    });
    const revealAll = () => {
        targets.forEach(element => element.classList.add('is-visible'));
    };
    preference.addEventListener('change', event => {
        if (event.matches) revealAll();
    });
    document.addEventListener('focusin', event => {
        event.target.closest('.site-reveal')?.classList.add('is-visible');
    });
    window.addEventListener('beforeprint', revealAll);
    window.addEventListener('pageshow', event => {
        if (event.persisted) revealAll();
    });
})();
