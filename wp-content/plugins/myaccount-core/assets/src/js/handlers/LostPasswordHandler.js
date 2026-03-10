/**
 * Lost Password Handler
 * Extends BaseFormHandler to handle lost password submission
 */
import BaseFormHandler from '../BaseFormHandler.js';

export default class LostPasswordHandler extends BaseFormHandler {
    getValidationSchema() {
        return window.yup.object().shape({
            email: window.yup
                .string()
                .email('Your email address isn\'t valid.')
                .required('This field is required.'),
        });
    }
}
