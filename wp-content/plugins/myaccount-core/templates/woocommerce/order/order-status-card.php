<?php
/**
 * Order status card (Section 2): icon, status pill, description, est. delivery, 4-step timeline.
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;

$status   = $order->get_status();
$status_name = wc_get_order_status_name( $status );

// Map WooCommerce status to timeline step (1=Placed, 2=Processing, 3=Shipped, 4=Delivered).
$step_placed    = 1;
$step_processing = 2;
$step_shipped   = 3;
$step_delivered = 4;
if ( in_array( $status, array( 'pending', 'on-hold' ), true ) ) {
	$current_step = 1;
} elseif ( 'processing' === $status ) {
	$current_step = 2;
} elseif ( 'completed' === $status || 'refunded' === $status ) {
	$current_step = 4;
} else {
	// Shipped or custom statuses (e.g. shipped).
	$current_step = 3;
}

$est_delivery = $order->get_meta( '_estimated_delivery' );
if ( ! $est_delivery && $order->get_date_created() ) {
	// Placeholder: e.g. +5 days from order date.
	$est_date = $order->get_date_created()->getTimestamp() + ( 5 * DAY_IN_SECONDS );
	$est_delivery = date_i18n( 'M j, Y', $est_date );
}

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

// Progress percentage for timeline line fill: 3 segments between 4 dots (thirds): step 1 = 0%, 2 ≈ 33.33%, 3 ≈ 66.67%, 4 = 100%.
$progress_pct = 4 === $current_step ? 100 : ( $current_step - 1 ) * ( 100 / 3 );
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
				<div class="ma-order-status-card__title-row">
					<h2 id="ma-order-status-card-heading" class="ma-order-status-card__title"><?php esc_html_e( 'Order Placed', 'woocommerce' ); ?></h2>
					<span class="ma-order-status-card__pill"><?php echo esc_html( $status_name ); ?></span>
				</div>
				<?php /* Description: state description text, not the status label (status is in the pill above). */ ?>
				<p class="ma-order-status-card__description"><?php echo esc_html( $status_description ); ?></p>
			</div>
		</div>
		<?php if ( $est_delivery && $current_step < 4 ) : ?>
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
			$order_date = $order->get_date_created() ? wc_format_datetime( $order->get_date_created() ) : '—';
			$steps = array(
				array( 'key' => 'placed',    'label' => __( 'Placed', 'woocommerce' ),    'sublabel' => __( 'Confirmed', 'woocommerce' ),    'date' => $order_date ),
				array( 'key' => 'processing', 'label' => __( 'Processing', 'woocommerce' ), 'sublabel' => __( 'Preparing', 'woocommerce' ), 'date' => '—' ),
				array( 'key' => 'shipped',   'label' => __( 'Shipped', 'woocommerce' ),   'sublabel' => __( 'In Transit', 'woocommerce' ),   'date' => '—' ),
				array( 'key' => 'delivered', 'label' => __( 'Delivered', 'woocommerce' ), 'sublabel' => __( 'Complete', 'woocommerce' ),   'date' => '—' ),
			);
			$step_index = 1;
			foreach ( $steps as $step ) :
				$is_active = $current_step >= $step_index;
				$is_current = $current_step === $step_index;
				?>
				<div class="ma-order-status-card__step <?php echo $is_current ? 'ma-order-status-card__step--current' : ''; ?> <?php echo $is_active ? 'ma-order-status-card__step--active' : ''; ?>">
					<div class="ma-order-status-card__step-dot" aria-hidden="true"></div>
					<div class="ma-order-status-card__step-labels">
						<span class="ma-order-status-card__step-label"><?php echo esc_html( $step['label'] ); ?></span>
						<span class="ma-order-status-card__step-sublabel"><?php echo esc_html( $step['sublabel'] ); ?></span>
						<span class="ma-order-status-card__step-date"><?php echo esc_html( $step['date'] ); ?></span>
					</div>
				</div>
				<?php
				$step_index++;
			endforeach;
			?>
		</div>
	</div>
</section>
