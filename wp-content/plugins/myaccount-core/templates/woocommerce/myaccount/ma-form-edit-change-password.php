<?php require_once __DIR__ . '/partials/form-field-icons.php'; ?>
<template id="form-change-password" x-data>
        <form
                x-data="passwordChangeForm()"
                class="ma-form ma-change-password__form"
                @keyup.enter="handleSubmit()"
                @submit.prevent="handleSubmit"
        >
            <div class="ma-change-password__header">
                <h2 class="ma-page-heading__title ma-page-heading__title--sm"><?php esc_html_e( 'Change Password', 'woocommerce' ); ?></h2>
                <button type="button" class="ma-btn ma-btn--ghost" @click="$store.popup.closePopup()" aria-label="<?php esc_attr_e( 'Close', 'woocommerce' ); ?>">
                    <?php ma_form_icon_x_mark(); ?>
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
