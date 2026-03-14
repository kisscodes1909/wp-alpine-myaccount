/**
 * Login Handler
 * Extends BaseFormHandler to handle login form submission.
 * Success and error messages are shown via notice (ma-form__notice) only.
 */
import BaseFormHandler from '../BaseFormHandler.js';

export default class LoginHandler extends BaseFormHandler {
    getValidationSchema() {
        return window.yup.object().shape({
            password: window.yup.string().required('Please enter your password.')
                .min(8, 'Password must be at least 8 characters.'),
            email: window.yup.string().email('Please enter a valid email address.')
                .required('Please enter your email address.'),
        });
    }

    done() {
        window.location.reload();
    }

    getApiEndpoint() {
        return 'handle_login';
    }
}
