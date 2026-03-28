import { registerAddressStore } from '../stores/address.js';

registerAddressStore();
const userAddressStore = window.Alpine?.store?.('userAddress');
if (userAddressStore?.init) {
    userAddressStore.init();
}
window.MyAccountAlpineRuntime?.start?.();
