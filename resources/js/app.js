import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import intersect from '@alpinejs/intersect';

Alpine.plugin(focus);
Alpine.plugin(intersect);

// Global store for checkout form submission state (prevents double-submit)
// Uses form's @submit event so it only triggers AFTER browser validation passes
Alpine.store('checkoutForm', {
    submitting: false,
});

window.Alpine = Alpine;
Alpine.start();

// Blur native select inputs after value change so their :focus styling (like animated arrows) resets
document.addEventListener('change', (e) => {
    if (e.target && e.target.tagName && e.target.tagName.toLowerCase() === 'select') {
        e.target.blur();
    }
});
