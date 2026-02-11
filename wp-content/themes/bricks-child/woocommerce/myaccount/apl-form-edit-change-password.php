<template id="form-change-password" x-data>
        <form
                x-data="passwordChangeForm()"
                class="w-full apl-form-refined flex flex-col gap-8"
                @keyup.enter="handleSubmit()"
                @submit.prevent="handleSubmit"
                @keyup="validateForm()"

        >
            <div class="flex justify-between items-center">
                <h2 class="apl-heading-chip-sm">Change Password</h2>
                <button type="button" @click="$store.popup.closePopup()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                        <path d="M9.46584 8.12341L15.6959 1.89301C16.1014 1.48775 16.1014 0.832499 15.6959 0.427237C15.2907 0.0219756 14.6354 0.0219756 14.2302 0.427237L7.99991 6.65763L1.76983 0.427237C1.36438 0.0219756 0.709335 0.0219756 0.304082 0.427237C-0.101361 0.832499 -0.101361 1.48775 0.304082 1.89301L6.53416 8.12341L0.304082 14.3538C-0.101361 14.7591 -0.101361 15.4143 0.304082 15.8196C0.506044 16.0217 0.771594 16.1233 1.03695 16.1233C1.30231 16.1233 1.56767 16.0217 1.76983 15.8196L7.99991 9.58918L14.2302 15.8196C14.4323 16.0217 14.6977 16.1233 14.963 16.1233C15.2284 16.1233 15.4938 16.0217 15.6959 15.8196C16.1014 15.4143 16.1014 14.7591 15.6959 14.3538L9.46584 8.12341Z" fill="#4D4D4D"/>
                    </svg>
                </button>
            </div>

            <!-- Current Password -->
            <div x-data="{showPassword:false}">
                <label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?></label>
                <div class="relative" x-data="{showPassword:false}">
                    <input
                            x-model="currentPassword"
                            type="password" class="woocommerce-Input woocommerce-Input--text input-text"
                            autocomplete="username"
                            @keyup="handler.validateField('password')"
                            :type="showPassword === true ? 'text' : 'password'"
                            class="pr-5"
                            :class="{'field-invalid': errors.currentPassword}"

                    />
                    <div class="password-toggle">
                        <!-- Validate Field -->
<!--                        <div x-validate-icon="{message: errors.password, touched:touched.password}"></div>-->
                        <!-- Password Eye -->
                        <div class="block w-10 h-10 flex items-center justify-center" x-password-eye="showPassword" @click="showPassword=!showPassword"></div>
                    </div>
                </div>
                <span class="text-red-600 mt-1 block" x-show="errors.currentPassword" x-text="errors.currentPassword"></span>
            </div>

            <!-- New Password -->
            <div x-data="{showPassword:false}">
                <label for="password"><?php esc_html_e( 'New Password', 'woocommerce' ); ?></label>
                <div class="relative" x-data="{showPassword:false}">
                    <input
                            x-model="newPassword"
                            type="password" class="woocommerce-Input woocommerce-Input--text input-text"
                            autocomplete="username"
                            @keyup="handler.validateField('password')"
                            :type="showPassword === true ? 'text' : 'password'"
                            class="pr-5"
                            :class="{'field-invalid': errors.newPassword}"

                    />
                    <div class="password-toggle">
                        <!-- Validate Field -->
                        <!--                        <div x-validate-icon="{message: errors.password, touched:touched.password}"></div>-->
                        <!-- Password Eye -->
                        <div class="block w-10 h-10 flex items-center justify-center" x-password-eye="showPassword" @click="showPassword=!showPassword"></div>
                    </div>
                </div>
                <span class="text-red-600 mt-1 block" x-show="errors.newPassword" x-text="errors.newPassword"></span>
            </div>

            <!-- Confirm Password -->
            <div x-data="{showPassword:false}">
                <label for="password"><?php esc_html_e( 'Confirm Password', 'woocommerce' ); ?></label>
                <div class="relative" x-data="{showPassword:false}">
                    <input
                            x-model="confirmPassword"
                            type="password" class="woocommerce-Input woocommerce-Input--text input-text"
                            autocomplete="username"
                            @keyup="handler.validateField('password')"
                            :type="showPassword === true ? 'text' : 'password'"
                            class="pr-5"
                            :class="{'field-invalid': errors.confirmPassword}"

                    />
                    <div class="password-toggle">
                        <!-- Validate Field -->
                        <!--                        <div x-validate-icon="{message: errors.password, touched:touched.password}"></div>-->
                        <!-- Password Eye -->
                        <div class="block w-10 h-10 flex items-center justify-center" x-password-eye="showPassword" @click="showPassword=!showPassword"></div>
                    </div>
                </div>
                <span class="text-red-600 mt-1 block" x-show="errors.confirmPassword" x-text="errors.confirmPassword"></span>
            </div>

<!--             Keep Me Signed In Checkbox-->
            <div class="flex items-center">
                <label class="jk-checkbox">
                    <input
                            x-model="keepSignedIn"
                            type="checkbox"
                            id="keep-signed-in"
                            />
                    <span><span class="leading-snug">Keep me signed in. <br/><i class="text-sm not-italic">Uncheck if you are using a public device<i></i></span></span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="text-center">(Password must be 8-25 characters.)</div>
            <div class="apl-form-actions">
                <button class="button">Save</button>
            </div>
        </form>
</template>

<script>
    // Localize data for Alpine component (passwordChangeForm is registered in alpine/components/forms/passwordChangeForm.js)
    window.changePasswordNonce = '<?php echo wp_create_nonce('change-password-action'); ?>';
    window.ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
