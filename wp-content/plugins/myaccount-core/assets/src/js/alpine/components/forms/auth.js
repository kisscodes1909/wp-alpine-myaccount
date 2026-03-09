import login from './login.js';
import signup from './signup.js';
import lostPassword from './lostPassword.js';
import resetPassword from './resetPassword.js';

export function registerAuthFormComponents() {
    Alpine.data('login', login);
    Alpine.data('signup', signup);
    Alpine.data('lostPassword', lostPassword);
    Alpine.data('resetPassword', resetPassword);
}
