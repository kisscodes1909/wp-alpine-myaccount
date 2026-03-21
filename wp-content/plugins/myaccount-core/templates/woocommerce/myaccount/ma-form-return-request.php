<?php
/**
 * Returns request popup form.
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/partials/form-field-icons.php';

$eligible_items = isset( $eligible_items ) && is_array( $eligible_items ) ? $eligible_items : array();
$request_types  = isset( $request_types ) && is_array( $request_types ) ? $request_types : array();
$template_id    = isset( $template_id ) ? (string) $template_id : 'ma-view-order-returns-popup-template';
?>
<template id="<?php echo esc_attr( $template_id ); ?>" x-data>
	<form class="ma-form ma-view-order-returns__form ma-return-request-form" x-data="viewOrderReturnsForm()" @submit.prevent="handleSubmit">
		<div class="ma-return-request-form__header">
			<div class="ma-view-order-returns__form-head">
				<h2 class="ma-page-heading__title ma-page-heading__title--sm ma-view-order-returns__form-title"><?php esc_html_e( 'Create a return request', 'myaccount-core' ); ?></h2>
				<p class="ma-view-order-returns__form-description"><?php esc_html_e( 'Select the item quantities you want us to review, choose return or exchange, and tell us what happened.', 'myaccount-core' ); ?></p>
			</div>
			<button type="button" class="ma-btn ma-btn--ghost" @click="$store.popup.closePopup()" aria-label="<?php esc_attr_e( 'Close', 'myaccount-core' ); ?>">
				<?php ma_form_icon_x_mark(); ?>
			</button>
		</div>

		<div class="ma-view-order-returns__items">
			<?php foreach ( $eligible_items as $item ) : ?>
				<?php $item_id = (int) $item['item_id']; ?>
				<div class="ma-view-order-returns__item ma-line-card">
					<div class="ma-view-order-returns__item-media ma-line-card__media">
						<?php echo wp_kses_post( $item['image_html'] ); ?>
					</div>
					<div class="ma-view-order-returns__item-body ma-line-card__body">
						<p class="ma-view-order-returns__item-name"><?php echo esc_html( $item['product_name'] ); ?></p>
						<?php if ( ! empty( $item['meta_inline'] ) ) : ?>
							<p class="ma-view-order-returns__item-meta"><?php echo esc_html( $item['meta_inline'] ); ?></p>
						<?php endif; ?>
						<p class="ma-view-order-returns__item-stock">
							<?php
							echo esc_html(
								sprintf(
									/* translators: %d: remaining item quantity */
									__( '%d item(s) eligible', 'myaccount-core' ),
									(int) $item['remaining_qty']
								)
							);
							?>
						</p>
					</div>
					<div class="ma-view-order-returns__item-qty ma-form__field">
						<label for="ma-return-qty-<?php echo esc_attr( (string) $item_id ); ?>" class="screen-reader-text">
							<?php
							echo esc_html(
								sprintf(
									/* translators: %s: product name */
									__( 'Quantity to return for %s', 'myaccount-core' ),
									$item['product_name']
								)
							);
							?>
						</label>
						<div class="ma-form__input-wrap">
							<input
								id="ma-return-qty-<?php echo esc_attr( (string) $item_id ); ?>"
								class="ma-form__input ma-view-order-returns__qty-input"
								type="number"
								min="0"
								max="<?php echo esc_attr( (string) $item['remaining_qty'] ); ?>"
								step="1"
								x-model.number="form.itemQty[<?php echo esc_attr( (string) $item_id ); ?>]"
							>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="ma-view-order-returns__grid">
			<div class="ma-form__field ma-view-order-returns__field">
				<label for="ma-return-type" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'Request type', 'myaccount-core' ); ?></label>
				<div class="ma-form__input-wrap">
					<span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_arrows_right_left(); ?></span>
					<select id="ma-return-type" class="ma-form__input" x-model="form.requestType">
					<?php foreach ( $request_types as $request_type => $label ) : ?>
						<option value="<?php echo esc_attr( $request_type ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="ma-form__field ma-view-order-returns__field">
				<label for="ma-return-reason" class="ma-form__label ma-form__label--required"><?php esc_html_e( 'Reason', 'myaccount-core' ); ?></label>
				<div class="ma-form__input-wrap">
					<span class="ma-form__input-icon ma-form__input-icon--left" aria-hidden="true"><?php ma_form_icon_chat_bubble_left_right(); ?></span>
					<input id="ma-return-reason" class="ma-form__input" type="text" x-model.trim="form.reason" maxlength="160">
				</div>
			</div>
		</div>

		<div class="ma-form__field ma-view-order-returns__field">
			<label for="ma-return-note" class="ma-form__label"><?php esc_html_e( 'Notes for the store', 'myaccount-core' ); ?></label>
			<div class="ma-form__input-wrap">
				<span class="ma-form__input-icon ma-form__input-icon--left ma-view-order-returns__textarea-icon" aria-hidden="true"><?php ma_form_icon_pencil_square(); ?></span>
				<textarea id="ma-return-note" class="ma-form__input ma-view-order-returns__textarea" rows="4" x-model.trim="form.note" maxlength="500"></textarea>
			</div>
		</div>

		<p class="ma-view-order-returns__error" x-show="errorMessage" x-text="errorMessage"></p>

		<div class="ma-form-actions ma-form-actions--two ma-view-order-returns__actions">
			<button type="button" class="ma-btn ma-btn--secondary-light" @click="$store.popup.closePopup()">
				<?php esc_html_e( 'Cancel', 'myaccount-core' ); ?>
			</button>
			<button type="submit" class="ma-btn ma-btn--primary" :disabled="isSubmitting">
				<span x-show="!isSubmitting"><?php esc_html_e( 'Submit request', 'myaccount-core' ); ?></span>
				<span x-show="isSubmitting"><?php esc_html_e( 'Submitting…', 'myaccount-core' ); ?></span>
			</button>
		</div>
	</form>
</template>
