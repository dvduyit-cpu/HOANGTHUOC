(() => {
    'use strict';
    const preference = window.matchMedia('(prefers-reduced-motion: reduce)');
    document.querySelectorAll('[data-product-slider]').forEach(slider => {
        const track = slider.querySelector('.product-slider-track');
        const controls = slider.querySelector('.product-slider-controls');
        const previous = slider.querySelector('[data-slide-prev]');
        const next = slider.querySelector('[data-slide-next]');
        const counter = slider.querySelector('[data-slide-counter]');
        const cards = [...track.querySelectorAll('.team-product-card')];
        const update = () => {
            const end = track.scrollWidth - track.clientWidth;
            controls.hidden = end <= 2;
            previous.disabled = track.scrollLeft <= 2;
            next.disabled = track.scrollLeft >= end - 2;
            const step = cards[0]?.getBoundingClientRect().width + parseFloat(getComputedStyle(track).gap || 0);
            const index = step ? Math.min(cards.length - 1, Math.max(0, Math.round(track.scrollLeft / step))) : 0;
            counter.textContent = `${index + 1} / ${cards.length}`;
        };
        const move = direction => {
            const card = track.querySelector('.team-product-card');
            if (!card) return;
            track.scrollBy({left: direction * (card.getBoundingClientRect().width + parseFloat(getComputedStyle(track).gap || 0)), behavior: preference.matches ? 'instant' : 'smooth'});
        };
        previous.addEventListener('click', () => move(-1));
        next.addEventListener('click', () => move(1));
        track.addEventListener('scroll', update, {passive: true});
        track.addEventListener('keydown', event => {
            if (event.target !== track || !['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
            event.preventDefault();
            move(event.key === 'ArrowLeft' ? -1 : 1);
        });
        window.addEventListener('resize', update);
        if ('ResizeObserver' in window) new ResizeObserver(update).observe(track);
        update();
    });
})();
