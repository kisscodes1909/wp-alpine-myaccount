/**
 * Form Components Registry
 * Centralized registration of all form-related Alpine components
 */
import { registerAuthFormComponents } from './auth.js';
import { registerEditAccountFormComponents } from './edit-account.js';

export function registerFormComponents() {
    registerAuthFormComponents();
    registerEditAccountFormComponents();
}
