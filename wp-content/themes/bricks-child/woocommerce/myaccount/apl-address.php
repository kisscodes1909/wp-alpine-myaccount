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
         class="fixed top-14 right-5 bg-black text-white px-4 py-3 rounded shadow-lg z-[1000]">
        <p x-text="$store.userAddress.notificationMessage"></p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <template x-for="address in $store.userAddress.addresses">
            <div class="bg-[#F6F8FC] rounded-lg p-6 flex flex-col">
                <!-- Header: Name & Default Badge -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base font-bold text-gray-900" x-text="`${address.fname} ${address.lname}`"></h3>
                    <template x-if="address.default">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd" />
                            </svg>
                            Default
                        </span>
                    </template>
                </div>

                <!-- Phone -->
                <p class="flex items-center gap-2 text-sm mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[14px] h-[14px] flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                    <span x-text="address.phone"></span>
                </p>

                <!-- Address -->
                <p class="flex items-start gap-2 text-sm mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[14px] h-[14px] flex-shrink-0 mt-[3px]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                    <span x-text="`${address.address} ${address.city} ${address.region} ${address.country} ${address.postalCode}`"></span>
                </p>

                <!-- Divider + Footer Actions -->
                <div class="mt-auto border-t border-gray-200 pt-4 flex items-center"
                     :class="address.default ? 'justify-end' : 'justify-between'">
                    <!-- Set as Default -->
                    <button x-show="!address.default"
                            @click="$store.userAddress.setDefault(address.id, true)"
                            class="inline-flex items-center gap-1.5 text-sm transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                        </svg>
                        Set as Default
                    </button>
                    <!-- Edit -->
                    <button @click="
                        $store.userAddress.startEdit(address.id);
                        $store.popup.openPopup(document.getElementById('edit-address').innerHTML);
                        "
                        class="inline-flex items-center gap-1.5 text-sm transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        Edit
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
                class="button slim max-w-[450px] w-full font-normal inline-flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Add Address</span>
        </button>
    </div>

</div>





