import updateAccount from './updateAccount.js';
import passwordChangeForm from './passwordChangeForm.js';

export function registerEditAccountFormComponents() {
    Alpine.data('updateAccount', updateAccount);
    Alpine.data('passwordChangeForm', passwordChangeForm);
}
