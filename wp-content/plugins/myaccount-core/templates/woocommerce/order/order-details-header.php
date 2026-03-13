<?php
/**
 * Order details header (Section 1): order number, date, total, action buttons.
 * Used on view-order page below page heading.
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;

$can_cancel = in_array( $order->get_status(), apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed', 'processing' ), $order ), true );
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
			<div class="ma-order-details-header__actions">
				<?php
				// Invoice: link to invoice if available (e.g. PDF plugin), else placeholder.
				$invoice_url = apply_filters( 'woocommerce_myaccount_order_invoice_url', '', $order );
				if ( $invoice_url ) :
					?>
					<a href="<?php echo esc_url( $invoice_url ); ?>" class="ma-btn ma-btn--secondary ma-order-details-header__btn" aria-label="<?php esc_attr_e( 'Download invoice', 'woocommerce' ); ?>">
						<svg class="ma-order-details-header__icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
						</svg>
						<span><?php esc_html_e( 'Invoice', 'woocommerce' ); ?></span>
					</a>
				<?php endif; ?>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="ma-btn ma-btn--secondary ma-order-details-header__btn" aria-label="<?php esc_attr_e( 'Help', 'woocommerce' ); ?>">
					<svg class="ma-order-details-header__icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
					</svg>
					<span><?php esc_html_e( 'Help', 'woocommerce' ); ?></span>
				</a>
				<?php if ( $can_cancel ) : ?>
					<?php $request_cancellation = $order->get_meta( 'request_order_cancellation' ); ?>
					<form action="" method="post" class="ma-order-details-header__cancel-form">
						<?php wp_nonce_field( 'cancel_order_action_nonce', 'cancel_order_nonce' ); ?>
						<input type="hidden" name="order_id" value="<?php echo esc_attr( $order->get_id() ); ?>" />
						<button type="submit" class="ma-btn ma-btn--danger ma-order-details-header__btn" <?php echo $request_cancellation ? 'disabled' : ''; ?>>
							<svg class="ma-order-details-header__icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
								<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
							</svg>
							<span><?php esc_html_e( 'Cancel Order', 'woocommerce' ); ?></span>
						</button>
					</form>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
