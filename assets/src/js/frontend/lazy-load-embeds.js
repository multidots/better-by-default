/**
 * Lazy load embeds.
 */
document.addEventListener("DOMContentLoaded", function() {
    const lazyLoadIframes = document.querySelectorAll('iframe[data-src]');

    const loadIframe = (iframe) => {
        iframe.src = iframe.dataset.src;
        iframe.classList.remove('lazy');
    };

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    loadIframe(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        });

        lazyLoadIframes.forEach(iframe => {
            observer.observe(iframe);
        });
    } else {
        // Fallback for browsers that do not support IntersectionObserver
        lazyLoadIframes.forEach(iframe => loadIframe(iframe));
    }
});
