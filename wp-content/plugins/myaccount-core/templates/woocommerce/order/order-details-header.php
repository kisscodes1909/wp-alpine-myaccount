<?php
/**
 * Order details header (Section 1): order number, date, total.
 * Primary actions live in order-details-items-summary (deduped).
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="ma-order-details-header">
	<div class="ma-order-details-header__inner">
		<div class="ma-order-details-header__left">
			<p class="ma-order-details-header__label"><?php esc_html_e( 'Order Details', 'woocommerce' ); ?></p>
			<p class="ma-order-details-header__number">#<?php echo esc_html( $order->get_order_number() ); ?></p>
			<p class="ma-order-details-header__date">
				<time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>">
					<?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?>
				</time>
			</p>
		</div>
		<div class="ma-order-details-header__right">
			<p class="ma-order-details-header__total-label"><?php esc_html_e( 'Order Total', 'woocommerce' ); ?></p>
			<p class="ma-order-details-header__total-value"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></p>
		</div>
	</div>
</div>
