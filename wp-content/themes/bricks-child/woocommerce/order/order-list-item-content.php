<?php
/**
 * Order list item content (header, meta, footer) for My Account orders list.
 * Used only in the orders list; not on view-order page.
 *
 * Expects: $order (WC_Order)
 */

defined( 'ABSPATH' ) || exit;

$order_status      = $order->get_status();
$order_status_name = wc_get_order_status_name( $order_status );
$actions           = wc_get_account_orders_actions( $order );
$view_url          = isset( $actions['view']['url'] ) ? $actions['view']['url'] : '';
$totals            = $order->get_order_item_totals();
$order_total       = isset( $totals['order_total'] ) ? $totals['order_total']['value'] : $order->get_formatted_order_total();
$item_count        = $order->get_item_count();
?>

<div class="ma-orders__item-header">
	<p class="ma-orders__item-order-number">Order #<?php echo esc_html( $order->get_order_number() ); ?></p>
	<span class="ma-orders__item-status">
		<span class="ma-orders__item-status-dot" aria-hidden="true"></span>
		<span><?php echo esc_html( $order_status_name ); ?></span>
	</span>
</div>

<div class="ma-orders__item-meta">
	<div class="ma-orders__item-meta-group">
		<p class="ma-orders__item-meta-label"><?php esc_html_e( 'Date', 'woocommerce' ); ?></p>
		<p class="ma-orders__item-meta-value">
			<time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>
		</p>
	</div>
	<div class="ma-orders__item-meta-group">
		<p class="ma-orders__item-meta-label"><?php esc_html_e( 'Items', 'woocommerce' ); ?></p>
		<p class="ma-orders__item-meta-value">
			<?php
			/* translators: %d: item count */
			echo esc_html( sprintf( _n( '%d item', '%d items', $item_count, 'woocommerce' ), $item_count ) );
			?>
		</p>
	</div>
</div>

<div class="ma-orders__item-footer">
	<p class="ma-orders__item-total"><?php echo wp_kses_post( $order_total ); ?></p>
	<?php if ( $view_url ) : ?>
		<a href="<?php echo esc_url( $view_url ); ?>" class="ma-orders__item-view-link" aria-label="<?php esc_attr_e( 'View order details', 'woocommerce' ); ?>">
			<span><?php esc_html_e( 'View Details', 'woocommerce' ); ?></span>
			<svg class="ma-orders__item-view-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" focusable="false">
				<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
			</svg>
		</a>
	<?php endif; ?>
</div>
