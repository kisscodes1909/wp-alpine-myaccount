/**
 * Update Account Handler — profile + full billing (WC_Customer billing_*).
 */
import BaseFormHandler from '../BaseFormHandler.js';

const req = (msg) => window.yup.string().required(msg);

export default class UpdateAccountHandler extends BaseFormHandler {
    getValidationSchema() {
        const y = window.yup;
        return y.object().shape({
            firstName: req('Please add your first name.'),
            lastName: req('Please add your last name.'),
            billing_address_1: req('Please add your street address.'),
            billing_city: req('Please add your city.'),
            billing_state: window.yup.string(),
            billing_postcode: window.yup.string(),
            billing_country: req('Please choose a country.'),
            billing_phone: req('Please add a phone number.'),
            billing_email: y.string().email('Please enter a valid email.').required('Please add your billing email.'),
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
