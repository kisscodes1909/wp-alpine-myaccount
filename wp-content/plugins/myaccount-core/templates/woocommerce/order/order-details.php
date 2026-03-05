<?php
/**
 * Order details (post–Section 3 content: downloads, shipments, cancel/return, return list).
 * Main items + summary are in view-order Section 3. This template is loaded via woocommerce_view_order.
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( ! $order ) {
	return;
}

$order_items        = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$downloads          = $order->get_downloadable_items();
$show_downloads     = $order->has_downloadable_item() && $order->is_download_permitted();

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}

$shipments = function_exists( 'aftership_get_shipment' ) ? aftership_get_shipment( $order_id ) : array();
$shipments = is_array( $shipments ) ? $shipments : array();
?>
<?php if ( false ) : // Temporarily hide shipment status (below items + summary). ?>
<!-- Shipment status (main items + summary are in view-order Section 3) -->
<?php if ( ! empty( $shipments ) ) : ?>
	<h2 class="ma-order-details__shipments-title"><?php esc_html_e( 'Shipment status', 'woocommerce' ); ?></h2>
<?php endif; ?>

<?php
$shipment_index = 1;
if ( ! empty( $shipments ) ) {
	$shipment_line_item_ids = array_map( function ( $s ) {
		return array_column( $s['line_items'], 'id' );
	}, $shipments );

	foreach ( $shipments as $index => $shipment ) {
		$line_item_ids = $shipment_line_item_ids[ $index ];
		$item_in_shipment = array_filter( $order_items, function ( $item_id ) use ( $line_item_ids ) {
			return in_array( $item_id, $line_item_ids, true );
		}, ARRAY_FILTER_USE_KEY );

		foreach ( array_keys( $item_in_shipment ) as $item_id ) {
			unset( $order_items[ $item_id ] );
		}

		wc_get_template(
			'woocommerce/order/order-details-aftership-shipment-list-item.php',
			array(
				'itemInShipment'   => $item_in_shipment,
				'order'            => $order,
				'shipment'         => $shipment,
				'shipment_index'   => $shipment_index,
			)
		);
		$shipment_index++;
	}
}
?>
<?php endif; ?>

<?php if ( false ) : // Temporarily hide "Not yet shipped" block. ?>
<!-- Not yet shipped -->
<?php if ( count( $order_items ) > 0 ) : ?>
	<?php wc_get_template( 'myaccount/page-heading.php', array( 'page_heading' => __( 'Not yet shipped', 'woocommerce' ) ) ); ?>
	<?php
	wc_get_template(
		'woocommerce/order/order-details-list-item.php',
		array(
			'order_items' => $order_items,
			'order'       => $order,
		)
	);
	?>
<?php endif; ?>
<?php endif; ?>

<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

<div class="md:container mx-auto px-8">
	<?php if ( in_array( $order->get_status(), apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed', 'processing' ), $order ), true ) ) : ?>
		<div class="mt-14">
			<?php $request_order_cancellation = $order->get_meta( 'request_order_cancellation' ); ?>
			<form action="" method="post">
				<?php wp_nonce_field( 'cancel_order_action_nonce', 'cancel_order_nonce' ); ?>
				<input type="hidden" value="<?php echo esc_attr( $order->get_id() ); ?>" name="order_id" />
				<div class="flex flex-col sm:flex-row gap-5 items-center">
					<button class="px-6 text-sm button slim w-[300px]" type="submit" <?php echo $request_order_cancellation ? 'disabled' : ''; ?>><?php esc_html_e( 'Cancel Order', 'woocommerce' ); ?></button>
					<?php if ( $request_order_cancellation ) : ?>
						<span><?php esc_html_e( 'Your cancelation request has been submitted.', 'woocommerce' ); ?></span>
					<?php endif; ?>
				</div>
			</form>
		</div>
	<?php endif; ?>

	<?php if ( false && $order->get_status() === 'shipped' ) : // Temporarily hide Return Order button. ?>
		<div class="mt-14 text-center md:text-left">
			<a href="<?php echo esc_url( wc_get_endpoint_url( 'return-order', $order->get_id() ) ); ?>" class="px-6 text-sm button slim w-[200px] md:w-[300px]"><?php esc_html_e( 'Return Order', 'woocommerce' ); ?></a>
		</div>
	<?php endif; ?>
</div>

<?php if ( false ) : // Temporarily hide Return list. ?>
<!-- Return list -->
<?php
wc_get_template(
	'woocommerce/order/order-details-return-list.php',
	array(
		'order'       => $order,
		'order_items' => $order->get_items(),
	)
);
?>
<?php endif; ?>

<?php do_action( 'woocommerce_after_order_details', $order ); ?>
