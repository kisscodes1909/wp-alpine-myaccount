import Alpine from 'alpinejs';
import { registerSharedStores } from '../stores/shared.js';
import { registerLoadingDirective } from '../directives/loading.js';

window.Alpine = Alpine;

registerSharedStores();
registerLoadingDirective();

window.MyAccountAlpineRuntime = window.MyAccountAlpineRuntime || {};

window.MyAccountAlpineRuntime.start = () => {
    if (window.MyAccountAlpineRuntime.started) {
        return;
    }

    Alpine.start();
    window.MyAccountAlpineRuntime.started = true;
};
