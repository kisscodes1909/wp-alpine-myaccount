/**
 * Alpine directive: x-loading
 * Reusable button loading state – shows spinner + label, disables button.
 *
 * Usage:
 *   <button x-loading="isLoading" data-loading-label="Saving...">
 *     <svg>...</svg><span>Save</span>
 *   </button>
 *
 * - Expression: boolean (or Alpine expression that evaluates to boolean).
 * - data-loading-label: optional, text when loading (default "Saving...").
 * - Button should have inline-flex items-center gap-2 for alignment (or your own classes).
 */

const SPINNER_SVG = `<span class="loading-icon inline-flex items-center justify-center w-5 h-5 flex-shrink-0" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle class="spinner-arc" cx="12" cy="12" r="10" stroke-dasharray="16 46" stroke-dashoffset="0" /></svg></span>`;

export function registerLoadingDirective() {
    Alpine.directive('loading', (el, { expression }, { evaluateLater, effect }) => {
        const getLoading = evaluateLater(expression);
        const loadingLabel = el.getAttribute('data-loading-label') || 'Saving...';

        const contentWrap = document.createElement('span');
        contentWrap.className = 'inline-flex items-center justify-center gap-2';
        while (el.firstChild) contentWrap.appendChild(el.firstChild);

        const loadingWrap = document.createElement('span');
        loadingWrap.className = 'inline-flex items-center justify-center gap-2';
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
