/**
 * Signup Component
 * Usage: <form x-data="signup" @submit.prevent="handleSubmit">
 * Requires: authenicationData.signupNonce, authenicationData.captchaSiteKey
 */
import SignupHandler from '../../../handlers/SignupHandler.js';

export default () => ({
    formData: {
        firstName: '',
        lastName: '',
        email: '',
        password: '',
        agreeTOS: false,
        receiveOfferNews: false,
    },
    isFormSubmitting: false,
    notice: '',
    errors: {},
    touched: {},
    passedRequirements: [],
    passwordRequirements: {},
    handler: null,

    init() {
        const authData = window.authenicationData || {};

        this.handler = new SignupHandler(this.formData, {
            signupNonce: authData.signupNonce || '',
            captchaSiteKey: authData.captchaSiteKey || '',
        });

        this.passwordRequirements = this.handler.passwordRequirements;

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
        this.$watch('handler.passedRequirements', (value) => {
            this.passedRequirements = value;
        });
    },

    async handleSubmit() {
        await this.handler.handleSubmit();
    },

    validateField(field) {
        return this.handler.validateField(field);
    },
});
