
import './bootstrap';
import '@splidejs/splide/css';
import Splide from '@splidejs/splide';
import './welcome.js';
import Alpine from 'alpinejs';

window.Alpine = Alpine;



Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    mountSplide();
});
function mountSplide() {
    const el = document.getElementById('photo-splide');
    if (el && !el.classList.contains('is-initialized')) {
        new Splide(el, {
            type: 'loop',
            perPage: 4,
            perMove: 1,
            gap: '1.5rem',
            arrows: true,
            pagination: true,
            drag: true,
            focus: 'center',
            breakpoints: {
                900: { perPage: 2 },
                600: { perPage: 1 },
            },
        }).mount();
    }
}