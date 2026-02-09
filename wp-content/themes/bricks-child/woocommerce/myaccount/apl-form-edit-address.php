<?php
/**
 * Alpine Data
 * const userAddress
 */
?>

<template id="edit-address" x-data>
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-lg font-semibold" x-text="$store.userAddress.form.title">Add Address</h2>
        <button @click="$store.popup.closePopup()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                <path d="M9.46584 8.12341L15.6959 1.89301C16.1014 1.48775 16.1014 0.832499 15.6959 0.427237C15.2907 0.0219756 14.6354 0.0219756 14.2302 0.427237L7.99991 6.65763L1.76983 0.427237C1.36438 0.0219756 0.709335 0.0219756 0.304082 0.427237C-0.101361 0.832499 -0.101361 1.48775 0.304082 1.89301L6.53416 8.12341L0.304082 14.3538C-0.101361 14.7591 -0.101361 15.4143 0.304082 15.8196C0.506044 16.0217 0.771594 16.1233 1.03695 16.1233C1.30231 16.1233 1.56767 16.0217 1.76983 15.8196L7.99991 9.58918L14.2302 15.8196C14.4323 16.0217 14.6977 16.1233 14.963 16.1233C15.2284 16.1233 15.4938 16.0217 15.6959 15.8196C16.1014 15.4143 16.1014 14.7591 15.6959 14.3538L9.46584 8.12341Z" fill="#4D4D4D"/>
            </svg>
        </button>
    </div>
    <!-- Modal Body -->
    <form class="flex flex-col underline-form gap-5">
        <div>
            <label for="first-name">First name</label>
            <div>
                <input x-model="$store.userAddress.editAddress.fname" type="text" name="first-name" id="first-name" autocomplete="given-name" />
            </div>
        </div>

        <div>
            <label for="last-name">Last name</label>
            <div>
                <input x-model="$store.userAddress.editAddress.lname"  type="text" name="last-name" id="last-name" autocomplete="family-name" />
            </div>
        </div>

        <div >
            <label for="address">Address</label>
            <div>
                <input x-init="$store.userAddress.transformToGoogleAutoComplete($el)" x-model="$store.userAddress.editAddress.address" type="text" name="address" id="address" autocomplete="address" />
            </div>
        </div>

        <div  x-data="{ countries: window.countries }">
            <label for="country">Country</label>
            <div>
                <select x-model="$store.userAddress.editAddress.country" id="country" name="country" autocomplete="country-name">
                    <template x-for="country in countries" :key="country">
                        <option x-bind:value="country" x-text="country"></option>
                    </template>
                </select>
            </div>
        </div>

        <div >
            <label for="address2">Apartment, suite, etc. (optional)</label>
            <div>
                <input x-model="$store.userAddress.editAddress.address2" type="text" name="address2" id="address2" autocomplete="address2">
            </div>
        </div>

        <div>
            <label for="city">City</label>
            <div>
                <input x-model="$store.userAddress.editAddress.city" type="text" name="city" id="city" autocomplete="address-level2" />
            </div>
        </div>

        <div >
            <label for="region">State / Province</label>
            <div>
                <input x-model="$store.userAddress.editAddress.region" type="text" name="region" id="region" autocomplete="address-level1" />
            </div>
        </div>

        <div >
            <label for="postal-code">ZIP / Postal code</label>
            <div>
                <input x-model="$store.userAddress.editAddress.postalCode" type="text" name="postal-code" id="postal-code" autocomplete="postal-code" />
            </div>
        </div>

        <div>
            <label for="phone-number">Phone Number</label>
            <div>
                <input @input="$store.userAddress.formatUSPhoneNumber()" x-model="$store.userAddress.editAddress.phone" type="text" name="phone-number" maxlength="14" />
            </div>
        </div>


        <template x-if="$store.userAddress.form.action === 'add'">
            <div class="flex items-center">
                <label class="flex items-center jk-checkbox">
                    <input
                            x-model="$store.userAddress.editAddress.default"
                            class="mr-2 leading-tight"
                            type="checkbox"
                    />
                    <span>Make this my default shipping address.</span>
                </label>
            </div>
        </template>

    </form>

    <!-- Modal Footer -->
    <div class="flex flex-col gap-5 py-8">

        <template x-if="$store.userAddress.form.action === 'edit'">
            <button
                    class="button slim light"
                    @click="$store.userAddress.remove($store.userAddress.editAddress.id)"
            >Remove</button>
        </template>

        <!-- <template x-if="$store.userAddress.form.action === 'add'">
            <div class="text-sm">By adding your address, you agree to our terms of service and privacy policy.</div>
        </template> -->
        <button @click="$store.userAddress.save()" class="button slim" x-text="$store.userAddress.form.buttonSaveLabel">Add</button>
    </div>
</template>



