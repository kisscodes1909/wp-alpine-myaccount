<?php
/**
 * My Account navigation
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );
?>

<?php
$active_nav_label = '';
foreach ( wc_get_account_menu_items() as $endpoint => $label ) {
	if ( wc_is_current_account_menu_item( $endpoint ) ) {
		$active_nav_label = $label;
		break;
	}
}
?>

<div class="ma-nav-dropdown"
	 x-data="navDropdown"
	 :class="{ 'is-open': open }"
	 @click.outside="open = false"
	 data-active-label="<?php echo esc_attr( $active_nav_label ?: __( 'Menu', 'woocommerce' ) ); ?>">
	<nav class="woocommerce-MyAccount-navigation <?php echo ( get_option( 'myaccount_layout' ) === 'stacked' ) ? 'ma-fullbleed-band' : ''; ?>" aria-label="<?php esc_attr_e( 'Account pages', 'woocommerce' ); ?>">
		<div class="ma-nav-dropdown__trigger"
			 role="button"
			 tabindex="0"
			 :aria-expanded="open"
			 aria-haspopup="listbox"
			 @click="open = !open"
			 aria-label="<?php esc_attr_e( 'Account menu', 'woocommerce' ); ?>">
			<span class="ma-nav-dropdown__trigger-label"><?php echo esc_html( $active_nav_label ?: __( 'Menu', 'woocommerce' ) ); ?></span>
			<svg class="ma-nav-dropdown__trigger-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
			</svg>
		</div>
		<ul class="woocommerce-MyAccount-navigation-list ma-nav-dropdown__list" role="listbox">
			<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
				<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
					<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
					   class="woocommerce-MyAccount-navigation-link"
					   role="option"
					   <?php echo wc_is_current_account_menu_item( $endpoint ) ? ' aria-current="page" aria-selected="true"' : ''; ?>>
						<span class="ma-nav-link__label"><?php echo esc_html( $label ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
</div>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
