/**
 * Update Account Handler — profile + full billing (WC_Customer billing_*).
 */
import BaseFormHandler from '../BaseFormHandler.js';

const req = (msg) => window.yup.string().required(msg);

export default class UpdateAccountHandler extends BaseFormHandler {
    getValidationSchema() {
        const y = window.yup;
        return y.object().shape({
            firstName: req('First name is required.'),
            lastName: req('Last name is required.'),
            billing_address_1: req('Street address is required.'),
            billing_city: req('City is required.'),
            billing_state: window.yup.string(),
            billing_postcode: window.yup.string(),
            billing_country: req('Country is required.'),
            billing_phone: req('Phone is required.'),
            billing_email: y.string().email('Invalid billing email.').required('Billing email is required.'),
            billing_company: y.string().nullable(),
            billing_address_2: y.string().nullable(),
        });
    }

    getApiEndpoint() {
        return 'save_account_details';
    }

    async handleSubmit() {
        await this.validateForm();

        if (Object.keys(this.errors).length > 0) {
            this.isFormSubmitting = false;
            const first = Object.values(this.errors).find(Boolean);
            if (first) {
                window.Alpine?.store('toast')?.addToast(first, 'error');
            }
            return;
        }

        this.isFormSubmitting = true;

        const payload = {
            ...this.formData,
            billing_first_name: this.formData.firstName,
            billing_last_name: this.formData.lastName,
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
