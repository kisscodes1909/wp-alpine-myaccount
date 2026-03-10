/**
 * Password Change Form Component
 * Usage: <form x-data="passwordChangeForm" @submit.prevent="handleSubmit">
 * Requires: changePasswordNonce
 */
import ChangePasswordHandler from '../../../handlers/ChangePasswordHandler.js';

export default () => ({
    formData: {
        currentPassword: '',
        newPassword: '',
        confirmPassword: '',
        keepSignedIn: true,
    },
    isFormSubmitting: false,
    errors: {},
    touched: {},
    notice: '',
    changePasswordNonce: window.changePasswordNonce || '',
    handler: null,

    init() {
        this.handler = new ChangePasswordHandler(this.formData, {
            nonce: this.changePasswordNonce
        });

        this.$watch('handler.isFormSubmitting', (value) => {
            this.isFormSubmitting = value;
        });
        this.$watch('handler.errors', (value) => {
            this.errors = value;
        });
        this.$watch('handler.touched', (value) => {
            this.touched = value;
        });
        this.$watch('handler.notice', (value) => {
            this.notice = value;
        });
    },

    async handleSubmit() {
        await this.handler.handleSubmit();
    },

    validateField(field) {
        return this.handler.validateField(field);
    },
});
