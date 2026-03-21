<?php
/**
 * View Order
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

$notes = $order->get_customer_order_notes();
$tracking_resolver = MyAccount_Core_Tracking_Resolver::instance();
$tracking_entries  = $tracking_resolver->get_entries( $order );
$returns_service   = MyAccount_Core_Returns::instance();
$returns_policy    = $returns_service->get_policy_context( $order );
$return_requests   = $returns_service->get_requests( $order );
$eligible_items    = $returns_service->get_eligible_items( $order );

if ( ! empty( $tracking_entries ) ) {
	$tracking_resolver->maybe_suppress_view_order_output( $order );
}

$returns_script_handle = '';
if ( wp_script_is( 'myaccount-core-js-endpoint', 'enqueued' ) ) {
	$returns_script_handle = 'myaccount-core-js-endpoint';
} elseif ( wp_script_is( 'alpine-bundle', 'enqueued' ) ) {
	$returns_script_handle = 'alpine-bundle';
}

if ( '' !== $returns_script_handle ) {
	wp_localize_script(
		$returns_script_handle,
		'viewOrderReturnsData',
		array(
			'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
			'nonce'        => wp_create_nonce( 'submit-return-request' ),
			'orderId'      => $order->get_id(),
			'policy'       => $returns_policy,
			'requests'     => $return_requests,
			'eligibleItems' => $eligible_items,
			'requestTypes' => $returns_service->get_request_type_labels(),
			'i18n'         => array(
				'selectItem'      => __( 'Please select at least one item to return or exchange.', 'myaccount-core' ),
				'missingReason'   => __( 'Please tell us why you want to return or exchange these items.', 'myaccount-core' ),
				'invalidQuantity' => __( 'One or more quantities are not valid for return.', 'myaccount-core' ),
				'genericError'    => __( 'Something went wrong. Please try again.', 'myaccount-core' ),
			),
		)
	);
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
	<?php
	wc_get_template(
		'order/order-returns.php',
		array(
			'order'             => $order,
			'section_id'        => 'ma-view-order-returns-' . (int) $order->get_id(),
			'existing_requests' => $return_requests,
			'eligible_items'    => $eligible_items,
			'policy'            => $returns_policy,
			'request_types'     => $returns_service->get_request_type_labels(),
		)
	);
	?>
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
