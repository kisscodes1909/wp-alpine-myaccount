<?php
/**
 * Order status card (Section 2): icon, title, description, est. delivery, tracking-aware timeline.
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;

$status            = $order->get_status();
$status_name       = wc_get_order_status_name( $status );
$timeline_context  = MyAccount_Core_Tracking_Module::instance()->get_timeline_context( $order );
$is_tracking_mode  = isset( $timeline_context['mode'] ) && 'tracking' === $timeline_context['mode'];
$step_count        = max( 1, (int) ( $timeline_context['step_count'] ?? 3 ) );
$current_step      = min( $step_count, max( 1, (int) ( $timeline_context['current_step'] ?? 1 ) ) );
$current_key       = sanitize_key( (string) ( $timeline_context['current_key'] ?? 'placed' ) );
$latest_ship_date  = isset( $timeline_context['latest_ship_date'] ) && is_string( $timeline_context['latest_ship_date'] ) ? $timeline_context['latest_ship_date'] : '';

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

$tracking_descriptions = array(
	'placed'     => __( 'Your order has been received and is awaiting payment.', 'woocommerce' ),
	'processing' => __( 'Your order has been confirmed and is being prepared for processing.', 'woocommerce' ),
	'partial_shipped' => __( 'Part of your order has shipped and the remaining items will follow soon.', 'myaccount-core' ),
	'shipped'    => __( 'Your order has shipped and is on the way.', 'myaccount-core' ),
	'delivered'  => __( 'Tracking shows your order was delivered.', 'myaccount-core' ),
);

$status_description = isset( $status_descriptions[ $status ] ) ? $status_descriptions[ $status ] : sprintf( __( 'Order status: %s', 'woocommerce' ), $status_name );

if ( $is_tracking_mode && isset( $tracking_descriptions[ $current_key ] ) ) {
	$status_description = $tracking_descriptions[ $current_key ];
}

$status_description = apply_filters( 'woocommerce_myaccount_order_status_description', $status_description, $order );

$current_titles = array(
	'placed'     => __( 'Order Placed', 'woocommerce' ),
	'processing' => __( 'Processing', 'woocommerce' ),
	'partial_shipped' => __( 'Partially Shipped', 'myaccount-core' ),
	'shipped'    => __( 'Shipped', 'myaccount-core' ),
	'delivered'  => __( 'Delivered', 'myaccount-core' ),
	'complete'   => __( 'Delivered', 'woocommerce' ),
);

$current_title = isset( $current_titles[ $current_key ] ) ? $current_titles[ $current_key ] : __( 'Order Placed', 'woocommerce' );

if ( ! $is_tracking_mode ) {
	$status_titles = array(
		'cancelled' => __( 'Cancelled', 'woocommerce' ),
		'failed'    => __( 'Payment Failed', 'woocommerce' ),
		'refunded'  => __( 'Refunded', 'woocommerce' ),
	);

	if ( isset( $status_titles[ $status ] ) ) {
		$current_title = $status_titles[ $status ];
	}
}

$progress_pct = 1 === $step_count ? 100 : ( ( $current_step - 1 ) / ( $step_count - 1 ) ) * 100;
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
				<h2 id="ma-order-status-card-heading" class="ma-order-status-card__title"><?php echo esc_html( $current_title ); ?></h2>
				<p class="ma-order-status-card__description"><?php echo esc_html( $status_description ); ?></p>
			</div>
		</div>
		<?php if ( $est_delivery && $current_step < $step_count && ! in_array( $status, array( 'cancelled', 'failed' ), true ) ) : ?>
			<div class="ma-order-status-card__est-delivery">
				<p class="ma-order-status-card__est-label"><?php esc_html_e( 'Est. Delivery', 'woocommerce' ); ?></p>
				<p class="ma-order-status-card__est-date"><?php echo esc_html( $est_delivery ); ?></p>
			</div>
		<?php endif; ?>
	</div>

	<div class="ma-order-status-card__timeline">
		<div class="ma-order-status-card__timeline-track" style="--ma-timeline-progress: <?php echo esc_attr( (string) $progress_pct ); ?>; --ma-timeline-steps: <?php echo (int) $step_count; ?>;">
			<div class="ma-order-status-card__timeline-line" aria-hidden="true"></div>
			<div class="ma-order-status-card__timeline-line-fill" aria-hidden="true"></div>
			<?php
			$fmt         = apply_filters( 'myaccount_core_order_status_card_date_format', wc_date_format(), $order );
			$date_placed = $order->get_date_created() ? $order->get_date_created()->date_i18n( $fmt ) : '';
			$date_paid   = $order->get_date_paid() ? $order->get_date_paid()->date_i18n( $fmt ) : '';
			$date_done   = $order->get_date_completed() ? $order->get_date_completed()->date_i18n( $fmt ) : '';
			$shipment_step_label = ( $is_tracking_mode && 'partial_shipped' === $current_key ) ? __( 'Partially Shipped', 'myaccount-core' ) : __( 'Shipped', 'myaccount-core' );
			$shipment_step_sublabel = ( $is_tracking_mode && 'partial_shipped' === $current_key ) ? __( 'Split shipment', 'myaccount-core' ) : __( 'In transit', 'myaccount-core' );

			$step_icons = array(
				'placed'     => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="ma-order-status-card__step-icon" aria-hidden="true"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" /><rect x="9" y="3" width="6" height="4" rx="1" /><path d="m9 14 2 2 4-4" /></svg>',
				'processing' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="ma-order-status-card__step-icon" aria-hidden="true"><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" /><path d="M3 21v-5h5" /><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" /><path d="M21 3v5h-5" /></svg>',
				'shipped'    => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="ma-order-status-card__step-icon" aria-hidden="true"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2" /><path d="M15 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 13.52 8H14" /><circle cx="17" cy="18" r="2" /><circle cx="7" cy="18" r="2" /></svg>',
				'delivered'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="ma-order-status-card__step-icon" aria-hidden="true"><path d="M21.801 10A10 10 0 1 1 17 3.335" /><path d="m9 11 3 3L22 4" /></svg>',
				'complete'   => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="ma-order-status-card__step-icon" aria-hidden="true"><path d="M21.801 10A10 10 0 1 1 17 3.335" /><path d="m9 11 3 3L22 4" /></svg>',
			);

			$steps = $is_tracking_mode
				? array(
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
						'key'      => 'shipped',
						'label'    => $shipment_step_label,
						'sublabel' => $shipment_step_sublabel,
						'date'     => ( $current_step >= 3 && $latest_ship_date ) ? $latest_ship_date : '',
					),
					array(
						'key'      => 'delivered',
						'label'    => __( 'Delivered', 'myaccount-core' ),
						'sublabel' => __( 'Order arrived', 'myaccount-core' ),
						'date'     => ( $current_step >= 4 && $date_done ) ? $date_done : '',
					),
				)
				: array(
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
				<div class="ma-order-status-card__step ma-order-status-card__step--<?php echo esc_attr( $step['key'] ); ?> <?php echo $is_current ? 'ma-order-status-card__step--current' : ''; ?> <?php echo $is_active ? 'ma-order-status-card__step--active' : ''; ?>">
					<div class="ma-order-status-card__step-dot" aria-hidden="true">
						<?php
						if ( isset( $step_icons[ $step['key'] ] ) ) {
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static Heroicons SVG markup from this template.
							echo $step_icons[ $step['key'] ];
						}
						?>
					</div>
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
