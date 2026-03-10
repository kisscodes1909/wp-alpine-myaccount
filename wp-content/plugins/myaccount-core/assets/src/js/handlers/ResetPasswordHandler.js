/**
 * Reset Password Handler
 * Extends BaseFormHandler to handle reset password submission
 */
import BaseFormHandler from '../BaseFormHandler.js';

export default class ResetPasswordHandler extends BaseFormHandler {
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

                    this.passedRequirements = [...passedRequirements];
                    return passedRequirements.length === Object.keys(this.passwordRequirements).length;
                }),
        });
    }
}
