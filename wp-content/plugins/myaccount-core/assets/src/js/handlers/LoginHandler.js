/**
 * Login Handler
 * Extends BaseFormHandler to handle login form submission.
 * Success and error messages are shown via notice (ma-form__notice) only.
 */
import BaseFormHandler from '../BaseFormHandler.js';

export default class LoginHandler extends BaseFormHandler {
    getValidationSchema() {
        return window.yup.object().shape({
            password: window.yup.string().required('This field is required.')
                .min(8, 'Your password isn\'t valid.'),
            email: window.yup.string().email('Your email address isn\'t valid.')
                .required('This field is required.'),
        });
    }

    done() {
        window.location.reload();
    }

    getApiEndpoint() {
        return 'handle_login';
    }
}
