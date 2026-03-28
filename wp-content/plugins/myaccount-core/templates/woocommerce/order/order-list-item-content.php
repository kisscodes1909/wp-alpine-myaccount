<?php
/**
 * Order list item content (header, meta, footer) for My Account orders list.
 * Used only in the orders list; not on view-order page.
 *
 * Expects: $order (WC_Order)
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;

$order_status      = $order->get_status();
$order_status_name = wc_get_order_status_name( $order_status );
// Map WooCommerce status to semantic type (reuses --ma-success/error/warning/info-* tokens).
$status_type_map   = array(
	'completed'  => 'success',
	'processing' => 'info',
	'pending'    => 'info',
	'on-hold'    => 'warning',
	'refunded'   => 'warning',
	'cancelled'  => 'error',
	'failed'     => 'error',
);
$status_mod        = isset( $status_type_map[ $order_status ] ) ? $status_type_map[ $order_status ] : 'info';
$actions           = wc_get_account_orders_actions( $order );
$view_url          = isset( $actions['view']['url'] ) ? $actions['view']['url'] : '';
$pay_action        = isset( $actions['pay'] ) && is_array( $actions['pay'] ) ? $actions['pay'] : null;
$cancel_action     = isset( $actions['cancel'] ) && is_array( $actions['cancel'] ) ? $actions['cancel'] : null;
$reorder_url       = '';
$valid_statuses    = apply_filters( 'woocommerce_valid_order_statuses_for_order_again', array( 'completed' ) );

if ( is_user_logged_in() && $order->has_status( $valid_statuses ) ) {
	$reorder_url = wp_nonce_url(
		add_query_arg( 'order_again', $order->get_id(), wc_get_cart_url() ),
		'woocommerce-order_again'
	);
}

$totals            = $order->get_order_item_totals();
$order_total       = isset( $totals['order_total'] ) ? $totals['order_total']['value'] : $order->get_formatted_order_total();
$item_count        = $order->get_item_count();
$ma_tracking_view_url = $view_url ? $view_url . '#ma-view-order-tracking-' . (int) $order->get_id() : '';

$ma_tracking_entries = array();
$ma_tracking_enabled = class_exists( 'MyAccount_Core_Tracking_Module' ) && MyAccount_Core_Tracking_Module::is_enabled();

if ( $ma_tracking_enabled ) {
	$ma_tracking_entries = MyAccount_Core_Tracking_Module::instance()->get_entries( $order );
}

$ma_has_tracking        = ! empty( $ma_tracking_entries );
$ma_all_tracking_done   = $ma_has_tracking;

if ( $ma_has_tracking ) {
	foreach ( $ma_tracking_entries as $ma_tracking_entry ) {
		if ( ! $ma_tracking_entry->is_delivered ) {
			$ma_all_tracking_done = false;
			break;
		}
	}
}
?>

<div class="ma-orders__item-header">
	<p class="ma-orders__item-order-number">Order #<?php echo esc_html( $order->get_order_number() ); ?></p>
	<span class="ma-orders__item-status ma-orders__item-status--<?php echo esc_attr( $status_mod ); ?> ma-orders__item-status--state-<?php echo esc_attr( sanitize_html_class( $order_status ) ); ?>">
		<span><?php echo esc_html( $order_status_name ); ?></span>
	</span>
</div>



<div class="ma-orders__item-meta ma-orders__item-meta--mobile">
	<div class="ma-orders__item-meta-row ma-orders__item-meta-row--date">
		<span class="ma-orders__item-meta-label"><?php esc_html_e( 'Date', 'woocommerce' ); ?></span>
		<span class="ma-orders__item-meta-value">
			<time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>
		</span>
	</div>
	<div class="ma-orders__item-meta-row ma-orders__item-meta-row--items">
		<span class="ma-orders__item-meta-label"><?php esc_html_e( 'Item', 'woocommerce' ); ?></span>
		<span class="ma-orders__item-meta-value">
			<?php
			/* translators: %d: item count */
			echo esc_html( sprintf( _n( '%d item', '%d items', $item_count, 'woocommerce' ), $item_count ) );
			?>
		</span>
	</div>
	<div class="ma-orders__item-meta-row ma-orders__item-meta-row--total">
		<span class="ma-orders__item-meta-label"><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
		<span class="ma-orders__item-meta-value"><?php echo wp_kses_post( $order_total ); ?></span>
	</div>
</div>

<div class="ma-orders__item-meta ma-orders__item-meta--desktop">
	<div class="ma-orders__item-meta-group">
		<p class="ma-orders__item-meta-label"><?php esc_html_e( 'Date', 'woocommerce' ); ?></p>
		<p class="ma-orders__item-meta-value">
			<time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>
		</p>
	</div>
	<div class="ma-orders__item-meta-group ma-orders__item-meta-group--items">
		<p class="ma-orders__item-meta-label"><?php esc_html_e( 'Items', 'woocommerce' ); ?></p>
		<p class="ma-orders__item-meta-value">
			<?php
			/* translators: %d: item count */
			echo esc_html( sprintf( _n( '%d item', '%d items', $item_count, 'woocommerce' ), $item_count ) );
			?>
		</p>
	</div>
	<div class="ma-orders__item-meta-group ma-orders__item-meta-group--total">
		<p class="ma-orders__item-meta-label"><?php esc_html_e( 'Total', 'woocommerce' ); ?></p>
		<p class="ma-orders__item-meta-value"><?php echo wp_kses_post( $order_total ); ?></p>
	</div>
