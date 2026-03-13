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
require_once __DIR__ . '/partials/form-field-icons.php';
?>

<div class="ma-auth-container">
    <div class="ma-auth-container__notices">
        <?php wc_print_notices(); ?>
    </div>

    <div class="ma-auth">
        <?php wc_get_template( 'myaccount/page-heading.php', array( 'page_heading' => 'Set a New Password', 'page_description' => 'Choose a strong password for your account' ) ); ?>

        <form x-data="resetPassword" method="post" class="ma-form">

<!--        <p>--><?php //echo apply_filters( 'woocommerce_reset_password_message', esc_html__( 'Enter a new password below.', 'woocommerce' ) ); ?><!--</p>--><?php //// @codingStandardsIgnoreLine ?>

        <div class="ma-form__section ma-reset-password__section">
            <div class="ma-form__field ma-reset-password__field" :class="{'error': (touched.password && errors.password)}">
                <label for="reg_password" class="ma-form__label"><?php esc_html_e('New Password', 'woocommerce'); ?></label>
                <div class="ma-form__input-wrap">
                    <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_lock_closed(); ?></span>
                    <input x-model="formData.password" minlength="8" type="password"
                           class="input-text ma-form__input"
                           id="reg_password" autocomplete="new-password"
                           @keyup="handler.validateField('password')"
                           name="password_1"
                           autocomplete="new-password"
                    />
                    <span x-validate-error="{message: errors.password, touched: touched.password}"></span>
                    <p class="ma-reset-password__requirements-hint">Password must contain:</p>
                    <ul class="ma-reset-password__requirements">
                        <template x-for="(requirement, index) in Object.values(passwordRequirements)" :key="index" >
                            <li class="ma-reset-password__requirement">
                                <svg x-show="passedRequirements.includes(requirement.code) && touched.password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ma-reset-password__requirement-icon ma-reset-password__requirement-icon--pass">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                <svg x-show="!passedRequirements.includes(requirement.code) && touched.password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ma-reset-password__requirement-icon ma-reset-password__requirement-icon--fail">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                                <span class="ma-reset-password__requirement-text" :class="{
                                'ma-reset-password__requirement-text--pass' : passedRequirements.includes(requirement.code) && touched.password,
                                'ma-reset-password__requirement-text--fail' : !passedRequirements.includes(requirement.code) && touched.password,
                            }" x-text="requirement.message"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>

        <input type="hidden" name="password_2" x-model="formData.password" autocomplete="new-password" />

        <input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
        <input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>" />

        <div class="clear"></div>

        <?php do_action( 'woocommerce_resetpassword_form' ); ?>

        <div class="ma-form-actions">
            <input type="hidden" name="wc_reset_password" value="true" />
            <button type="submit" class="ma-btn ma-btn--primary" value="<?php esc_attr_e( 'Save', 'woocommerce' ); ?>"><?php esc_html_e( 'Save', 'woocommerce' ); ?></button>
        </div>

        <?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>

        </form>
    </div>
</div>
<?php
do_action( 'woocommerce_after_reset_password_form' );
