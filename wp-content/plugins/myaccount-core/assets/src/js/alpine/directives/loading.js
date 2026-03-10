/**
 * Alpine directive: x-loading
 * Reusable button loading state - shows spinner + label, disables button.
 */

const SPINNER_SVG = `<span class="loading-icon ma-btn-loading-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="ma-btn-loading-svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle class="spinner-arc" cx="12" cy="12" r="10" stroke-dasharray="16 46" stroke-dashoffset="0" /></svg></span>`;

export function registerLoadingDirective() {
    Alpine.directive('loading', (el, { expression }, { evaluateLater, effect }) => {
        const getLoading = evaluateLater(expression);
        const loadingLabel = el.getAttribute('data-loading-label') || 'Saving...';

        const contentWrap = document.createElement('span');
        contentWrap.className = 'ma-btn-content';
        while (el.firstChild) contentWrap.appendChild(el.firstChild);

        const loadingWrap = document.createElement('span');
        loadingWrap.className = 'ma-btn-loading';
        loadingWrap.setAttribute('aria-hidden', 'true');
        loadingWrap.innerHTML = SPINNER_SVG + `<span class="button-loading-label">${loadingLabel}</span>`;

        loadingWrap.style.display = 'none';
        el.appendChild(loadingWrap);
        el.appendChild(contentWrap);

        effect(() => {
            getLoading((loading) => {
                const isLoad = !!loading;
                el.setAttribute('aria-busy', isLoad);
                loadingWrap.style.display = isLoad ? '' : 'none';
                contentWrap.style.display = isLoad ? 'none' : '';
            });
        });
    });
}
