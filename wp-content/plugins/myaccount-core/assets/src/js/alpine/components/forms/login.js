/**
 * Login Component
 * Usage: <form x-data="login" @submit.prevent="handleSubmit">
 * Requires: LoginHandler class, authenicationData.wooLoginNonce
 */
import LoginHandler from '../../../handlers/LoginHandler.js';

export default () => ({
    formData: {
        email: '',
        password: '',
        rememberme: false
    },
    isFormSubmitting: false,
    allowSubmit: false,
    errors: {},
    touched: {},
    woocommerceLoginNonce: window.authenicationData?.wooLoginNonce || '',
    notice: '',
    noticeType: '',
    handler: null,

    init() {
        this.handler = new LoginHandler(this.formData, {
            woocommerceLoginNonce: this.woocommerceLoginNonce
        });

        // Watch and bind handler properties to Alpine.js properties
        this.$watch('handler.isFormSubmitting', value => this.isFormSubmitting = value);
        this.$watch('handler.errors', value => this.errors = value);
        this.$watch('handler.touched', value => this.touched = value);
        this.$watch('handler.notice', value => this.notice = value);
        this.$watch('handler.noticeType', value => this.noticeType = value);
    },

    async handleSubmit() {
        await this.handler.handleSubmit(['rememberme']);
    },
});
