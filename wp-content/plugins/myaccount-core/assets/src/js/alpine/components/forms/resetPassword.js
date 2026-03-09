import BaseFormHandler from '../../../BaseFormHandler.js';

class ResetPasswordHandler extends BaseFormHandler {
    constructor(formData, additionalData = {}) {
        super(formData, additionalData);
        this.passedRequirements = [];
        this.passwordRequirements = {
            minLength: { regex: /.{8,}/, message: 'At least 8 characters', code: 'ERR_PASSWORD_MINLENGTH' },
            uppercase: { regex: /(?=.*[A-Z])/, message: '1 uppercase letter', code: 'ERR_PASSWORD_UPPERCASE' },
            number: { regex: /(?=.*[0-9])/, message: '1 number', code: 'ERR_PASSWORD_NUMBER' },
            lowercase: { regex: /(?=.*[a-z])/, message: '1 lowercase letter', code: 'ERR_PASSWORD_LOWERCASE' },
        };
    }

    getValidationSchema() {
        return window.yup.object().shape({
            password: window.yup
                .string()
                .required('This field is required.')
                .test('password-complexity', 'Your password does not meet the requirements.', (value) => {
                    const passedRequirements = [];

                    Object.values(this.passwordRequirements).forEach((requirement) => {
                        if (requirement.regex.test(value || '')) {
                            passedRequirements.push(requirement.code);
                        }
                    });

                    this.passedRequirements = passedRequirements;
                    return passedRequirements.length === Object.keys(this.passwordRequirements).length;
                }),
        });
    }
}

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
