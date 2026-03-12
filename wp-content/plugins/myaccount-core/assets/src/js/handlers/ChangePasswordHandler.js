/**
 * Change Password Handler
 * Extends BaseFormHandler to handle change password submission
 */
import BaseFormHandler from '../BaseFormHandler.js';

export default class ChangePasswordHandler extends BaseFormHandler {
    getValidationSchema() {
        return window.yup.object().shape({
            currentPassword: window.yup.string().required('The current password is required.'),
            newPassword: window.yup.string().min(8, 'The new password must be at least 8 characters long.')
                .required('The new password is required.'),
            confirmPassword: window.yup.string()
                .oneOf([window.yup.ref('newPassword'), null], 'Passwords must match.')
                .required('Confirming your new password is required.')
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

        window.wp.ajax.post(this.getApiEndpoint(), payload).done((response) => {
            this.isFormSubmitting = false;
            this.notice = this.getResponseMessage(response);
            this.done(response);

            const event = new CustomEvent(`${this.getApiEndpoint()}_success`);
            window.dispatchEvent(event);

        }).fail((error) => {
            this.notice = this.getErrorMessage(error);
            this.isFormSubmitting = false;
        });
    }

    done(response) {
        const message = this.getResponseMessage(response);
        if (message) {
            window.Alpine?.store('toast')?.addToast(message, 'success');
        }
        window.Alpine?.store('popup')?.closePopup();
    }
}
