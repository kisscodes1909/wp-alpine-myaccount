import BaseFormHandler from "./BaseFormHandler.js";

class LostPasswordHandler extends BaseFormHandler {
    getValidationSchema() {
        return window.yup.object().shape({
            email: window.yup.string().email('Your email address isn’t valid.')
                .required('This field is required.'),
        });
    }
}

document.addEventListener('alpine:init', () => {
    Alpine.data('lostPassword', () => ({
        formData: {
            email: '',
        },
        isFormSubmitting: false,
        allowSubmit: false,
        errors:{},
        touched:{},
        notice:'',
        handler: null,
        init() {
            this.handler = new LostPasswordHandler(this.formData, {});
            //
            // Watch and bind handler properties to Alpine.js properties
            this.$watch('handler.isFormSubmitting', value => this.isFormSubmitting = value);
            this.$watch('handler.errors', value => this.errors = value);
            this.$watch('handler.touched', value => this.touched = value);
            this.$watch('handler.notice', value => this.notice = value);
        },
    }));
});