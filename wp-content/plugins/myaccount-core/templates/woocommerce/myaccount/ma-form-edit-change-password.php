<?php require_once __DIR__ . '/partials/form-field-icons.php'; ?>
<template id="form-change-password" x-data>
        <form
                x-data="passwordChangeForm()"
                class="ma-form ma-change-password__form"
                @keyup.enter="handleSubmit()"
                @submit.prevent="handleSubmit"
        >
            <div class="ma-change-password__header">
                <h2 class="apl-heading-chip-sm">Change Password</h2>
                <button type="button" class="ma-btn ma-btn--ghost" @click="$store.popup.closePopup()" aria-label="<?php esc_attr_e( 'Close', 'woocommerce' ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none" aria-hidden="true">
                        <path d="M9.46584 8.12341L15.6959 1.89301C16.1014 1.48775 16.1014 0.832499 15.6959 0.427237C15.2907 0.0219756 14.6354 0.0219756 14.2302 0.427237L7.99991 6.65763L1.76983 0.427237C1.36438 0.0219756 0.709335 0.0219756 0.304082 0.427237C-0.101361 0.832499 -0.101361 1.48775 0.304082 1.89301L6.53416 8.12341L0.304082 14.3538C-0.101361 14.7591 -0.101361 15.4143 0.304082 15.8196C0.506044 16.0217 0.771594 16.1233 1.03695 16.1233C1.30231 16.1233 1.56767 16.0217 1.76983 15.8196L7.99991 9.58918L14.2302 15.8196C14.4323 16.0217 14.6977 16.1233 14.963 16.1233C15.2284 16.1233 15.4938 16.0217 15.6959 15.8196C16.1014 15.4143 16.1014 14.7591 15.6959 14.3538L9.46584 8.12341Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>

            <div class="ma-form__field">
                <label for="change-password-current" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'Password', 'woocommerce' ); ?></label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_lock_closed(); ?></span>
                    <input
                            id="change-password-current"
                            x-model="formData.currentPassword"
                            type="password" class="woocommerce-Input woocommerce-Input--text input-text ma-form__input"
                            autocomplete="username"
                            @blur="handler.validateField('currentPassword')"
                            :class="{'field-invalid': errors.currentPassword}"
                    />
                </div>
                <span x-validate-error="{message: errors.currentPassword, touched: touched.currentPassword}"></span>
            </div>

            <div class="ma-form__field">
                <label for="change-password-new" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'New Password', 'woocommerce' ); ?></label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_lock_closed(); ?></span>
                    <input
                            id="change-password-new"
                            x-model="formData.newPassword"
                            type="password" class="woocommerce-Input woocommerce-Input--text input-text ma-form__input"
                            autocomplete="new-password"
                            @blur="handler.validateField('newPassword')"
                            :class="{'field-invalid': errors.newPassword}"
                    />
                </div>
                <span x-validate-error="{message: errors.newPassword, touched: touched.newPassword}"></span>
            </div>

            <div class="ma-form__field">
                <label for="change-password-confirm" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'Confirm Password', 'woocommerce' ); ?></label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_lock_closed(); ?></span>
                    <input
                            id="change-password-confirm"
                            x-model="formData.confirmPassword"
                            type="password" class="woocommerce-Input woocommerce-Input--text input-text ma-form__input"
                            autocomplete="new-password"
                            @blur="handler.validateField('confirmPassword')"
                            :class="{'field-invalid': errors.confirmPassword}"
                    />
                </div>
                <span x-validate-error="{message: errors.confirmPassword, touched: touched.confirmPassword}"></span>
            </div>

            <div class="ma-change-password__remember-row">
                <label class="ma-form__checkbox">
                    <input x-model="formData.keepSignedIn" type="checkbox" id="keep-signed-in" />
                    <span class="ma-form__checkbox-box"></span>
                    <span class="ma-form__checkbox-label"><span class="ma-change-password__remember-text">Keep me signed in. <br/><i class="ma-change-password__remember-note">Uncheck if you are using a public device</i></span></span>
                </label>
            </div>

            <p class="ma-form__hint ma-u-muted" style="text-align: center;"><?php esc_html_e( 'Password must be 8-25 characters.', 'woocommerce' ); ?></p>
            <div class="ma-form-actions">
                <button type="submit" class="ma-btn ma-btn--primary ma-change-password__submit" :disabled="isFormSubmitting" :aria-busy="isFormSubmitting" x-loading="isFormSubmitting" data-loading-label="Saving...">
                    Save
                </button>
            </div>
        </form>
</template>

<script>
    window.changePasswordNonce = '<?php echo wp_create_nonce( 'change-password-action' ); ?>';
    window.ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
</script>
