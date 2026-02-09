/**
 * Alpine.js bundle entry
 * Includes: Alpine, Yup, and theme Alpine stores/components/directives
 */

import Alpine from 'alpinejs';
import * as yup from 'yup';

// Expose for code that uses window.Alpine / window.yup
window.Alpine = Alpine;
window.yup = yup;

import { registerStores } from './stores/index.js';
import { registerValidationDirectives } from './directives/validate.js';
import { registerFormComponents } from './components/forms/index.js';
import { registerAccountComponents } from './components/account/index.js';

// Register stores, directives, components (bundle runs in one file, Alpine already available)
registerStores();
registerValidationDirectives();
registerFormComponents();
registerAccountComponents();

if (window.location.hostname === 'localhost' || window.location.hostname.includes('local')) {
    console.log('✅ Alpine.js initialization complete');
    console.log('📦 Stores:', Object.keys(Alpine.store));
    console.log('🎨 Components:', Object.keys(Alpine._data || {}));
}

// Start Alpine (required when using npm package)
Alpine.start();
