/**
 * Alpine Stores Registry
 * Centralized registration of all Alpine stores
 */
import toast from './toast.js';
import popup from './popup.js';
import loader from './loader.js';
import wishlist from './wishlist.js';
import userAddress from './userAddress.js';

export function registerStores() {
    Alpine.store('toast', toast);
    Alpine.store('popup', popup);
    Alpine.store('loader', loader);
    Alpine.store('wishlist', wishlist);
    Alpine.store('userAddress', userAddress);
}
