/**
 * Signup Component
 * Usage: <form x-data="signup" @submit.prevent="handleSubmit">
 * Requires: authenicationData.signupNonce, authenicationData.captchaSiteKey
 */
export default () => ({
    formData: {
        firstName: '',
        lastName: '',
        email: '',
        password: '',
        agreeTOS: false,
        receiveOfferNews: false,
    },
    messages: { message: 0 },
    isFormSubmitting: false,
    notice: '',
    errors: {},
    touched: {},
    signupNonce: window.authenicationData?.signupNonce || '',
    passedRequirements: [],
    passwordRequirements: {
        minLength: { regex: /.{8,}/, message: 'At least 8 characters', code: 'ERR_PASSWORD_MINLENGTH' },
        uppercase: { regex: /(?=.*[A-Z])/, message: '1 uppercase letter', code: 'ERR_PASSWORD_UPPERCASE' },
        number: { regex: /(?=.*[0-9])/, message: '1 number', code: 'ERR_PASSWORD_NUMBER' },
        lowercase: { regex: /(?=.*[a-z])/, message: '1 lowercase letter', code: 'ERR_PASSWORD_LOWERCASE' },
    },

    async handleSubmit() {
        const token = await grecaptcha.execute(window.authenicationData.captchaSiteKey, { action: 'signup' });

        await this.validateForm();

        if (Object.keys(this.errors).length > 0) {
            return;
        }

        this.isFormSubmitting = true;

        window.wp.ajax.post('handle_signup', {
            firstName: this.formData.firstName,
            lastName: this.formData.lastName,
            email: this.formData.email,
            password: this.formData.password,
            agreeTOS: this.formData.agreeTOS,
            receiveOfferNews: this.formData.receiveOfferNews,
            signupNonce: this.signupNonce,
            captchaToken: token
        }).done((response) => {
            this.notice = response.message;
            this.isFormSubmitting = false;
            window.location.reload();
        }).fail((error) => {
            this.notice = error.message;
            this.isFormSubmitting = false;
            console.error('Error:', error);
        });
    },

    async validateForm() {
        const fields = Object.keys(this.formData);
        for (const field of fields) {
            if (field === 'receiveOfferNews') continue;
            await this.validateField(field);
        }
    },

    async validateField(field) {
        const yup = window.yup;
        const schema = yup.object().shape({
            firstName: yup.string().required('This field is required.')
                .matches(/^[A-Za-z]+$/, 'Your name isn\'t valid.'),
            lastName: yup.string().required('This field is required.')
                .matches(/^[A-Za-z]+$/, 'Your name isn\'t valid.'),
            email: yup.string().email('Your email address isn\'t valid.').required('This field is required.'),
            agreeTOS: yup.boolean()
                .required('You must accept the Terms of Service.')
                .oneOf([true], 'You must accept the Terms of Service.'),
            password: yup.string().required('This field is required.')
                .test('password-complexity', 'Your password does not meet the requirements.', value => {
                    let passedRequirements = [];
                    Object.entries(this.passwordRequirements).forEach(([, requirement]) => {
                        if (requirement.regex.test(value)) {
                            passedRequirements.push(requirement.code);
                        }
                    });
                    this.passedRequirements = passedRequirements;
                    return passedRequirements.length === Object.keys(this.passwordRequirements).length;
                })
        });

        try {
            await schema.validateAt(field, this.formData);
            delete this.errors[field];
        } catch (error) {
            this.errors[field] = error.message;
        }

        this.touched[field] = true;
    },
});
