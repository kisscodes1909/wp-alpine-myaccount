<?php
/**
 * My Account navigation
 *
 * Horizontal tab design per Figma (Kisscode My Account). Uses WooCommerce base
 * classes: woocommerce-MyAccount-navigation, woocommerce-MyAccount-navigation-link, is-active.
 * Tailwind/theme classes applied for layout and variants.
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

<div class="my-account-header hidden flex-col md:flex-row md:items-center md:justify-between gap-4 md:container md:px-8 md:mx-auto md:mb-6 mb-4">
	<h2 class="text-center md:text-left text-xl font-semibold md:text-2xl"><?php esc_html_e( 'My Account', 'woocommerce' ); ?></h2>
	<span class="text-center md:text-right text-sm md:text-base text-gray-600">
		<?php
		/* translators: 1: first name 2: last name */
		echo esc_html( sprintf( __( 'Not %1$s %2$s?', 'woocommerce' ), $current_user->first_name, $current_user->last_name ) );
		?>
		<a class="underline inline-flex items-center gap-1 text-gray-900 hover:no-underline" href="<?php echo esc_url( wp_logout_url() ); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
			</svg>
			<span><?php esc_html_e( 'Sign out', 'woocommerce' ); ?></span>
		</a>
	</span>
</div>

<div class="woocommerce-MyAccount-navigation-wrapper border-t border-b border-gray-200 bg-white">
	<nav class="woocommerce-MyAccount-navigation md:container md:px-8 mx-auto" aria-label="<?php esc_attr_e( 'Account pages', 'woocommerce' ); ?>">
		<ul class="woocommerce-MyAccount-navigation-list flex flex-wrap gap-0 overflow-x-auto md:overflow-visible py-0 list-none m-0 border-0">
			<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
				<li class="flex-shrink-0 <?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
					<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
					   class="woocommerce-MyAccount-navigation-link block py-4 px-4 md:px-6 text-base font-normal tracking-wide text-gray-600 no-underline border-b-2 border-transparent whitespace-nowrap hover:text-gray-800 md:py-4"
					   <?php echo wc_is_current_account_menu_item( $endpoint ) ? ' aria-current="page"' : ''; ?>>
						<span class="uppercase"><?php echo esc_html( $label ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
</div>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
