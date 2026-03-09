import userAddress from './userAddress.js';

export function registerAddressStore() {
    Alpine.store('userAddress', userAddress);
}
