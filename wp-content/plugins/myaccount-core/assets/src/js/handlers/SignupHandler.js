/**
 * Signup Handler
 * Extends BaseFormHandler to handle signup submission
 */
import BaseFormHandler from '../BaseFormHandler.js';

export default class SignupHandler extends BaseFormHandler {
    constructor(formData, additionalData = {}) {
        super(formData, additionalData);
        this.passedRequirements = [];
        /* Order = grid row-major: col1 row1, col2 row1, col1 row2, col2 row2 (matches UI partial). */
        this.passwordRequirements = {
            minLength: { regex: /.{8,}/, message: '8 or more characters', code: 'ERR_PASSWORD_MINLENGTH' },
            uppercase: { regex: /(?=.*[A-Z])/, message: 'One uppercase letter', code: 'ERR_PASSWORD_UPPERCASE' },
            number: { regex: /(?=.*[0-9])/, message: 'One number', code: 'ERR_PASSWORD_NUMBER' },
            lowercase: { regex: /(?=.*[a-z])/, message: 'One lowercase letter', code: 'ERR_PASSWORD_LOWERCASE' },
        };
    }

    getValidationSchema() {
        return window.yup.object().shape({
            firstName: window.yup.string().required('Please enter your first name.')
                .matches(/^[A-Za-z]+$/, 'First name: letters only, please.'),
            lastName: window.yup.string().required('Please enter your last name.')
                .matches(/^[A-Za-z]+$/, 'Last name: letters only, please.'),
            email: window.yup.string().email('Please enter a valid email address.')
                .required('Please enter your email address.'),
            agreeTOS: window.yup.boolean()
                .required('Please accept the Terms of Service to continue.')
                .oneOf([true], 'Please accept the Terms of Service to continue.'),
            password: window.yup.string().required('Please choose a password.')
                .test('password-complexity', 'Almost there — add everything in the list above (8+ chars, upper, lower, and a number).', (value) => {
                    const passedRequirements = [];
                    Object.entries(this.passwordRequirements).forEach(([, requirement]) => {
                        if (requirement.regex.test(value || '')) {
                            passedRequirements.push(requirement.code);
                        }
                    });
                    this.passedRequirements = [...passedRequirements];
                    return passedRequirements.length === Object.keys(this.passwordRequirements).length;
                })
        });
    }

    getApiEndpoint() {
        return 'handle_signup';
    }

    async handleSubmit() {
        await this.validateForm(['receiveOfferNews']);

        if (Object.keys(this.errors).length > 0) {
            this.isFormSubmitting = false;
            return;
        }

        this.isFormSubmitting = true;

        const payload = {
            ...this.formData,
            signupNonce: this.additionalData.signupNonce,
        };

        window.wp.ajax.post(this.getApiEndpoint(), payload)
            .done((response) => {
                this.isFormSubmitting = false;
                this.notice = this.getResponseMessage(response);
                this.noticeType = 'success';
                this.done(response);
                window.dispatchEvent(new CustomEvent(`${this.getApiEndpoint()}_success`));
            })
            .fail((error) => {
                this.isFormSubmitting = false;
                this.notice = this.getErrorMessage(error);
                this.noticeType = 'error';
            });
    }

    done() {
        window.location.reload();
    }
}
