<?php
wc_get_template('myaccount/page-heading.php',
    [
        'page_heading' => 'Address Book',
        'page_description' => 'Manage your saved addresses',
    ]
);
?>

<div x-cloak x-data x-init="$store.userAddress.init()" class="md:container mx-auto px-8 space-y-10 bg">
    <!-- Notification toast (uses userAddress store; previously in apl-address-popup) -->
    <div x-show="$store.userAddress.showNotification"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-90"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-90"
         class="fixed top-14 right-5 bg-black text-white px-4 py-3 shadow-lg z-[1000]">
        <p x-text="$store.userAddress.notificationMessage"></p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <template x-for="address in $store.userAddress.addresses" :key="address.id">
            <div class="border border-gray-200 bg-white px-8 pt-8 pb-6 flex flex-col h-[250px]">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="text-lg font-medium leading-7 text-gray-900" x-text="`${address.fname} ${address.lname}`"></h3>
                        <p class="text-base leading-6 text-gray-600 mt-2" x-text="address.phone"></p>
                    </div>
                    <template x-if="address.default">
                        <span class="inline-flex items-center gap-1 rounded-[999px] bg-black px-3 py-1 text-xs font-normal leading-4 tracking-wide text-white uppercase whitespace-nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Default</span>
                        </span>
                    </template>
                </div>

                <div class="mt-6 flex-1 text-base text-gray-600 leading-6 flex flex-col gap-0.5">
                    <p x-text="address.address"></p>
                    <template x-if="address.address2">
                        <p x-text="address.address2"></p>
                    </template>
                    <p x-text="[address.city, address.region, address.postalCode].filter(Boolean).join(', ')"></p>
                    <p x-text="address.country"></p>
                </div>

                <div class="pt-4 flex items-center justify-start gap-3 flex-wrap">
                    <button @click="
                        $store.userAddress.startEdit(address.id);
                        $store.popup.openPopup(document.getElementById('edit-address').innerHTML);
                        "
                            class="button light slim rounded-none w-[140px] inline-flex items-center justify-center"
                            style="border:1px solid #d1d5dc;">
                        Edit
                    </button>
                    <button x-show="!address.default || $store.userAddress.isActionLoading('set-default', address.id)"
                            type="button"
                            @click="$store.userAddress.setDefault(address.id, true)"
                            :disabled="$store.userAddress.isActionLoading('set-default', address.id)"
                            x-loading="$store.userAddress.isActionLoading('set-default', address.id)"
                            data-loading-label="Saving..."
                            class="button light slim rounded-none w-[140px] inline-flex items-center justify-center"
                            style="border:1px solid #d1d5dc;">
                        Set Default
                    </button>
                    <button type="button"
                            @click="$store.userAddress.remove(address.id)"
                            :disabled="$store.userAddress.isActionLoading('delete', address.id)"
                            x-loading="$store.userAddress.isActionLoading('delete', address.id)"
                            data-loading-label="Deleting..."
                            class="button light slim rounded-none w-[140px] inline-flex items-center justify-center text-[#ec003f]"
                            style="border:1px solid #ffa1ad;color:#ec003f;">
                        Delete
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- UI for Empty Addresses -->
    <div x-show="$store.userAddress.addresses.length === 0" class="p-4 bg-white">
        <h3 class="text-lg text-center text-gray-700">You have not added any addresses yet.</h3>
    </div>

    <div class="flex flex-col items-center justify-between" x-effect="$store.userAddress.checkMaxAddress()">
        <template x-if="$store.userAddress.stopAdd">
            <div class="text-red-600 text-center mb-2 text-sm">*You can only save up to 9 addresses.</div>
        </template>
        <button
                @click="
                $store.userAddress.startAdd()
                $store.popup.openPopup(document.getElementById('edit-address').innerHTML)"
                :disabled="$store.userAddress.stopAdd"
                class="button slim rounded-none max-w-[450px] w-full font-normal inline-flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Add Address</span>
        </button>
    </div>

</div>
