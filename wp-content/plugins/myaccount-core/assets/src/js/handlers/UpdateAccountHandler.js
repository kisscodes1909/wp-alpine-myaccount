/**
 * Update Account Handler
 * Extends BaseFormHandler to handle account updates
 */
import BaseFormHandler from '../BaseFormHandler.js';

export default class UpdateAccountHandler extends BaseFormHandler {
    getValidationSchema() {
        return window.yup.object().shape({
            firstName: window.yup.string().required('First name is required.'),
            lastName: window.yup.string().required('Last name is required.'),
            email: window.yup.string().email('Invalid email address.').required('Email is required.'),
        });
    }

    getApiEndpoint() {
        return 'save_account_details';
    }

    async handleSubmit() {
        await this.validateForm();

        if (Object.keys(this.errors).length > 0) {
            this.isFormSubmitting = false;
            return;
        }

        this.isFormSubmitting = true;

        const payload = {
            ...this.formData,
            nonce: this.additionalData.nonce,
        };

        window.wp.ajax.post(this.getApiEndpoint(), payload)
            .done((response) => {
                this.isFormSubmitting = false;
                this.notice = '';
                this.done(response);
                window.dispatchEvent(new CustomEvent(`${this.getApiEndpoint()}_success`));
            })
            .fail((error) => {
                this.isFormSubmitting = false;
                this.notice = '';
                this.fail(error);
            });
    }

    done(response) {
        const message = this.getResponseMessage(response) || 'Your account details have been updated.';
        window.Alpine?.store('toast')?.addToast(message, 'success');
    }

    fail(error) {
        const message = this.getErrorMessage(error);
        window.Alpine?.store('toast')?.addToast(message, 'error');
    }
}
