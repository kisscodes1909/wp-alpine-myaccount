<?php
/**
 * View-order returns section.
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;

$section_id        = isset( $section_id ) ? (string) $section_id : 'ma-view-order-returns';
$existing_requests = isset( $existing_requests ) && is_array( $existing_requests ) ? $existing_requests : array();
$eligible_items    = isset( $eligible_items ) && is_array( $eligible_items ) ? $eligible_items : array();
$policy            = isset( $policy ) && is_array( $policy ) ? $policy : array();
$request_types     = isset( $request_types ) && is_array( $request_types ) ? $request_types : array();
$can_submit        = ! empty( $policy['is_eligible'] ) && ! empty( $eligible_items );
$popup_template_id = 'ma-view-order-returns-popup-template-' . (int) $order->get_id();
?>
<section class="ma-view-order-returns" aria-labelledby="<?php echo esc_attr( $section_id ); ?>" x-data="viewOrderReturns()" data-ma-returns-module>
	<div class="ma-view-order-returns__header">
		<div>
			<h2 id="<?php echo esc_attr( $section_id ); ?>" class="ma-u-section-title ma-u-section-title--mb-md">
				<?php esc_html_e( 'Returns & Exchanges', 'myaccount-core' ); ?>
			</h2>
			<p class="ma-u-section-description">
				<?php esc_html_e( 'Start a request for eligible items and track its status here.', 'myaccount-core' ); ?>
			</p>
		</div>

		<?php if ( $can_submit ) : ?>
			<button
				type="button"
				class="ma-btn ma-btn--primary ma-view-order-returns__toggle"
				@click="openPopup()"
			>
				<span><?php esc_html_e( 'Request return or exchange', 'myaccount-core' ); ?></span>
			</button>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $policy['message'] ) || ! empty( $policy['reference_date'] ) || ! empty( $policy['deadline_date'] ) ) : ?>
	<div class="ma-view-order-returns__policy">
		<?php if ( ! empty( $policy['message'] ) ) : ?>
			<p class="ma-view-order-returns__policy-text"><?php echo esc_html( $policy['message'] ); ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $policy['reference_date'] ) || ! empty( $policy['deadline_date'] ) ) : ?>
			<p class="ma-view-order-returns__policy-meta">
				<?php if ( ! empty( $policy['reference_date'] ) ) : ?>
				<span>
					<?php esc_html_e( 'Reference date:', 'myaccount-core' ); ?>
					<strong><?php echo esc_html( $policy['reference_date'] ); ?></strong>
				</span>
				<?php endif; ?>
				<?php if ( ! empty( $policy['deadline_date'] ) ) : ?>
				<span>
					<?php esc_html_e( 'Return deadline:', 'myaccount-core' ); ?>
					<strong><?php echo esc_html( $policy['deadline_date'] ); ?></strong>
				</span>
				<?php endif; ?>
			</p>
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<?php if ( ! empty( $existing_requests ) ) : ?>
	<div class="ma-view-order-returns__list">
		<?php foreach ( $existing_requests as $request ) : ?>
			<article class="ma-view-order-returns__request ma-u-surface-panel">
				<div class="ma-view-order-returns__request-header">
					<div class="ma-view-order-returns__request-meta">
						<p class="ma-view-order-returns__request-id">
							<?php esc_html_e( 'Request', 'myaccount-core' ); ?>
							<span><?php echo esc_html( '#' . strtoupper( substr( (string) $request['id'], 0, 8 ) ) ); ?></span>
						</p>
						<p class="ma-view-order-returns__request-date"><?php echo esc_html( $request['created_at_label'] ?? '' ); ?></p>
					</div>
					<?php
					$ma_return_status = isset( $request['status'] ) ? sanitize_key( (string) $request['status'] ) : 'submitted';
					$ma_return_badge_tone = 'ma-u-badge--info';
					if ( in_array( $ma_return_status, array( 'approved', 'received', 'completed' ), true ) ) {
						$ma_return_badge_tone = 'ma-u-badge--success';
					} elseif ( 'rejected' === $ma_return_status ) {
						$ma_return_badge_tone = 'ma-u-badge--danger';
					}
					?>
					<div class="ma-view-order-returns__request-badges">
						<span class="ma-view-order-returns__badge ma-u-badge ma-u-badge--muted"><?php echo esc_html( $request['request_type_label'] ?? '' ); ?></span>
						<span class="ma-view-order-returns__badge ma-u-badge <?php echo esc_attr( $ma_return_badge_tone ); ?>"><?php echo esc_html( $request['status_label'] ?? '' ); ?></span>
					</div>
				</div>

				<div class="ma-view-order-returns__request-body">
					<div>
						<p class="ma-view-order-returns__request-label"><?php esc_html_e( 'Items', 'myaccount-core' ); ?></p>
						<ul class="ma-view-order-returns__request-items">
							<?php foreach ( (array) ( $request['items'] ?? array() ) as $request_item ) : ?>
								<li>
									<span><?php echo esc_html( $request_item['product_name'] ?? '' ); ?></span>
									<strong><?php echo esc_html( '× ' . (int) ( $request_item['qty'] ?? 0 ) ); ?></strong>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>

					<div>
						<p class="ma-view-order-returns__request-label"><?php esc_html_e( 'Reason', 'myaccount-core' ); ?></p>
						<p class="ma-view-order-returns__request-value"><?php echo esc_html( $request['reason'] ?? '' ); ?></p>
						<?php if ( ! empty( $request['note'] ) ) : ?>
							<p class="ma-view-order-returns__request-note"><?php echo esc_html( $request['note'] ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<?php if ( empty( $existing_requests ) ) : ?>
	<div class="ma-view-order-returns__empty">
		<?php
		wc_get_template(
			'myaccount/partials/ma-empty-state.php',
			array(
				'title'                => __( 'No return requests yet', 'myaccount-core' ),
				'description'          => __( 'Eligible items from this order can be returned or exchanged here during the return window.', 'myaccount-core' ),
				'primary_as_button'    => true,
				'primary_label'        => __( 'Start a request', 'myaccount-core' ),
				'primary_button_attrs' => $can_submit ? '@click="openPopup()"' : 'disabled="disabled"',
				'modifier_class'       => 'ma-empty-state--returns',
			)
		);
		?>
	</div>
	<?php endif; ?>
</section>

<?php
wc_get_template(
	'myaccount/ma-form-return-request.php',
	array(
		'template_id'    => $popup_template_id,
		'eligible_items' => $eligible_items,
		'request_types'  => $request_types,
	)
);
?>
