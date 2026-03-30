document.addEventListener('DOMContentLoaded', () => {
    /* SCROLL REVEAL */
    const reveals = document.querySelectorAll('.reveal, .reveal-stagger');
    if (reveals.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        
        reveals.forEach(el => observer.observe(el));
    }

    /* HEADER OPACITY ON SCROLL */
    const header = document.querySelector('header');
    if (header && !document.body.classList.contains('font-sans') /* não é no dashboard */) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 40) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        }, { passive: true });
    }
});
