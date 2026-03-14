<?php
/**
 * Static password rules (signup + reset). Same rules as SignupHandler / ResetPasswordHandler.
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;
?>
<p class="ma-password-rules__title"><?php esc_html_e( 'Choose a password that includes:', 'woocommerce' ); ?></p>
<ul class="ma-password-rules" aria-label="<?php esc_attr_e( 'Tips for a strong password', 'woocommerce' ); ?>">
	<li class="ma-password-rules__item"><?php esc_html_e( '8 or more characters', 'woocommerce' ); ?></li>
	<li class="ma-password-rules__item"><?php esc_html_e( 'One uppercase letter', 'woocommerce' ); ?></li>
	<li class="ma-password-rules__item"><?php esc_html_e( 'One number', 'woocommerce' ); ?></li>
	<li class="ma-password-rules__item"><?php esc_html_e( 'One lowercase letter', 'woocommerce' ); ?></li>
</ul>
