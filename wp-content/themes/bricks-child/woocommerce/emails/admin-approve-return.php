<?php
/**
 * Admin Approve Return Email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/admin-approve-return.php.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php _e( 'Dear customer,', 'woocommerce' ); ?></p>
<p><?php _e( 'Your return request has been approved.', 'woocommerce' ); ?></p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>