<?php
/**
 * View Order
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

$notes = $order->get_customer_order_notes();
$tracking_module  = MyAccount_Core_Tracking_Module::instance();
$tracking_entries = $tracking_module->get_entries( $order );

if ( ! empty( $tracking_entries ) ) {
	$tracking_module->maybe_suppress_view_order_output( $order );
}

// Order again is shown in order-details-items-summary; avoid duplicate from after_order_table on this endpoint.
remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button', 10 );

wc_get_template(
	'myaccount/page-heading.php',
	array(
		'page_heading'      => sprintf(
			/* translators: %s: order number */
			__( 'Order %s', 'woocommerce' ),
			'#' . $order->get_order_number()
		),
		'page_description'  => __( 'Status, items, and updates for this order.', 'myaccount-core' ),
		'page_heading_icon' => 'order',
	)
);

?>

<div class="ma-view-order">
	<?php wc_get_template( 'order/order-details-header.php', array( 'order' => $order ) ); ?>
	<?php wc_get_template( 'order/order-status-card.php', array( 'order' => $order ) ); ?>
	<?php wc_get_template( 'order/order-tracking-block.php', array( 'order' => $order, 'tracking_entries' => $tracking_entries ) ); ?>
	<?php wc_get_template( 'order/order-details-items-summary.php', array( 'order' => $order ) ); ?>
	<?php do_action( 'myaccount_core_view_order_after_items_summary', $order ); ?>
</div>

<?php if ( $notes ) : ?>
	<h2><?php esc_html_e( 'Order updates', 'woocommerce' ); ?></h2>
	<ol class="woocommerce-OrderUpdates commentlist notes">
		<?php foreach ( $notes as $note ) : ?>
		<li class="woocommerce-OrderUpdate comment note">
			<div class="woocommerce-OrderUpdate-inner comment_container">
				<div class="woocommerce-OrderUpdate-text comment-text">
					<p class="woocommerce-OrderUpdate-meta meta"><?php echo date_i18n( esc_html__( 'l jS \o\f F Y, h:ia', 'woocommerce' ), strtotime( $note->comment_date ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
					<div class="woocommerce-OrderUpdate-description description">
						<?php echo wpautop( wptexturize( $note->comment_content ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</div>
		</li>
		<?php endforeach; ?>
	</ol>
<?php endif; ?>

<?php do_action( 'woocommerce_view_order', $order_id ); ?>
