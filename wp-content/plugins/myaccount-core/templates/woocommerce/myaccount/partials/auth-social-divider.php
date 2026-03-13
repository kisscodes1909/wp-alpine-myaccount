<?php
/**
 * Auth social buttons (Google, Apple) + "or" divider.
 * Used on login and signup views.
 *
 * @package MyAccount_Core
 * @param string $context Optional. 'login' or 'signup' for aria-labels. Default 'login'.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$auth_social_context = isset( $auth_social_context ) ? $auth_social_context : 'login';
$is_signup           = ( 'signup' === $auth_social_context );
$google_aria         = $is_signup ? __( 'Sign up with Google', 'woocommerce' ) : __( 'Sign in with Google', 'woocommerce' );
$apple_aria          = $is_signup ? __( 'Sign up with Apple', 'woocommerce' ) : __( 'Sign in with Apple', 'woocommerce' );
?>
<div class="ma-auth__social">
	<button type="button" class="ma-btn ma-btn--secondary-light ma-auth__social-btn" aria-label="<?php echo esc_attr( $google_aria ); ?>">
		<?php ma_form_icon_google(); ?>
		<span><?php esc_html_e( 'Google', 'woocommerce' ); ?></span>
	</button>
	<button type="button" class="ma-btn ma-btn--secondary-light ma-auth__social-btn" aria-label="<?php echo esc_attr( $apple_aria ); ?>">
		<?php ma_form_icon_apple(); ?>
		<span><?php esc_html_e( 'Apple', 'woocommerce' ); ?></span>
	</button>
</div>
<div class="ma-auth__divider" aria-hidden="true">
	<span class="ma-auth__divider-line"></span>
	<span class="ma-auth__divider-text"><?php esc_html_e( 'or', 'woocommerce' ); ?></span>
	<span class="ma-auth__divider-line"></span>
</div>
