/**
 * Account Components Registry
 * Centralized registration of all account-related Alpine components
 */
import viewOrderReturns, { viewOrderReturnsForm } from './viewOrderReturns.js';

export function registerAccountComponents() {
    const AlpineInstance = window.Alpine;

    if (!AlpineInstance || typeof AlpineInstance.data !== 'function') {
        return;
    }

    AlpineInstance.data('viewOrderReturns', viewOrderReturns);
    AlpineInstance.data('viewOrderReturnsForm', viewOrderReturnsForm);
}
