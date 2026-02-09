<?php
wc_get_template('myaccount/page-heading.php',
    [
        'page_heading' => 'Address Book',
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
            <div class="bg-[#F6F8FC] rounded-lg p-8">
                <div class="text-xl mb-3 text-black">Saved Address</div>
                <div class="text-base text-black" x-text="`${address.fname} ${address.lname}`">John Shah</div>
                <div class="text-base" x-text="address.phone">8980252445</div>
                <div class="text-base mb-4" x-text="`${address.address} ${address.city} ${address.region} ${address.country} ${address.postalCode}`">1/4 Pragatinagar Flats, opp. jain derasar, near Jain derasar, Vijaynagar road</div>
                <div class="border-t my-8"></div>
                <div class="flex flex-row gap-4">
                    <span x-text="address.default ? 'Default' : 'Set as default'"
                          class="cursor-pointer"
                          :class="address.default ? 'font-semibold':''"
                          @click="address.default ? false : $store.userAddress.setDefault(address.id, true)">
</span>
                    |
                    <span class="cursor-pointer" @click="
                        $store.userAddress.startEdit(address.id)
                        $store.popup.openPopup(document.getElementById('edit-address').innerHTML)
                        ">
                            Edit</span>
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
                class="button slim max-w-[450px] w-full font-normal">Add Address</button>
    </div>

</div>






