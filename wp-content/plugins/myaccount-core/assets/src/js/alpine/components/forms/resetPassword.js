import ResetPasswordHandler from '../../../handlers/ResetPasswordHandler.js';

export default () => ({
    formData: {
        password: '',
    },
    isFormSubmitting: false,
    allowSubmit: false,
    errors: {},
    touched: {},
    notice: '',
    handler: null,
    passedRequirements: [],
    passwordRequirements: {},

    init() {
        this.handler = new ResetPasswordHandler(this.formData);

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
        this.$watch('handler.passwordRequirements', (value) => {
            this.passwordRequirements = value;
        });

        this.passwordRequirements = this.handler.passwordRequirements;
    },
});
