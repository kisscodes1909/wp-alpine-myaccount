<?php
/**
 * Lost password reset form.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-reset-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_reset_password_form' );
?>

<div class="ma-auth-container">
    <div class="ma-auth-container__notices">
        <?php wc_print_notices(); ?>
    </div>

    <div class="ma-auth">
        <?php wc_get_template( 'myaccount/page-heading.php', array( 'page_heading' => 'Set a New Password', 'page_description' => 'Choose a strong password for your account' ) ); ?>

        <form x-data="resetPassword" method="post" class="ma-form">

<!--        <p>--><?php //echo apply_filters( 'woocommerce_reset_password_message', esc_html__( 'Enter a new password below.', 'woocommerce' ) ); ?><!--</p>--><?php //// @codingStandardsIgnoreLine ?>

        <div class="mb-8">
            <div class="mb-10" :class="{'error': (touched.password && errors.password)}">
                <label for="reg_password"><?php esc_html_e('New Password', 'woocommerce'); ?></label>
                <div class="relative" x-data="{showPassword:false}">
                    <input x-model="formData.password" minlength="8" type="password"
                           class="input-text ma-form__input"
                           id="reg_password" autocomplete="new-password"
                           @keyup="handler.validateField('password')"
                           :type="showPassword === true ? 'text' : 'password'"
                           name="password_1"
                           autocomplete="new-password"
                    />
                    <div class="password-toggle">
                        <!-- Password Eye -->
                        <span class="block w-10 h-10 flex items-center justify-center">
                    <svg x-show="!showPassword" @click="showPassword=!showPassword" class="cursor-pointer size-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>

                     <svg x-show="showPassword" @click="showPassword=!showPassword" class="cursor-pointer size-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </span>
                    </div>
                    <span x-validate-error="{message: errors.password, touched: touched.password}"></span>
                    <p class="mt-4">Password must contain:</p>
                    <ul class="grid grid-cols-1 md:grid-cols-2 list-none gap-4 md:gap-2">
                        <template x-for="(requirement, index) in Object.values(passwordRequirements)" :key="index" >
                            <li class="flex flex-row items-center gap-3">
                                <svg x-show="passedRequirements.includes(requirement.code) && touched.password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-green-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                <svg x-show="!passedRequirements.includes(requirement.code) && touched.password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-red-600 bg-white">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                                <span :class="{
                                'text-green-600' : passedRequirements.includes(requirement.code) && touched.password,
                                'text-red-600' : !passedRequirements.includes(requirement.code) && touched.password,
                            }" x-text="requirement.message"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>

        <input type="hidden" name="password_2" x-model="formData.password" autocomplete="new-password" />

<!--        <div class="mb-8">-->
<!--            <label for="password_2">--><?php //esc_html_e( 'Re-enter new password', 'woocommerce' ); ?><!--</label>-->
<!--            <input minlength="8" type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password_2" id="password_2" autocomplete="new-password" />-->
<!--        </div>-->

        <input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
        <input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>" />

        <div class="clear"></div>

        <?php do_action( 'woocommerce_resetpassword_form' ); ?>

        <div class="ma-form-actions">
            <input type="hidden" name="wc_reset_password" value="true" />
            <button type="submit" class="woocommerce-Button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" value="<?php esc_attr_e( 'Save', 'woocommerce' ); ?>"><?php esc_html_e( 'Save', 'woocommerce' ); ?></button>
        </div>

        <?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>

        </form>
    </div>
</div>
<?php
do_action( 'woocommerce_after_reset_password_form' );
