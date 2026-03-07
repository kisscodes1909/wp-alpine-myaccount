<?php
/**
 * Alpine Data
 * const userAddress
 */
?>
<style>
.ma-form.ma-form-address .address-autocomplete-wrapper {
    width: 100%;
}

.ma-form.ma-form-address .address-autocomplete-wrapper gmp-place-autocomplete {
    width: 100%;
    min-height: 50px;
    display: block;
    border-radius: 0;
    border: 1px solid var(--ma-border);
    background-color: var(--ma-surface-soft);
    transition: border-color 150ms cubic-bezier(0.4, 0, 0.2, 1), box-shadow 150ms cubic-bezier(0.4, 0, 0.2, 1);
    color-scheme: light;
    --gmp-mat-color-surface: var(--ma-surface-soft);
    --gmp-mat-color-on-surface: var(--ma-text-strong);
    --gmp-mat-color-on-surface-variant: var(--ma-text-muted-dark);
    --gmp-mat-color-primary: var(--ma-text-charcoal);
    --gmp-mat-color-outline-decorative: transparent;
    --gmp-mat-font-family: inherit;
}

.ma-form.ma-form-address .address-autocomplete-wrapper gmp-place-autocomplete:focus-within {
    border-color: var(--ma-border-focus);
    box-shadow: 0 0 0 1px var(--ma-border-divider);
}

.apl-address-line2-toggle {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--ma-info-text);
    font-size: 15px;
    line-height: 1.25;
    font-weight: 500;
    text-decoration: none;
}

.apl-address-line2-toggle:hover {
    opacity: 0.9;
}
</style>
<template id="edit-address" x-data>
    <div class="flex justify-between items-center mb-8">
        <h2 class="apl-heading-chip-sm" x-text="$store.userAddress.form.title">Add Address</h2>
        <button @click="$store.popup.closePopup()" style="color: var(--ma-text-charcoal);">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                <path d="M9.46584 8.12341L15.6959 1.89301C16.1014 1.48775 16.1014 0.832499 15.6959 0.427237C15.2907 0.0219756 14.6354 0.0219756 14.2302 0.427237L7.99991 6.65763L1.76983 0.427237C1.36438 0.0219756 0.709335 0.0219756 0.304082 0.427237C-0.101361 0.832499 -0.101361 1.48775 0.304082 1.89301L6.53416 8.12341L0.304082 14.3538C-0.101361 14.7591 -0.101361 15.4143 0.304082 15.8196C0.506044 16.0217 0.771594 16.1233 1.03695 16.1233C1.30231 16.1233 1.56767 16.0217 1.76983 15.8196L7.99991 9.58918L14.2302 15.8196C14.4323 16.0217 14.6977 16.1233 14.963 16.1233C15.2284 16.1233 15.4938 16.0217 15.6959 15.8196C16.1014 15.4143 16.1014 14.7591 15.6959 14.3538L9.46584 8.12341Z" fill="currentColor"/>
            </svg>
        </button>
    </div>
    <form class="ma-form ma-form-address flex flex-col gap-4" x-data="{ showAddress2: false }" x-init="showAddress2 = !!$store.userAddress.editAddress.address2">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="first-name">First name</label>
                <div>
                    <input class="ma-form__input" x-model="$store.userAddress.editAddress.fname" type="text" name="first-name" id="first-name" autocomplete="given-name" />
                </div>
            </div>
            <div>
                <label for="last-name">Last name</label>
                <div>
                    <input class="ma-form__input" x-model="$store.userAddress.editAddress.lname" type="text" name="last-name" id="last-name" autocomplete="family-name" />
                </div>
            </div>
        </div>

        <div>
            <label for="address">Address</label>
            <template x-if="$store.userAddress.autocompleteEnabled">
                <div class="address-autocomplete-wrapper"></div>
            </template>
            <template x-if="!$store.userAddress.autocompleteEnabled">
                <div>
                    <input class="ma-form__input" x-model="$store.userAddress.editAddress.address" type="text" name="address" id="address" autocomplete="street-address" />
                </div>
            </template>
        </div>

        <div>
            <button type="button" class="apl-address-line2-toggle" @click="showAddress2 = !showAddress2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path x-show="!showAddress2" stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    <path x-show="showAddress2" stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                </svg>
                <span x-text="showAddress2 ? 'Hide Address Line 2 (optional)' : 'Add Address Line 2 (optional)'"></span>
            </button>
        </div>

        <div x-show="showAddress2" x-transition>
            <label for="address2">Apartment, suite, etc. (optional)</label>
            <div>
                <input class="ma-form__input" x-model="$store.userAddress.editAddress.address2" type="text" name="address2" id="address2" autocomplete="address2">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="country">Country / Region</label>
                <div>
                    <select class="ma-form__input" x-model="$store.userAddress.editAddress.country" id="country" name="country" autocomplete="country-name">
                        <template x-for="(countryName, countryCode) in $store.userAddress.countries" :key="countryCode">
                            <option x-bind:value="countryName" x-text="countryName"></option>
                        </template>
                    </select>
                </div>
            </div>
            <div>
                <label for="postal-code">ZIP Code</label>
                <div>
                    <input class="ma-form__input" x-model="$store.userAddress.editAddress.postalCode" type="text" name="postal-code" id="postal-code" autocomplete="postal-code" />
                </div>
            </div>
            <div>
                <label for="region">State</label>
                <div>
                    <input class="ma-form__input" x-model="$store.userAddress.editAddress.region" type="text" name="region" id="region" autocomplete="address-level1" />
                </div>
            </div>
        </div>

        <div>
            <label for="city">Town / City</label>
            <div>
                <input class="ma-form__input" x-model="$store.userAddress.editAddress.city" type="text" name="city" id="city" autocomplete="address-level2" />
            </div>
        </div>

        <div>
            <label for="phone-number">Phone</label>
            <div>
                <input class="ma-form__input" @input="$store.userAddress.formatUSPhoneNumber()" x-model="$store.userAddress.editAddress.phone" type="text" name="phone-number" id="phone-number" maxlength="14" />
            </div>
        </div>

        <template x-if="$store.userAddress.form.action === 'add'">
            <div class="flex items-center pt-1">
                <label class="jk-checkbox cursor-pointer">
                    <input x-model="$store.userAddress.editAddress.default" type="checkbox" />
                    <span>Make this my default shipping address.</span>
                </label>
            </div>
        </template>
    </form>

    <div class="ma-form-actions ma-form-actions--two">
        <template x-if="$store.userAddress.form.action === 'edit'">
            <button type="button"
                    class="button light inline-flex items-center justify-center gap-2"
                    :disabled="$store.userAddress.removing"
                    :aria-busy="$store.userAddress.removing"
                    x-loading="$store.userAddress.removing"
                    data-loading-label="Removing..."
                    @click="$store.userAddress.remove($store.userAddress.editAddress.id)"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0115.916 21H8.084a2.25 2.25 0 01-2.244-1.327L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0V4.5A2.25 2.25 0 0013.5 2.25h-3A2.25 2.25 0 008.25 4.5v.893m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                <span>Remove</span>
            </button>
        </template>

        <button type="button" @click="$store.userAddress.save()" class="button inline-flex items-center justify-center gap-2"
                :disabled="$store.userAddress.saving"
                :aria-busy="$store.userAddress.saving"
                x-loading="$store.userAddress.saving"
                data-loading-label="Saving...">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
            <span x-text="$store.userAddress.form.buttonSaveLabel">Add</span>
        </button>
    </div>
</template>
