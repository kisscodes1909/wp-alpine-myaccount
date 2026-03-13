<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-lost-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
require_once __DIR__ . '/partials/form-field-icons.php';
?>

<div class="ma-auth-container">
    <div class="ma-auth-container__notices">
        <?php
        if ( isset( $_GET['reset-link-sent'] ) ) {
            wc_print_notice( esc_html__( 'Instructions to reset your password has been emailed to you.', 'woocommerce' ), 'notice' );
        } else {
            wc_print_notices();
        }
        ?>
    </div>

    <div class="ma-auth">
        <?php wc_get_template( 'myaccount/page-heading.php', array( 'page_heading' => 'Forgot Password', 'page_description' => 'Enter your email to receive a reset link' ) ); ?>

        <form x-data="lostPassword" method="post" class="woocommerce-ResetPassword lost_reset_password ma-form">

    <!--	<p>--><?php //echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' ) ); ?><!--</p>--><?php //// @codingStandardsIgnoreLine ?>

        <div class="ma-form__section">
            <div class="ma-form__field ma-lost-password__field" x-validate-field="{message: errors.email, touched:touched.email}">
                <label for="reg_email" class="ma-form__label"><?php esc_html_e('Email address', 'woocommerce'); ?></label>
                <div class="ma-form__input-wrap">
                <span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_envelope(); ?></span>
                <input id="reg_email" x-model="formData.email"
                       type="text" class="woocommerce-Input woocommerce-Input--text input-text ma-form__input"
                       autocomplete="email"
                       @blur="handler.validateField('email')"
                       name="user_login"
                />
                </div>
                <span x-validate-error="{message: errors.email, touched:touched.email}"></span>
            </div>
            <div class="clear"></div>
        </div>

        <?php do_action( 'woocommerce_lostpassword_form' ); ?>

        <div class="ma-form-actions ma-form-actions--two">
            <input type="hidden" name="wc_reset_password" value="true" />
            <button
                    :disabled="(Object.values(errors).length > 0  && Object.values(touched).length > 0) || Object.values(touched).length == 0 "
                    type="submit" class="ma-btn ma-btn--primary" value="<?php esc_attr_e( 'Reset password', 'woocommerce' ); ?>"><?php esc_html_e( 'Reset password', 'woocommerce' ); ?></button>
            <a href="<?php echo home_url('/') ?>" class="ma-btn ma-btn--secondary">Go Back</a>
        </div>

        <?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

        </form>
    </div>
</div>
<?php
do_action( 'woocommerce_after_lost_password_form' );
