<?php
/**
 * Order details: row1 = Items | Order Summary; row2 = Shipping | Billing.
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;

$order_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$item_count  = array_sum( array_map( function ( $item ) { return $item->get_quantity(); }, $order_items ) );
$totals      = $order->get_order_item_totals();
$shipping    = $order->get_address( 'shipping' );
$billing     = $order->get_address( 'billing' );

$billing_phone          = trim( (string) $order->get_billing_phone( 'edit' ) ) ?: trim( (string) ( $billing['phone'] ?? '' ) );
$shipping_phone         = trim( (string) $order->get_shipping_phone( 'edit' ) ) ?: trim( (string) ( $shipping['phone'] ?? '' ) );
$shipping_contact_phone = $shipping_phone !== '' ? $shipping_phone : $billing_phone;

?>
<div class="ma-order-details-items-summary">
	<div class="ma-order-details-items-summary__grid">
		<div class="ma-order-details-items-summary__items">
			<h2 class="ma-order-details-items-summary__items-title ma-u-section-title ma-u-section-title--mb-md">
				<?php
				echo esc_html( sprintf( __( 'Items · %d', 'woocommerce' ), $item_count ) );
				?>
			</h2>
			<div class="ma-order-details-items-summary__items-list">
				<?php
				$show_purchase_note = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
				do_action( 'woocommerce_order_details_before_order_table_items', $order );
				foreach ( $order_items as $item_id => $item ) {
					$product = $item->get_product();
					wc_get_template(
						'order/order-details-item.php',
						array(
							'order'              => $order,
							'item_id'            => $item_id,
							'item'               => $item,
							'show_purchase_note' => $show_purchase_note,
							'purchase_note'      => $product ? $product->get_purchase_note() : '',
							'product'            => $product,
						)
					);
				}
				do_action( 'woocommerce_order_details_after_order_table_items', $order );
				?>
			</div>
		</div>

		<aside class="ma-order-details-items-summary__summary">
			<h2 class="ma-u-section-title ma-u-section-title--mb-md"><?php esc_html_e( 'Order Summary', 'woocommerce' ); ?></h2>
			<div class="ma-order-details-items-summary__summary-card">
				<div class="ma-order-details-items-summary__summary-inner">
					<?php if ( $totals ) : ?>
						<?php foreach ( $totals as $key => $total ) : ?>
							<?php if ( 'payment_method' === $key ) { continue; } ?>
							<div class="ma-order-details-items-summary__summary-row <?php echo ( 'order_total' === $key ) ? 'ma-order-details-items-summary__summary-row--total' : ''; ?>">
								<span class="ma-order-details-items-summary__summary-label"><?php echo esc_html( $total['label'] ); ?></span>
								<span class="ma-order-details-items-summary__summary-value"><?php echo wp_kses_post( $total['value'] ); ?></span>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>

				<?php if ( $order->get_customer_note() ) : ?>
				<div class="ma-order-details-items-summary__customer-note">
					<p class="ma-order-details-items-summary__customer-note-label"><?php esc_html_e( 'Note:', 'woocommerce' ); ?></p>
					<div class="ma-order-details-items-summary__customer-note-text"><?php echo wp_kses( nl2br( wptexturize( $order->get_customer_note() ) ), array( 'br' => array() ) ); ?></div>
				</div>
				<?php endif; ?>

				<?php
				$payment_title = $order->get_payment_method_title();
				$payment_last4 = $order->get_meta( 'payment_method_last4' );
				if ( '' === $payment_last4 && $order->get_payment_method() ) {
					$payment_last4 = apply_filters( 'woocommerce_myaccount_order_payment_last4', '', $order );
				}
				if ( $payment_title || $payment_last4 ) :
					?>
				<div class="ma-order-details-items-summary__payment">
					<p class="ma-order-details-items-summary__payment-label"><?php esc_html_e( 'Payment', 'woocommerce' ); ?></p>
					<div class="ma-order-details-items-summary__payment-inner">
						<div class="ma-order-details-items-summary__payment-icon" aria-hidden="true">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
								<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
							</svg>
						</div>
						<div class="ma-order-details-items-summary__payment-details">
							<?php if ( $payment_title ) : ?>
								<p class="ma-order-details-items-summary__payment-method"><?php echo esc_html( $payment_title ); ?></p>
							<?php endif; ?>
							<?php if ( $payment_last4 ) : ?>
								<p class="ma-order-details-items-summary__payment-masked"><?php echo esc_html( '•••• ' . $payment_last4 ); ?></p>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php endif; ?>

				<div class="ma-order-details-items-summary__actions">
					<?php
					$invoice_url = apply_filters( 'woocommerce_myaccount_order_invoice_url', '', $order );
					if ( $order->needs_payment() ) :
						$pay_url = $order->get_checkout_payment_url();
						?>
					<a href="<?php echo esc_url( $pay_url ); ?>" class="ma-btn ma-btn--primary ma-btn--block">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
						</svg>
						<span><?php esc_html_e( 'Pay', 'woocommerce' ); ?></span>
					</a>
					<?php endif; ?>
					<?php
					$cancel_statuses = apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $order );
					$can_cancel      = $order->has_status( $cancel_statuses ) && current_user_can( 'cancel_order', $order->get_id() );
					if ( $can_cancel ) :
						$cancel_url = $order->get_cancel_order_url( wc_get_page_permalink( 'myaccount' ) );
						?>
					<a href="<?php echo esc_url( $cancel_url ); ?>" class="ma-btn ma-btn--danger ma-btn--block">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
						</svg>
						<span><?php esc_html_e( 'Cancel Order', 'woocommerce' ); ?></span>
					</a>
					<?php endif; ?>
					<?php if ( $invoice_url ) : ?>
					<a href="<?php echo esc_url( $invoice_url ); ?>" class="ma-btn ma-btn--secondary ma-btn--block" aria-label="<?php esc_attr_e( 'Download invoice', 'woocommerce' ); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
						</svg>
						<span><?php esc_html_e( 'Download Invoice', 'woocommerce' ); ?></span>
					</a>
					<?php endif; ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="ma-btn ma-btn--secondary ma-btn--block" aria-label="<?php esc_attr_e( 'Need help', 'woocommerce' ); ?>">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
						</svg>
						<span><?php esc_html_e( 'Need Help?', 'woocommerce' ); ?></span>
					</a>
					<?php
					if ( function_exists( 'woocommerce_order_again_button' ) ) {
						woocommerce_order_again_button( $order );
					}
					?>
				</div>
			</div>
		</aside>

		<div class="ma-order-details-items-summary__addresses">
			<div class="ma-order-details-items-summary__shipping">
				<h2 class="ma-order-details-items-summary__shipping-title ma-u-section-title ma-u-section-title--mb-md"><?php esc_html_e( 'Shipping Address', 'woocommerce' ); ?></h2>
				<div class="ma-order-details-items-summary__shipping-inner">
					<div class="ma-order-details-items-summary__shipping-deliver">
						<p class="ma-order-details-items-summary__shipping-label"><?php esc_html_e( 'Deliver To', 'woocommerce' ); ?></p>
						<div class="ma-order-details-items-summary__shipping-address-block">
							<div class="ma-order-details-items-summary__shipping-icon" aria-hidden="true">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
									<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
									<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
								</svg>
							</div>
							<div class="ma-order-details-items-summary__shipping-content">
								<p class="ma-order-details-items-summary__shipping-name"><?php echo esc_html( trim( $shipping['first_name'] . ' ' . $shipping['last_name'] ) ); ?></p>
								<p class="ma-order-details-items-summary__shipping-address">
									<?php echo esc_html( $shipping['address_1'] ); ?><br>
									<?php echo esc_html( implode( ', ', array_filter( array( $shipping['city'], $shipping['state'], $shipping['postcode'] ) ) ) ); ?>
								</p>
							</div>
						</div>
					</div>
					<div class="ma-order-details-items-summary__shipping-contact">
						<p class="ma-order-details-items-summary__shipping-label"><?php esc_html_e( 'Contact', 'woocommerce' ); ?></p>
						<div class="ma-order-details-items-summary__shipping-contact-list">
							<?php if ( $shipping_contact_phone ) : ?>
							<span class="ma-order-details-items-summary__contact-line">
								<span class="ma-order-details-items-summary__contact-icon-wrap" aria-hidden="true">
									<svg class="ma-order-details-items-summary__contact-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.372 3.13A2.25 2.25 0 005.25 2.278V4.5c0 .897.352 1.756.978 2.397M2.25 6.75l3 3" />
									</svg>
								</span>
								<?php echo esc_html( $shipping_contact_phone ); ?>
							</span>
							<?php endif; ?>
							<?php if ( $billing['email'] ) : ?>
							<span class="ma-order-details-items-summary__contact-line">
								<span class="ma-order-details-items-summary__contact-icon-wrap" aria-hidden="true">
									<svg class="ma-order-details-items-summary__contact-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
									</svg>
								</span>
								<?php echo esc_html( $billing['email'] ); ?>
							</span>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

			<div class="ma-order-details-items-summary__billing">
				<h2 class="ma-order-details-items-summary__billing-title ma-u-section-title ma-u-section-title--mb-md"><?php esc_html_e( 'Billing Address', 'woocommerce' ); ?></h2>
				<div class="ma-order-details-items-summary__billing-inner">
					<div class="ma-order-details-items-summary__billing-billto">
						<p class="ma-order-details-items-summary__billing-label"><?php esc_html_e( 'Bill To', 'myaccount-core' ); ?></p>
						<div class="ma-order-details-items-summary__billing-address-block">
							<div class="ma-order-details-items-summary__billing-icon" aria-hidden="true">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
									<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5M4.5 3h15A1.5 1.5 0 0121 4.5v15a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 19.5v-15A1.5 1.5 0 014.5 3z" />
									<path stroke-linecap="round" stroke-linejoin="round" d="M8 7.5h8M8 12h4" />
								</svg>
							</div>
							<div class="ma-order-details-items-summary__billing-content">
								<p class="ma-order-details-items-summary__billing-name"><?php echo esc_html( trim( $billing['first_name'] . ' ' . $billing['last_name'] ) ); ?></p>
								<p class="ma-order-details-items-summary__billing-address">
									<?php echo esc_html( $billing['address_1'] ); ?><?php echo $billing['address_2'] ? '<br>' . esc_html( $billing['address_2'] ) : ''; ?><br>
									<?php echo esc_html( implode( ', ', array_filter( array( $billing['city'], $billing['state'], $billing['postcode'] ) ) ) ); ?>
									<?php if ( ! empty( $billing['country'] ) ) : ?>
										<br><?php echo esc_html( WC()->countries->countries[ $billing['country'] ] ?? $billing['country'] ); ?>
									<?php endif; ?>
								</p>
							</div>
						</div>
					</div>
					<div class="ma-order-details-items-summary__billing-contact">
						<p class="ma-order-details-items-summary__billing-label"><?php esc_html_e( 'Contact', 'woocommerce' ); ?></p>
						<div class="ma-order-details-items-summary__billing-contact-list">
							<?php if ( $billing_phone ) : ?>
							<span class="ma-order-details-items-summary__contact-line">
								<span class="ma-order-details-items-summary__contact-icon-wrap" aria-hidden="true">
									<svg class="ma-order-details-items-summary__contact-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.372 3.13A2.25 2.25 0 005.25 2.278V4.5c0 .897.352 1.756.978 2.397M2.25 6.75l3 3" />
									</svg>
								</span>
								<?php echo esc_html( $billing_phone ); ?>
							</span>
							<?php endif; ?>
							<?php if ( ! empty( $billing['email'] ) ) : ?>
							<span class="ma-order-details-items-summary__contact-line">
								<span class="ma-order-details-items-summary__contact-icon-wrap" aria-hidden="true">
									<svg class="ma-order-details-items-summary__contact-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
										<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
									</svg>
								</span>
								<?php echo esc_html( $billing['email'] ); ?>
							</span>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