</div>

<div class="ma-orders__item-footer<?php echo ! $ma_has_tracking ? ' ma-orders__item-footer--no-tracking' : ''; ?>">
	<?php if ( $ma_has_tracking ) : ?>
		<<?php echo $ma_tracking_view_url ? 'a' : 'div'; ?>
			<?php if ( $ma_tracking_view_url ) : ?>
				href="<?php echo esc_url( $ma_tracking_view_url ); ?>"
			<?php endif; ?>
			class="ma-orders__item-fulfillment ma-orders__item-fulfillment--<?php echo $ma_all_tracking_done ? 'delivered' : 'transit'; ?>"
		>
			<span class="ma-orders__item-fulfillment-icon" aria-hidden="true">
				<?php if ( $ma_all_tracking_done ) : ?>
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" focusable="false" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.086H19.5m-9 0V8.25m0 0h4.125c.621 0 1.129.504 1.09 1.124a17.902 17.902 0 013.213 9.193c0 .538-.214 1.05-.595 1.426L18 18.75M9 8.25h.008v.008H9V8.25zm3 0h.008v.008H12V8.25zm3 0h.008v.008H15V8.25z" />
					</svg>
				<?php else : ?>
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" focusable="false" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
						<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.086H19.5m-9 0V8.25m0 0h4.125c.621 0 1.129.504 1.09 1.124a17.902 17.902 0 013.213 9.193c0 .538-.214 1.05-.595 1.426L18 18.75M9 8.25h.008v.008H9V8.25zm3 0h.008v.008H12V8.25zm3 0h.008v.008H15V8.25z" />
					</svg>
				<?php endif; ?>
			</span>
			<span class="ma-orders__item-fulfillment-text">
				<?php echo esc_html( $ma_all_tracking_done ? __( 'Delivered successfully', 'myaccount-core' ) : __( 'In transit', 'myaccount-core' ) ); ?>
			</span>
		</<?php echo $ma_tracking_view_url ? 'a' : 'div'; ?>>
	<?php endif; ?>
	<div class="ma-orders__item-actions">
		<?php // TODO: Exchange/Refund button (UI only; feature not implemented yet). ?>
		<?php // TODO: Review button (UI only; feature not implemented yet). ?>
		<?php // TODO: View review button (UI only; feature not implemented yet). ?>
		<?php if ( $pay_action && ! empty( $pay_action['url'] ) ) : ?>
			<a href="<?php echo esc_url( $pay_action['url'] ); ?>" class="ma-btn ma-btn--primary ma-orders__item-action-button ma-orders__item-pay-button">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" focusable="false">
					<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15A2.25 2.25 0 002.25 6.75v10.5A2.25 2.25 0 004.5 19.5z" />
				</svg>
				<?php echo esc_html( $pay_action['name'] ); ?>
			</a>
		<?php endif; ?>
		<?php if ( $cancel_action && ! empty( $cancel_action['url'] ) ) : ?>
			<a href="<?php echo esc_url( $cancel_action['url'] ); ?>" class="ma-btn ma-btn--secondary-light ma-orders__item-action-button ma-orders__item-cancel-button">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" focusable="false">
					<path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
				</svg>
				<?php echo esc_html( $cancel_action['name'] ); ?>
			</a>
		<?php endif; ?>
		<?php if ( in_array( $order_status, array( 'on-hold', 'refunded' ), true ) && $view_url ) : ?>
			<a href="<?php echo esc_url( $view_url ); ?>" class="ma-btn ma-btn--secondary-light ma-orders__item-action-button ma-orders__item-help-button">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" focusable="false">
					<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a2.25 2.25 0 113.168-2.63c0 1.125-.69 1.718-1.396 2.19-.54.362-1.063.733-1.063 1.46v.375" />
					<path stroke-linecap="round" stroke-linejoin="round" d="M12 17.25h.008v.008H12v-.008z" />
					<path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
				</svg>
				<?php esc_html_e( 'Help', 'myaccount-core' ); ?>
			</a>
		<?php endif; ?>
		<?php if ( 'processing' === $order_status && $ma_tracking_view_url ) : ?>
			<a href="<?php echo esc_url( $ma_tracking_view_url ); ?>" class="ma-btn ma-btn--secondary-light ma-orders__item-action-button ma-orders__item-track-button">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" focusable="false">
					<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
					<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.086H19.5m-9 0V8.25m0 0h4.125c.621 0 1.129.504 1.09 1.124a17.902 17.902 0 013.213 9.193c0 .538-.214 1.05-.595 1.426L18 18.75M9 8.25h.008v.008H9V8.25zm3 0h.008v.008H12V8.25zm3 0h.008v.008H15V8.25z" />
				</svg>
				<?php esc_html_e( 'Track delivery', 'myaccount-core' ); ?>
			</a>
		<?php endif; ?>
		<?php if ( $reorder_url ) : ?>
			<a href="<?php echo esc_url( $reorder_url ); ?>" class="ma-btn ma-btn--primary ma-orders__item-action-button ma-orders__item-reorder-button">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" focusable="false">
					<path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
				</svg>
				<?php esc_html_e( 'Order again', 'woocommerce' ); ?>
			</a>
		<?php endif; ?>
		<?php // View details button removed; order item is clickable. ?>
	</div>
</div>
