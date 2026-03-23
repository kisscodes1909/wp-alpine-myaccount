import viewOrderReturns, { viewOrderReturnsForm } from './components/viewOrderReturns.js';

export function registerReturnsModuleComponents() {
    const AlpineInstance = window.Alpine;

    if (!AlpineInstance || typeof AlpineInstance.data !== 'function') {
        return;
    }

    AlpineInstance.data('viewOrderReturns', viewOrderReturns);
    AlpineInstance.data('viewOrderReturnsForm', viewOrderReturnsForm);
}

export function initReturnsModuleTrees() {
    const AlpineInstance = window.Alpine;

    if (!AlpineInstance || typeof AlpineInstance.initTree !== 'function') {
        return;
    }

    document.querySelectorAll('[data-ma-returns-module]').forEach((element) => {
        AlpineInstance.initTree(element);
    });
}
