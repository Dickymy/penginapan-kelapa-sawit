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
