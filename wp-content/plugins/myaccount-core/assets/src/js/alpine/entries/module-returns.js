import viewOrderReturns, { viewOrderReturnsForm } from '../components/account/viewOrderReturns.js';

function initReturnsModuleTrees() {
    if (!window.Alpine || typeof window.Alpine.initTree !== 'function') {
        return;
    }

    document.querySelectorAll('[data-ma-returns-module]').forEach((element) => {
        window.Alpine.initTree(element);
    });
}

if (window.Alpine && typeof window.Alpine.data === 'function') {
    window.Alpine.data('viewOrderReturns', viewOrderReturns);
    window.Alpine.data('viewOrderReturnsForm', viewOrderReturnsForm);
    initReturnsModuleTrees();
}
