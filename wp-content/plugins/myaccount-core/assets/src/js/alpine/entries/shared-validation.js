import * as yup from 'yup';
import { registerValidationDirectives } from '../directives/validate.js';

window.yup = yup;
registerValidationDirectives();
