<?php
/**
 * View-order tracking block.
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $tracking_entries ) || ! is_array( $tracking_entries ) ) {
	return;
}

$section_id = 'ma-view-order-tracking-' . (int) $order->get_id();
?>
<section class="ma-view-order-tracking" aria-labelledby="<?php echo esc_attr( $section_id ); ?>">
	<h2 id="<?php echo esc_attr( $section_id ); ?>" class="ma-u-section-title ma-u-section-title--mb-md">
		<?php esc_html_e( 'Track Delivery', 'myaccount-core' ); ?>
	</h2>

	<div class="ma-view-order-tracking__card">
		<?php foreach ( $tracking_entries as $tracking_entry ) : ?>
			<article class="ma-view-order-tracking__item">
				<div class="ma-view-order-tracking__row">
					<div class="ma-view-order-tracking__identity">
						<span class="ma-view-order-tracking__icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M3.75 7.5h9.75v7.5H3.75z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/>
								<path d="M13.5 10.5h3.35l2.4 2.4v2.1H13.5z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/>
								<path d="M7.5 18.75a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM16.5 18.75a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" stroke="currentColor" stroke-width="1.75"/>
								<path d="M9 18.75h6M3.75 18.75H6" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
							</svg>
						</span>
						<p class="ma-view-order-tracking__carrier"><?php echo esc_html( $tracking_entry->carrier_name ); ?></p>
						<p class="ma-view-order-tracking__number"><?php echo esc_html( $tracking_entry->tracking_number ); ?></p>
					</div>

					<?php if ( $tracking_entry->status_label ) : ?>
						<p class="ma-view-order-tracking__status"><?php echo esc_html( $tracking_entry->status_label ); ?></p>
					<?php endif; ?>

					<?php if ( $tracking_entry->ship_date ) : ?>
						<p class="ma-view-order-tracking__date"><?php echo esc_html( sprintf( __( 'Shipped %s', 'myaccount-core' ), $tracking_entry->ship_date ) ); ?></p>
					<?php endif; ?>

					<div class="ma-view-order-tracking__actions">
						<a
							href="<?php echo esc_url( $tracking_entry->tracking_url ); ?>"
							class="ma-btn ma-btn--secondary"
							target="_blank"
							rel="noopener noreferrer"
						>
							<?php esc_html_e( 'Track delivery', 'myaccount-core' ); ?>
						</a>
					</div>
				</div>

				<?php if ( $tracking_entry->status_detail ) : ?>
					<p class="ma-view-order-tracking__detail"><?php echo esc_html( $tracking_entry->status_detail ); ?></p>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
</section>
