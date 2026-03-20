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
		<div class="ma-view-order-tracking__list">
			<?php foreach ( $tracking_entries as $tracking_entry ) : ?>
				<article class="ma-view-order-tracking__item">
					<div class="ma-view-order-tracking__item-head">
						<div class="ma-view-order-tracking__summary">
							<p class="ma-view-order-tracking__carrier"><?php echo esc_html( $tracking_entry->carrier_name ); ?></p>
							<p class="ma-view-order-tracking__number"><?php echo esc_html( $tracking_entry->tracking_number ); ?></p>
						</div>

						<?php if ( $tracking_entry->status_label || $tracking_entry->ship_date ) : ?>
							<div class="ma-view-order-tracking__meta">
								<?php if ( $tracking_entry->status_label ) : ?>
									<p class="ma-view-order-tracking__status"><?php echo esc_html( $tracking_entry->status_label ); ?></p>
								<?php endif; ?>
								<?php if ( $tracking_entry->ship_date ) : ?>
									<p class="ma-view-order-tracking__date"><?php echo esc_html( sprintf( __( 'Shipped %s', 'myaccount-core' ), $tracking_entry->ship_date ) ); ?></p>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>

					<?php if ( $tracking_entry->status_detail ) : ?>
						<p class="ma-view-order-tracking__detail"><?php echo esc_html( $tracking_entry->status_detail ); ?></p>
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
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
