<?php
/**
 * Order status card (Section 2): icon, title, description, est. delivery, 3-step timeline (core Woo: no shipped).
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;

$status      = $order->get_status();
$status_name = wc_get_order_status_name( $status );

// Timeline: 1 = Placed, 2 = Processing, 3 = Complete (maps to Woo completed/refunded).
if ( in_array( $status, array( 'cancelled', 'failed' ), true ) ) {
	$current_step = 1;
} elseif ( in_array( $status, array( 'pending', 'on-hold' ), true ) ) {
	$current_step = 1;
} elseif ( 'processing' === $status ) {
	$current_step = 2;
} elseif ( 'completed' === $status || 'refunded' === $status ) {
	$current_step = 3;
} else {
	$current_step = (int) apply_filters( 'myaccount_core_order_status_card_timeline_step', 2, $status, $order );
	$current_step = min( 3, max( 1, $current_step ) );
}

$est_delivery = $order->get_meta( '_estimated_delivery' );
$est_delivery = $est_delivery ? sanitize_text_field( $est_delivery ) : '';

$status_descriptions = array(
	'pending'    => __( 'Your order has been received and is awaiting payment.', 'woocommerce' ),
	'on-hold'    => __( 'Your order is on hold until we confirm payment.', 'woocommerce' ),
	'processing' => __( 'Your order has been confirmed and is being prepared for processing.', 'woocommerce' ),
	'completed'  => __( 'Your order has been delivered.', 'woocommerce' ),
	'refunded'   => __( 'Your order has been refunded.', 'woocommerce' ),
	'cancelled'  => __( 'This order has been cancelled.', 'woocommerce' ),
	'failed'     => __( 'Payment for this order failed.', 'woocommerce' ),
);
$status_description = isset( $status_descriptions[ $status ] ) ? $status_descriptions[ $status ] : sprintf( __( 'Order status: %s', 'woocommerce' ), $status_name );
$status_description = apply_filters( 'woocommerce_myaccount_order_status_description', $status_description, $order );

// Progress: 2 segments between 3 dots → step 1 = 0%, 2 = 50%, 3 = 100%.
$progress_pct = 3 === $current_step ? 100 : ( $current_step - 1 ) * 50;
?>
<section class="ma-order-status-card" aria-labelledby="ma-order-status-card-heading">
	<div class="ma-order-status-card__header">
		<div class="ma-order-status-card__header-left">
			<div class="ma-order-status-card__icon-wrap">
				<svg class="ma-order-status-card__icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
				</svg>
			</div>
			<div class="ma-order-status-card__header-text">
				<h2 id="ma-order-status-card-heading" class="ma-order-status-card__title"><?php esc_html_e( 'Order Placed', 'woocommerce' ); ?></h2>
				<p class="ma-order-status-card__description"><?php echo esc_html( $status_description ); ?></p>
			</div>
		</div>
		<?php if ( $est_delivery && $current_step < 3 && ! in_array( $status, array( 'cancelled', 'failed' ), true ) ) : ?>
			<div class="ma-order-status-card__est-delivery">
				<p class="ma-order-status-card__est-label"><?php esc_html_e( 'Est. Delivery', 'woocommerce' ); ?></p>
				<p class="ma-order-status-card__est-date"><?php echo esc_html( $est_delivery ); ?></p>
			</div>
		<?php endif; ?>
	</div>

	<div class="ma-order-status-card__timeline">
		<div class="ma-order-status-card__timeline-track" style="--ma-timeline-progress: <?php echo (float) $progress_pct; ?>;">
			<div class="ma-order-status-card__timeline-line" aria-hidden="true"></div>
			<div class="ma-order-status-card__timeline-line-fill" aria-hidden="true"></div>
			<?php
			$fmt         = apply_filters( 'myaccount_core_order_status_card_date_format', wc_date_format(), $order );
			$date_placed = $order->get_date_created() ? $order->get_date_created()->date_i18n( $fmt ) : '';
			$date_paid   = $order->get_date_paid() ? $order->get_date_paid()->date_i18n( $fmt ) : '';
			$date_done   = $order->get_date_completed() ? $order->get_date_completed()->date_i18n( $fmt ) : '';

			$steps = array(
				array(
					'key'      => 'placed',
					'label'    => __( 'Placed', 'woocommerce' ),
					'sublabel' => __( 'Order received', 'woocommerce' ),
					'date'     => $date_placed,
				),
				array(
					'key'      => 'processing',
					'label'    => __( 'Processing', 'woocommerce' ),
					'sublabel' => __( 'Preparing', 'woocommerce' ),
					'date'     => ( $current_step >= 2 && $date_paid ) ? $date_paid : '',
				),
				array(
					'key'      => 'complete',
					'label'    => __( 'Complete', 'woocommerce' ),
					'sublabel' => __( 'Delivered', 'woocommerce' ),
					'date'     => ( $current_step >= 3 && $date_done ) ? $date_done : '',
				),
			);
			$step_index = 1;
			foreach ( $steps as $step ) :
				$is_active  = $current_step >= $step_index;
				$is_current = $current_step === $step_index;
				?>
				<div class="ma-order-status-card__step <?php echo $is_current ? 'ma-order-status-card__step--current' : ''; ?> <?php echo $is_active ? 'ma-order-status-card__step--active' : ''; ?>">
					<div class="ma-order-status-card__step-dot" aria-hidden="true"></div>
					<div class="ma-order-status-card__step-labels">
						<span class="ma-order-status-card__step-label"><?php echo esc_html( $step['label'] ); ?></span>
						<span class="ma-order-status-card__step-sublabel"><?php echo esc_html( $step['sublabel'] ); ?></span>
						<?php if ( $step['date'] !== '' ) : ?>
							<span class="ma-order-status-card__step-date"><?php echo esc_html( $step['date'] ); ?></span>
						<?php endif; ?>
					</div>
				</div>
				<?php
				$step_index++;
			endforeach;
			?>
		</div>
	</div>
</section>
