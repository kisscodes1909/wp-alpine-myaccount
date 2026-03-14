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
            minLength: { regex: /.{8,}/, message: '8 or more characters', code: 'ERR_PASSWORD_MINLENGTH' },
            uppercase: { regex: /(?=.*[A-Z])/, message: 'One uppercase letter', code: 'ERR_PASSWORD_UPPERCASE' },
            number: { regex: /(?=.*[0-9])/, message: 'One number', code: 'ERR_PASSWORD_NUMBER' },
            lowercase: { regex: /(?=.*[a-z])/, message: 'One lowercase letter', code: 'ERR_PASSWORD_LOWERCASE' },
        };
    }

    getValidationSchema() {
        return window.yup.object().shape({
            password: window.yup
                .string()
                .required('Please choose a new password.')
                .test('password-complexity', 'Almost there — add everything in the list above (8+ chars, upper, lower, and a number).', (value) => {
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
