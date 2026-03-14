/**
 * Update Account Component
 * Usage: <form x-data="updateAccount" @submit.prevent="handleSubmit">
 * Requires: window.accountData (profile + billing), saveAccountDetailsNonce
 */
import UpdateAccountHandler from '../../../handlers/UpdateAccountHandler.js';

function billingDefaults(data) {
    return {
        billing_company: data.billing_company || '',
        billing_address_1: data.billing_address_1 || '',
        billing_address_2: data.billing_address_2 || '',
        billing_city: data.billing_city || '',
        billing_state: data.billing_state || '',
        billing_postcode: data.billing_postcode || '',
        billing_country: data.billing_country || '',
        billing_phone: data.billing_phone || '',
        billing_email: data.billing_email || '',
    };
}

export default () => {
    const userData = window.accountData || {};
    const nonce = window.saveAccountDetailsNonce || '';

    return {
        formData: {
            firstName: userData.firstName || '',
            lastName: userData.lastName || '',
            ...billingDefaults(userData),
        },
        /* Save enabled without forcing a prior @input (readonly email no longer triggers input). */
        allowSubmit: true,
        isFormSubmitting: false,
        errors: {},
        touched: {},
        notice: '',
        handler: null,

        init() {
            this.handler = new UpdateAccountHandler(this.formData, {
                nonce,
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
