<?php
/**
 * Order again (view-order summary actions). Matches Woo logic; styled as ma-btn.
 *
 * @package MyAccount_Core
 * @see woocommerce_order_again_button()
 */

defined( 'ABSPATH' ) || exit;
?>
<p class="ma-order-details-items-summary__order-again order-again">
	<a href="<?php echo esc_url( $order_again_url ); ?>" class="ma-btn ma-btn--secondary ma-btn--block"><?php esc_html_e( 'Order again', 'woocommerce' ); ?></a>
</p>
