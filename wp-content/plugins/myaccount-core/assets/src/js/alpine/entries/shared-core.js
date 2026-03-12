import Alpine from 'alpinejs';
import { registerSharedStores } from '../stores/shared.js';
import { registerLoadingDirective } from '../directives/loading.js';
import maNavDropdown from '../components/account/maNavDropdown.js';

window.Alpine = Alpine;

registerSharedStores();
registerLoadingDirective();
Alpine.data('maNavDropdown', maNavDropdown);

window.MyAccountAlpineRuntime = window.MyAccountAlpineRuntime || {};

window.MyAccountAlpineRuntime.start = () => {
    if (window.MyAccountAlpineRuntime.started) {
        return;
    }

    Alpine.start();
    window.MyAccountAlpineRuntime.started = true;
};
