<?php
/**
 * Reusable empty state (shared My Account).
 * Standard layout: Heading + Description + primary CTA (link or button).
 *
 * Template vars:
 * - title, description, primary_label, modifier_class, heading_level, primary_icon (see below).
 * - primary_url + primary_label → anchor CTA (default).
 * - primary_as_button (bool) + primary_label + primary_button_attrs (string): button CTA for Alpine/popup.
 *   primary_button_attrs: trusted HTML attributes only (e.g. @click, :disabled). Output unescaped.
 * - If not button mode and primary_url/primary_label missing → shop + Browse products.
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;

$title                = isset( $title ) ? (string) $title : '';
$description          = isset( $description ) ? (string) $description : '';
$primary_url          = isset( $primary_url ) ? (string) $primary_url : '';
$primary_label        = isset( $primary_label ) ? (string) $primary_label : '';
$modifier_class       = isset( $modifier_class ) ? (string) $modifier_class : '';
$heading_level        = isset( $heading_level ) && in_array( $heading_level, array( 'h2', 'h3' ), true ) ? $heading_level : 'h2';
$primary_icon         = isset( $primary_icon ) ? (bool) $primary_icon : true;
$primary_as_button    = ! empty( $primary_as_button );
$primary_button_attrs = isset( $primary_button_attrs ) ? (string) $primary_button_attrs : '';

if ( '' === $title ) {
	return;
}

if ( '' === $description ) {
	$description = esc_html__( 'Use the action below to get started.', 'myaccount-core' );
}

if ( $primary_as_button ) {
	if ( '' === $primary_label ) {
		$primary_label = esc_html__( 'Continue', 'myaccount-core' );
	}
} elseif ( '' === $primary_url || '' === $primary_label ) {
	$primary_url   = esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) );
	$primary_label = esc_html__( 'Browse products', 'woocommerce' );
	$primary_icon  = true;
}

$root_class = trim( 'ma-empty-state ' . $modifier_class );
?>
<div class="<?php echo esc_attr( $root_class ); ?>">
	<<?php echo esc_attr( $heading_level ); ?> class="ma-empty-state__title"><?php echo esc_html( $title ); ?></<?php echo esc_attr( $heading_level ); ?>>
	<p class="ma-empty-state__description"><?php echo esc_html( $description ); ?></p>
	<div class="ma-empty-state__actions">
		<?php if ( $primary_as_button ) : ?>
			<button type="button" class="ma-btn ma-btn--primary" <?php echo $primary_button_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Alpine attrs from plugin template only. ?>>
				<?php if ( $primary_icon ) : ?>
					<svg xmlns="http://www.w3.org/2000/svg" class="ma-empty-state__icon ma-empty-state__icon--plus" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" focusable="false">
						<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
					</svg>
				<?php endif; ?>
				<span><?php echo esc_html( $primary_label ); ?></span>
			</button>
		<?php else : ?>
			<a href="<?php echo esc_url( $primary_url ); ?>" class="ma-btn ma-btn--primary">
				<?php if ( $primary_icon ) : ?>
					<svg xmlns="http://www.w3.org/2000/svg" class="ma-empty-state__icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" focusable="false">
						<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386a1.5 1.5 0 011.415 1.026L5.91 6.75m0 0h12.84m-12.84 0l1.531 8.677A1.5 1.5 0 008.917 16.5h8.666a1.5 1.5 0 001.476-1.073L20.25 9H6.75m2.25 10.5a1.125 1.125 0 11-2.25 0 1.125 1.125 0 012.25 0zm9 0a1.125 1.125 0 11-2.25 0 1.125 1.125 0 012.25 0z" />
					</svg>
				<?php endif; ?>
				<span><?php echo esc_html( $primary_label ); ?></span>
			</a>
		<?php endif; ?>
	</div>
</div>
