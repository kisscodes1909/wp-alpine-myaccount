import toast from './toast.js';
import popup from './popup.js';
import loader from './loader.js';

export function registerSharedStores() {
    Alpine.store('toast', toast);
    Alpine.store('popup', popup);
    Alpine.store('loader', loader);
}
