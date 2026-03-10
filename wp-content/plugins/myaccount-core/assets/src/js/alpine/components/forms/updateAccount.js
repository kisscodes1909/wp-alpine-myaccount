/**
 * Update Account Component
 * Usage: <form x-data="updateAccount" @submit.prevent="handleSubmit">
 * Requires: userData (firstName, lastName, email), saveAccountDetailsNonce
 */
import UpdateAccountHandler from '../../../handlers/UpdateAccountHandler.js';

export default () => {
    const userData = window.accountData || {};
    const nonce = window.saveAccountDetailsNonce || '';

    return {
        formData: {
            firstName: userData.firstName || '',
            lastName: userData.lastName || '',
            email: userData.email || '',
        },
        allowSubmit: false,
        isFormSubmitting: false,
        errors: {},
        touched: {},
        notice: '',
        handler: null,

        init() {
            this.handler = new UpdateAccountHandler(this.formData, {
                nonce
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

        setAllowSubmit() {
            this.allowSubmit = true;
        },

        async handleSubmit() {
            await this.handler.handleSubmit();
        },

        async validateForm() {
            await this.handler.validateForm();
            this.allowSubmit = Object.keys(this.handler.errors || {}).length === 0;
        },

        validateField(field) {
            return this.handler.validateField(field);
        },
    };
};
