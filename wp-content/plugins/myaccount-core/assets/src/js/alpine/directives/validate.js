/**
 * Alpine Validation Directives
 * Custom directives for form validation UI
 */
export function registerValidationDirectives() {

    Alpine.directive('validate-field', (el, { expression }, { evaluateLater, effect }) => {
        let getValidator = evaluateLater(expression);
        effect(() => {
            getValidator(errors => {
                const {message, touched} = errors;

                if (touched && message) {
                    el.classList.add('error');
                } else {
                    el.classList.remove('error');
                }
            });
        });
    });

    Alpine.directive('validate-icon', (el, { expression }, { evaluateLater, effect }) => {
        let getValidator = evaluateLater(expression);

        el.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="close w-10 h-10 text-red-600 bg-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tick w-10 h-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>`;

        const closeEl = el.querySelector('.close');
        const tickEl = el.querySelector('.tick');

        effect(() => {
            getValidator(errors => {
                const {message, touched} = errors;

                if(message) {
                    closeEl.style.display = 'block';
                } else {
                    closeEl.style.display = 'none';
                }

                if(touched && !message) {
                    tickEl.style.display = 'block';
                } else {
                    tickEl.style.display = 'none';
                }
            });
        });
    });

    Alpine.directive('validate-error', (el, { expression }, { evaluateLater, effect }) => {
        let getValidator = evaluateLater(expression);

        el.classList.add('ma-form__error');
        el.innerHTML = `<svg class="ma-form__error-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" focusable="false">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01" />
                        </svg>
                        <span class="ma-form__error-text"></span>`;

        const textEl = el.querySelector('.ma-form__error-text');
        el.style.display = 'none';

        effect(() => {
            getValidator(errors => {
                const hasTouched = errors && Object.prototype.hasOwnProperty.call(errors, 'touched');
                const touched = hasTouched ? errors.touched : true;
                const message = errors && errors.message ? errors.message : '';
                const shouldShow = Boolean(message) && (touched === undefined || touched);

                if (textEl) {
                    textEl.textContent = message;
                }

                el.style.display = shouldShow ? 'inline-flex' : 'none';
            });
        });
    });
}
