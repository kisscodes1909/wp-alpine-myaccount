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

$ma_tracking_entries = array();
if ( class_exists( 'MyAccount_Core_Tracking_Resolver' ) ) {
	$ma_tracking_entries = MyAccount_Core_Tracking_Resolver::instance()->get_entries( $order );
}
?>

<div class="ma-orders__item-header">
	<p class="ma-orders__item-order-number">Order #<?php echo esc_html( $order->get_order_number() ); ?></p>
	<span class="ma-orders__item-status ma-orders__item-status--<?php echo esc_attr( $status_mod ); ?>">
		<span class="ma-orders__item-status-dot" aria-hidden="true"></span>
		<span><?php echo esc_html( $order_status_name ); ?></span>
	</span>
</div>

<div class="ma-orders__item-meta<?php echo ! empty( $ma_tracking_entries ) ? ' ma-orders__item-meta--has-tracking' : ''; ?>">
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
	<?php if ( ! empty( $ma_tracking_entries ) ) : ?>
		<div class="ma-orders__item-meta-group ma-orders__item-meta-group--tracking">
			<p class="ma-orders__item-meta-label"><?php esc_html_e( 'Tracking', 'myaccount-core' ); ?></p>
			<div class="ma-orders__item-meta-value ma-orders__item-meta-value--tracking">
				<?php
				foreach ( $ma_tracking_entries as $ma_tracking_entry ) {
					$ma_tracking_label = '' !== $ma_tracking_entry->tracking_number
						? $ma_tracking_entry->tracking_number
						: __( 'Track', 'myaccount-core' );
					$ma_link_title     = '' === $ma_tracking_entry->carrier_name
						? sprintf( ' title="%s"', esc_attr__( 'Open carrier tracking page', 'myaccount-core' ) )
						: '';
					echo '<div class="ma-orders__item-tracking-row">';
					echo '<span class="ma-orders__item-tracking-icon" aria-hidden="true">';
					echo '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" focusable="false" aria-hidden="true">';
					echo '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.086H19.5m-9 0V8.25m0 0h4.125c.621 0 1.129.504 1.09 1.124a17.902 17.902 0 013.213 9.193c0 .538-.214 1.05-.595 1.426L18 18.75M9 8.25h.008v.008H9V8.25zm3 0h.008v.008H12V8.25zm3 0h.008v.008H15V8.25z" />';
					echo '</svg></span><span class="ma-orders__item-tracking-main">';
					if ( '' !== $ma_tracking_entry->carrier_name ) {
						echo '<span class="ma-orders__item-tracking-provider">' . esc_html( $ma_tracking_entry->carrier_name ) . '</span>';
						echo '<span class="ma-orders__item-tracking-sep" aria-hidden="true"> · </span>';
					}
					printf(
						'<a href="%1$s" class="ma-orders__item-tracking-id" target="_blank" rel="noopener noreferrer"%3$s>%2$s</a>',
						esc_url( $ma_tracking_entry->tracking_url ),
						esc_html( $ma_tracking_label ),
						$ma_link_title
					);
					echo '</span></div>';
				}
				?>
			</div>
		</div>
	<?php endif; ?>
</div>

<div class="ma-orders__item-footer">
	<p class="ma-orders__item-total"><?php echo wp_kses_post( $order_total ); ?></p>
	<div class="ma-orders__item-actions">
		<?php if ( $reorder_url ) : ?>
			<a href="<?php echo esc_url( $reorder_url ); ?>" class="ma-btn ma-btn--secondary-light ma-orders__item-action-button ma-orders__item-reorder-button">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" focusable="false">
					<path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
				</svg>
				<?php esc_html_e( 'Order again', 'woocommerce' ); ?>
			</a>
		<?php endif; ?>
		<?php if ( $view_url ) : ?>
			<a href="<?php echo esc_url( $view_url ); ?>" class="ma-btn ma-btn--secondary-light ma-orders__item-action-button ma-orders__item-view-button" aria-label="<?php esc_attr_e( 'View order details', 'woocommerce' ); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" focusable="false">
					<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12S5.25 5.25 12 5.25 21.75 12 21.75 12 18.75 18.75 12 18.75 2.25 12 2.25 12z" />
					<path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
				</svg>
				<span><?php esc_html_e( 'View Details', 'woocommerce' ); ?></span>
			</a>
		<?php endif; ?>
	</div>
</div>
