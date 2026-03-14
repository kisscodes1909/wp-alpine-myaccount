/**
 * Change Password Handler
 * Extends BaseFormHandler to handle change password submission
 */
import BaseFormHandler from '../BaseFormHandler.js';

export default class ChangePasswordHandler extends BaseFormHandler {
    getValidationSchema() {
        return window.yup.object().shape({
            currentPassword: window.yup.string().required('Please enter your current password.'),
            newPassword: window.yup.string().min(8, 'New password: at least 8 characters.')
                .required('Please enter a new password.'),
            confirmPassword: window.yup.string()
                .oneOf([window.yup.ref('newPassword'), null], 'Those passwords don’t match — try again.')
                .required('Please confirm your new password.')
        });
    }

    getApiEndpoint() {
        return 'change_password';
    }

    async handleSubmit() {
        await this.validateForm();

        if (Object.keys(this.errors).length > 0) {
            this.isFormSubmitting = false;
            return;
        }

        this.isFormSubmitting = true;

        const payload = {
            ...this.additionalData,
            currentPassword: this.formData.currentPassword,
            pass1: this.formData.newPassword,
            pass2: this.formData.confirmPassword,
            keepSignedIn: this.formData.keepSignedIn,
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
        const message = this.getResponseMessage(response);
        if (message) {
            window.Alpine?.store('toast')?.addToast(message, 'success');
        }
        window.Alpine?.store('popup')?.closePopup();
    }

    fail(error) {
        const message = this.getErrorMessage(error);
        if (message) {
            window.Alpine?.store('toast')?.addToast(message, 'error');
        }
    }
}
