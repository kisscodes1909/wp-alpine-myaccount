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

$current_user = wp_get_current_user();

do_action( 'woocommerce_before_account_navigation' );
?>

<?php // Temporarily commented out ma-nav-header.
if ( false ) : ?>
<div class="ma-nav-header ma-fullbleed-band">
	<h2 class="ma-nav-header__title"><?php esc_html_e( 'My Account', 'woocommerce' ); ?></h2>
	<span class="ma-nav-header__user">
		<?php
		/* translators: 1: first name 2: last name */
		echo esc_html( sprintf( __( 'Not %1$s %2$s?', 'woocommerce' ), $current_user->first_name, $current_user->last_name ) );
		?>
		<a class="ma-nav-header__signout" href="<?php echo esc_url( wp_logout_url() ); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" class="ma-nav-header__signout-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
			</svg>
			<span><?php esc_html_e( 'Sign out', 'woocommerce' ); ?></span>
		</a>
	</span>
</div>
<?php endif;

$active_nav_label = '';
foreach ( wc_get_account_menu_items() as $endpoint => $label ) {
	if ( wc_is_current_account_menu_item( $endpoint ) ) {
		$active_nav_label = $label;
		break;
	}
}
?>

<div class="ma-nav-dropdown" data-active-label="<?php echo esc_attr( $active_nav_label ?: __( 'Menu', 'woocommerce' ) ); ?>">
	<nav class="woocommerce-MyAccount-navigation <?php echo ( get_option( 'myaccount_layout' ) === 'stacked' ) ? 'ma-fullbleed-band' : ''; ?>" aria-label="<?php esc_attr_e( 'Account pages', 'woocommerce' ); ?>">
		<div class="ma-nav-dropdown__trigger"
			 role="button"
			 tabindex="0"
			 aria-expanded="false"
			 aria-haspopup="listbox"
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
