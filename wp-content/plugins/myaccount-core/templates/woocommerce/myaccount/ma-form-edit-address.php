<?php
/**
 * Alpine Data
 * const userAddress
 */
require_once __DIR__ . '/partials/form-field-icons.php';
?>
<template id="edit-address" x-data>
    <div class="ma-address-form__header">
        <h2 class="ma-page-heading__title ma-page-heading__title--sm" x-text="$store.userAddress.form.title">Add Address</h2>
        <button type="button" class="ma-btn ma-btn--ghost" @click="$store.popup.closePopup()" aria-label="<?php esc_attr_e( 'Close', 'woocommerce' ); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none" aria-hidden="true">
                <path d="M9.46584 8.12341L15.6959 1.89301C16.1014 1.48775 16.1014 0.832499 15.6959 0.427237C15.2907 0.0219756 14.6354 0.0219756 14.2302 0.427237L7.99991 6.65763L1.76983 0.427237C1.36438 0.0219756 0.709335 0.0219756 0.304082 0.427237C-0.101361 0.832499 -0.101361 1.48775 0.304082 1.89301L6.53416 8.12341L0.304082 14.3538C-0.101361 14.7591 -0.101361 15.4143 0.304082 15.8196C0.506044 16.0217 0.771594 16.1233 1.03695 16.1233C1.30231 16.1233 1.56767 16.0217 1.76983 15.8196L7.99991 9.58918L14.2302 15.8196C14.4323 16.0217 14.6977 16.1233 14.963 16.1233C15.2284 16.1233 15.4938 16.0217 15.6959 15.8196C16.1014 15.4143 16.1014 14.7591 15.6959 14.3538L9.46584 8.12341Z" fill="currentColor"/>
            </svg>
        </button>
    </div>
    <form class="ma-form ma-form-address ma-address-form"
          x-data="{ showAddress2: false }"
          x-init="showAddress2 = $store.userAddress.initEditAddressForm()">
        <div class="ma-address-form__grid ma-address-form__grid--two">
            <div class="ma-form__field">
                <label for="first-name" class="ma-form__label ma-form__label--required">First name</label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_user(); ?></span>
                    <input class="ma-form__input" x-model="$store.userAddress.editAddress.fname" type="text" name="first-name" id="first-name" autocomplete="given-name" />
                </div>
            </div>
            <div class="ma-form__field">
                <label for="last-name" class="ma-form__label ma-form__label--required">Last name</label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_user(); ?></span>
                    <input class="ma-form__input" x-model="$store.userAddress.editAddress.lname" type="text" name="last-name" id="last-name" autocomplete="family-name" />
                </div>
            </div>
        </div>

        <div class="ma-form__field">
            <label for="address" class="ma-form__label ma-form__label--required">Address</label>
            <div class="ma-form__input-wrap">
                <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_map_pin(); ?></span>
                <input class="ma-form__input" x-model="$store.userAddress.editAddress.address" type="text" name="address" id="address" autocomplete="street-address" />
            </div>
        </div>

        <div>
            <button type="button" class="ma-btn ma-btn--ghost apl-address-line2-toggle" @click="showAddress2 = !showAddress2">
                <svg xmlns="http://www.w3.org/2000/svg" class="ma-address-form__icon-sm" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path x-show="!showAddress2" stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    <path x-show="showAddress2" stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                </svg>
                <span x-text="showAddress2 ? 'Hide Address Line 2 (optional)' : 'Add Address Line 2 (optional)'"></span>
            </button>
        </div>

        <div class="ma-form__field" x-show="showAddress2" x-transition>
            <label for="address2" class="ma-form__label">Apartment, suite, etc. (optional)</label>
            <div class="ma-form__input-wrap">
                <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_map_pin(); ?></span>
                <input class="ma-form__input" x-model="$store.userAddress.editAddress.address2" type="text" name="address2" id="address2" autocomplete="address2">
            </div>
        </div>

        <div class="ma-address-form__grid ma-address-form__grid--three">
            <div class="ma-form__field">
                <label for="country" class="ma-form__label ma-form__label--required">Country / Region</label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_globe_alt(); ?></span>
                    <select class="ma-form__input" x-model="$store.userAddress.editAddress.country" id="country" name="country" autocomplete="country">
                        <template x-for="(countryName, countryCode) in $store.userAddress.countries" :key="countryCode">
                            <option :value="countryCode" :selected="countryCode === $store.userAddress.editAddress.country" x-text="countryName"></option>
                        </template>
                    </select>
                </div>
            </div>
            <div class="ma-form__field">
                <label for="postal-code" class="ma-form__label">ZIP Code</label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_map_pin(); ?></span>
                    <input class="ma-form__input" x-model="$store.userAddress.editAddress.postalCode" type="text" name="postal-code" id="postal-code" autocomplete="postal-code" />
                </div>
            </div>
            <div class="ma-form__field">
                <label for="region" class="ma-form__label">State</label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_map_pin(); ?></span>
                    <input class="ma-form__input" x-model="$store.userAddress.editAddress.region" type="text" name="region" id="region" autocomplete="address-level1" />
                </div>
            </div>
        </div>

        <div class="ma-form__field">
            <label for="city" class="ma-form__label ma-form__label--required">Town / City</label>
            <div class="ma-form__input-wrap">
                <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_map_pin(); ?></span>
                <input class="ma-form__input" x-model="$store.userAddress.editAddress.city" type="text" name="city" id="city" autocomplete="address-level2" />
            </div>
        </div>

        <div class="ma-form__field">
            <label for="phone-number" class="ma-form__label ma-form__label--required">Phone</label>
            <div class="ma-form__input-wrap">
                <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_phone(); ?></span>
                <input class="ma-form__input" @input="$store.userAddress.formatUSPhoneNumber()" x-model="$store.userAddress.editAddress.phone" type="text" name="phone-number" id="phone-number" maxlength="14" />
            </div>
        </div>

        <template x-if="$store.userAddress.form.action === 'add'">
            <div class="ma-address-form__default-row">
                <label class="ma-form__checkbox">
                    <input x-model="$store.userAddress.editAddress.default" type="checkbox" />
                    <span class="ma-form__checkbox-box"></span>
                    <span class="ma-form__checkbox-label ma-address-form__checkbox-label">Make this my default shipping address.</span>
                </label>
            </div>
        </template>
    </form>

    <div class="ma-form-actions ma-form-actions--two">
        <template x-if="$store.userAddress.form.action === 'edit'">
            <button type="button"
                    class="ma-btn ma-btn--secondary-light ma-address-form__action-button"
                    :disabled="$store.userAddress.removing"
                    :aria-busy="$store.userAddress.removing"
                    x-loading="$store.userAddress.removing"
                    data-loading-label="Removing..."
                    @click="$store.userAddress.remove($store.userAddress.editAddress.id)"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="ma-address-form__icon-sm" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0115.916 21H8.084a2.25 2.25 0 01-2.244-1.327L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0V4.5A2.25 2.25 0 0013.5 2.25h-3A2.25 2.25 0 008.25 4.5v.893m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                <span>Remove</span>
            </button>
        </template>

        <button type="button" @click="$store.userAddress.save()" class="ma-btn ma-btn--primary ma-address-form__action-button"
                :disabled="$store.userAddress.saving"
                :aria-busy="$store.userAddress.saving"
                x-loading="$store.userAddress.saving"
                data-loading-label="Saving...">
            <svg xmlns="http://www.w3.org/2000/svg" class="ma-address-form__icon-sm" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
            <span x-text="$store.userAddress.form.buttonSaveLabel">Add</span>
        </button>
    </div>
</template>
