/**
 * Alpine.js bundle entry
 * Includes: Alpine, Yup, and theme Alpine stores/components/directives
 *
 * Performance: marks/measures are recorded for alpine-register, alpine-start, alpine-total.
 * - In DevTools: Performance tab → record load → look at "User Timing" in the timeline.
 * - In Console (localhost): durations are logged after init.
 */

import Alpine from 'alpinejs';
import * as yup from 'yup';

// Expose for code that uses window.Alpine / window.yup
window.Alpine = Alpine;
window.yup = yup;

// Performance: mark start of our init (after Alpine/Yup load)
if (typeof performance !== 'undefined' && performance.mark) {
    performance.mark('alpine-bundle-start');
}

import { registerStores } from './stores/index.js';
import { registerValidationDirectives } from './directives/validate.js';
import { registerLoadingDirective } from './directives/loading.js';
import { registerFormComponents } from './components/forms/index.js';
import { registerAccountComponents } from './components/account/index.js';

// Register stores, directives, components (bundle runs in one file, Alpine already available)
registerStores();
registerValidationDirectives();
registerLoadingDirective();
registerFormComponents();
registerAccountComponents();

if (typeof performance !== 'undefined' && performance.mark) {
    performance.mark('alpine-register-done');
}

// Start Alpine (required when using npm package)
Alpine.start();

if (typeof performance !== 'undefined' && performance.mark) {
    performance.mark('alpine-start-done');
    performance.measure('alpine-register', 'alpine-bundle-start', 'alpine-register-done');
    performance.measure('alpine-start', 'alpine-register-done', 'alpine-start-done');
    performance.measure('alpine-total', 'alpine-bundle-start', 'alpine-start-done');
}

// Show init summary in console on local dev (localhost, *.local, *.test) or when ?alpine_debug=1
const isLocalHost = window.location.hostname === 'localhost' || window.location.hostname.includes('local') || window.location.hostname.endsWith('.test');
const debugQuery = new URLSearchParams(window.location.search).get('alpine_debug');
if (isLocalHost || debugQuery === '1') {
    console.log('✅ Alpine.js initialization complete');
    console.log('📦 Stores:', Object.keys(Alpine.store));
    console.log('🎨 Components:', Object.keys(Alpine._data || {}));
    if (typeof performance !== 'undefined' && performance.getEntriesByType) {
        const measures = performance.getEntriesByType('measure').filter((m) => m.name.startsWith('alpine-'));
        measures.forEach((m) => console.log(`⏱ ${m.name}: ${m.duration.toFixed(2)} ms`));
    }
}
