export default class BaseFormHandler {
    constructor(formData, additionalData = {}) {
        this.formData = formData;
        this.additionalData = additionalData;
        this.errors = {};
        this.touched = {};
        this.notice = '';
        this.isFormSubmitting = false;
    }

    async validateForm(skipFields = []) {
        const fields = Object.keys(this.formData).filter(field => !skipFields.includes(field));
        for (const field of fields) {
            await this.validateField(field);
        }
    }

    async validateField(field) {
        const yup = window.yup;
        const schema = this.getValidationSchema();

        try {
            await schema.validateAt(field, this.formData);
            delete this.errors[field];
        } catch (error) {
            this.errors[field] = error.message;
        }

        this.touched[field] = true;
    }

    getValidationSchema() {
        throw new Error('getValidationSchema method should be implemented in the subclass');
    }

    getErrorMessage(error) {
        const responseData = error?.responseJSON?.data;

        if (typeof responseData?.message === 'string' && responseData.message) {
            return responseData.message;
        }

        if (typeof responseData === 'string' && responseData) {
            return responseData;
        }

        if (typeof error?.message === 'string' && error.message) {
            return error.message;
        }

        return 'Something went wrong. Please try again.';
    }

    async handleSubmit(skipFields) {
        await this.validateForm(skipFields);

        if (Object.keys(this.errors).length > 0) {
            this.isFormSubmitting = false;
            return;
        }

        this.isFormSubmitting = true;

        window.wp.ajax.post(this.getApiEndpoint(), {
            ...this.formData,
            ...this.additionalData
        }).done((response) => {
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
}
