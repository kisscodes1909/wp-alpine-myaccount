<?php
/**
 * Admin New Request Return Email
 **
 * @see https://docs.woocommerce.com/document/template-structure/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

    <p><?php _e( 'Dear Admin,', 'woocommerce' ); ?></p>
    <p><?php _e( 'A new return request has been submitted.', 'woocommerce' ); ?></p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>