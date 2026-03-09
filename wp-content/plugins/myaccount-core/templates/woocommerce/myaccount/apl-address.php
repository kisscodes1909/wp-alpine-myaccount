<?php
wc_get_template(
	'myaccount/page-heading.php',
	array(
		'page_heading'     => 'Address Book',
		'page_description' => 'Manage your saved addresses',
	)
);
?>

<div x-cloak x-data x-init="$store.userAddress.init()" class="ma-address-book">
    <div x-show="$store.userAddress.showNotification"
         x-transition:enter="ma-tr-enter"
         x-transition:enter-start="ma-tr-enter-start"
         x-transition:enter-end="ma-tr-enter-end"
         x-transition:leave="ma-tr-leave"
         x-transition:leave-start="ma-tr-leave-start"
         x-transition:leave-end="ma-tr-leave-end"
         class="ma-address-book__toast">
        <p x-text="$store.userAddress.notificationMessage"></p>
    </div>

    <div class="ma-address-book__list">
        <template x-for="address in $store.userAddress.addresses" :key="address.id">
            <div class="ma-address-book__card">
                <div class="ma-address-book__card-head">
                    <div class="ma-address-book__identity">
                        <h3 class="ma-address-book__name" x-text="`${address.fname} ${address.lname}`"></h3>
                        <p class="ma-address-book__phone" x-text="address.phone"></p>
                    </div>
                    <template x-if="address.default">
                        <span class="ma-address-book__default-pill">
                            <svg xmlns="http://www.w3.org/2000/svg" class="ma-address-book__default-icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Default</span>
                        </span>
                    </template>
                </div>

                <div class="ma-address-book__content">
                    <p x-text="address.address"></p>
                    <template x-if="address.address2">
                        <p x-text="address.address2"></p>
                    </template>
                    <p x-text="[address.city, address.region, address.postalCode].filter(Boolean).join(', ')"></p>
                    <p x-text="address.country"></p>
                </div>

                <div class="ma-address-book__actions">
                    <button @click="
                        $store.userAddress.startEdit(address.id);
                        $store.popup.openPopup(document.getElementById('edit-address').innerHTML);
                        "
                            class="button light slim ma-address-book__action-button">
                        Edit
                    </button>
                    <button x-show="!address.default || $store.userAddress.isActionLoading('set-default', address.id)"
                            type="button"
                            @click="$store.userAddress.setDefault(address.id, true)"
                            :disabled="$store.userAddress.isActionLoading('set-default', address.id)"
                            x-loading="$store.userAddress.isActionLoading('set-default', address.id)"
                            data-loading-label="Saving..."
                            class="button light slim ma-address-book__action-button">
                        Set Default
                    </button>
                    <button type="button"
                            @click="$store.userAddress.remove(address.id)"
                            :disabled="$store.userAddress.isActionLoading('delete', address.id)"
                            x-loading="$store.userAddress.isActionLoading('delete', address.id)"
                            data-loading-label="Deleting..."
                            class="button light slim ma-address-book__action-button ma-address-book__action-button--danger">
                        Delete
                    </button>
                </div>
            </div>
        </template>
    </div>

    <div x-show="$store.userAddress.addresses.length === 0" class="ma-address-book__empty">
        <h3 class="ma-address-book__empty-title">You have not added any addresses yet.</h3>
    </div>

    <div class="ma-address-book__footer" x-effect="$store.userAddress.checkMaxAddress()">
        <template x-if="$store.userAddress.stopAdd">
            <div class="ma-address-book__limit-message">*You can only save up to 9 addresses.</div>
        </template>
        <button
                @click="
                $store.userAddress.startAdd()
                $store.popup.openPopup(document.getElementById('edit-address').innerHTML)"
                :disabled="$store.userAddress.stopAdd"
                class="button slim ma-address-book__add-button">
            <svg xmlns="http://www.w3.org/2000/svg" class="ma-address-book__add-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Add Address</span>
        </button>
    </div>

</div>
