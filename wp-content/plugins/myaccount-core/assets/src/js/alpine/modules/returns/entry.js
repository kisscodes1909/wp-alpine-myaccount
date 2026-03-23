import { initReturnsModuleTrees, registerReturnsModuleComponents } from './register.js';

registerReturnsModuleComponents();

if (window.MyAccountAlpineRuntime?.started) {
    initReturnsModuleTrees();
}
