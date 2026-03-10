import LostPasswordHandler from '../../../handlers/LostPasswordHandler.js';

export default () => ({
    formData: {
        email: '',
    },
    isFormSubmitting: false,
    allowSubmit: false,
    errors: {},
    touched: {},
    notice: '',
    handler: null,

    init() {
        this.handler = new LostPasswordHandler(this.formData, {});

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
});
