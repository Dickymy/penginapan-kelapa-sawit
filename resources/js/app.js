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

// Global store for mobile navigation drawer
Alpine.store('mobileNav', {
    open: false,
    toggle() {
        this.open = !this.open;
    },
    close() {
        this.open = false;
    },
});

window.Alpine = Alpine;
Alpine.start();

// Blur native select inputs after value change so their :focus styling (like animated arrows) resets
document.addEventListener('change', (e) => {
    if (e.target && e.target.tagName && e.target.tagName.toLowerCase() === 'select') {
        e.target.blur();
    }
});

// Global page progress bar on navigation
let progressWatchdogTimer = null;

const showProgressBar = () => {
    let bar = document.getElementById('page-progress-bar');
    if (!bar) {
        bar = document.createElement('div');
        bar.id = 'page-progress-bar';
        document.body.prepend(bar);
    }
    clearTimeout(progressWatchdogTimer);
    bar.style.width = '0%';
    bar.style.opacity = '1';
    setTimeout(() => { bar.style.width = '75%'; }, 20);

    // Watchdog fallback: reset bar if page navigation does not occur within 3 seconds
    progressWatchdogTimer = setTimeout(() => {
        completeProgressBar();
    }, 3000);
};

const completeProgressBar = () => {
    clearTimeout(progressWatchdogTimer);
    const bar = document.getElementById('page-progress-bar');
    if (bar) {
        bar.style.width = '100%';
        setTimeout(() => {
            bar.style.opacity = '0';
            setTimeout(() => { bar.style.width = '0%'; }, 300);
        }, 150);
    }
};

window.addEventListener('pageshow', completeProgressBar);
window.addEventListener('hashchange', completeProgressBar);

document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (!link) return;
    const href = link.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('tel:') || href.startsWith('mailto:') || link.target === '_blank' || link.hasAttribute('download')) {
        return;
    }

    // Ignore clicks if modifier keys are pressed or default is already prevented
    if (e.ctrlKey || e.metaKey || e.shiftKey || e.defaultPrevented) {
        return;
    }

    // Ignore in-page anchor jumps or links to the exact same page
    if (link.origin === window.location.origin) {
        const isSamePath = link.pathname.replace(/\/+$/, '') === window.location.pathname.replace(/\/+$/, '');
        const isSameSearch = (link.search || '') === (window.location.search || '');
        if (isSamePath && isSameSearch) {
            // Same page anchor navigation (e.g. /#cari-kamar) or click on current link
            return;
        }
        showProgressBar();
    }
});

