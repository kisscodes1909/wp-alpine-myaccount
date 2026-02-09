/**
 * Form Components Registry
 * Centralized registration of all form-related Alpine components
 */
import login from './login.js';
import signup from './signup.js';
import updateAccount from './updateAccount.js';
import passwordChangeForm from './passwordChangeForm.js';

export function registerFormComponents() {
    Alpine.data('login', login);
    Alpine.data('signup', signup);
    Alpine.data('updateAccount', updateAccount);
    Alpine.data('passwordChangeForm', passwordChangeForm);
}
