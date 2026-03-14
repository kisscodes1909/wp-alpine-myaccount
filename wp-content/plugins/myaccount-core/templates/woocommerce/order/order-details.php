<?php
/**
 * Order details (post–Section 3: downloads, hooks, after_order_details).
 * Main items + summary are in view-order Section 3.
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( ! $order ) {
	return;
}

$order_items    = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$downloads      = $order->get_downloadable_items();
$show_downloads = apply_filters(
	'woocommerce_order_downloads_table_show_downloads',
	( $order->has_downloadable_item() && $order->is_download_permitted() ),
	$order
);
// My Account view-order: no downloads block in this layout (items + summary only).
if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'view-order' ) ) {
	$show_downloads = false;
}

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

<?php
// Fires after_order_table for third-party hooks; Order again is output in items-summary (see view-order).
do_action( 'woocommerce_order_details_after_order_table', $order );
?>

<?php if ( false ) : // Return list hidden. ?>
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
