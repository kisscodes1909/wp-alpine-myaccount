import { initReturnsModuleTrees, registerReturnsModuleComponents } from '../modules/returns/register.js';

registerReturnsModuleComponents();

if (window.MyAccountAlpineRuntime?.started) {
    initReturnsModuleTrees();
}
