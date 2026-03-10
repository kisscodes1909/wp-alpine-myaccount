/**
 * Signup Handler
 * Extends BaseFormHandler to handle signup submission
 */
import BaseFormHandler from '../BaseFormHandler.js';

export default class SignupHandler extends BaseFormHandler {
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
            firstName: window.yup.string().required('This field is required.')
                .matches(/^[A-Za-z]+$/, 'Your name isn\'t valid.'),
            lastName: window.yup.string().required('This field is required.')
                .matches(/^[A-Za-z]+$/, 'Your name isn\'t valid.'),
            email: window.yup.string().email('Your email address isn\'t valid.')
                .required('This field is required.'),
            agreeTOS: window.yup.boolean()
                .required('You must accept the Terms of Service.')
                .oneOf([true], 'You must accept the Terms of Service.'),
            password: window.yup.string().required('This field is required.')
                .test('password-complexity', 'Your password does not meet the requirements.', (value) => {
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
        const captchaSiteKey = this.additionalData.captchaSiteKey || '';
        const token = captchaSiteKey && window.grecaptcha
            ? await window.grecaptcha.execute(captchaSiteKey, { action: 'signup' })
            : '';

        await this.validateForm(['receiveOfferNews']);

        if (Object.keys(this.errors).length > 0) {
            this.isFormSubmitting = false;
            return;
        }

        this.isFormSubmitting = true;

        const payload = {
            ...this.formData,
            signupNonce: this.additionalData.signupNonce,
            captchaToken: token,
        };

        window.wp.ajax.post(this.getApiEndpoint(), payload).done((response) => {
            this.isFormSubmitting = false;
            this.notice = response.message;
            this.done(response);

            const event = new CustomEvent(`${this.getApiEndpoint()}_success`);
            window.dispatchEvent(event);

        }).fail((error) => {
            this.notice = this.getErrorMessage(error);
            this.isFormSubmitting = false;
        });
    }

    done() {
        window.location.reload();
    }
}
