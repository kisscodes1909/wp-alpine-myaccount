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
$active_nav_label   = '';
$ma_layout_stacked  = ( get_option( 'myaccount_layout' ) === 'stacked' );
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
	<nav class="ma-nav <?php echo $ma_layout_stacked ? 'ma-fullbleed-band' : ''; ?>" aria-label="<?php esc_attr_e( 'Account pages', 'woocommerce' ); ?>">
		<?php if ( is_user_logged_in() && ! $ma_layout_stacked ) : ?>
			<?php
			$ma_nav_user = wp_get_current_user();
			if ( $ma_nav_user && $ma_nav_user->exists() ) :
				$ma_nav_display  = MyAccount_Core_Hooks::get_navigation_display_name( $ma_nav_user );
				$ma_nav_initials  = MyAccount_Core_Hooks::get_navigation_user_initials( $ma_nav_user );
				$ma_nav_avatar_url = MyAccount_Core_Hooks::get_navigation_avatar_url( $ma_nav_user );
				$ma_nav_avatar_sz  = MyAccount_Core_Hooks::get_navigation_avatar_size( $ma_nav_user );
				$ma_nav_tier     = MyAccount_Core_Hooks::get_navigation_membership_label( $ma_nav_user );
				$ma_nav_show_status = (bool) apply_filters( 'myaccount_core_navigation_show_status', true, $ma_nav_user );
				$ma_nav_status_label = apply_filters( 'myaccount_core_navigation_status_label', __( 'Active', 'myaccount-core' ), $ma_nav_user );
				?>
				<div class="ma-nav__user">
					<div class="ma-nav__user-avatar" aria-hidden="true">
						<?php if ( '' !== $ma_nav_avatar_url ) : ?>
							<img
								class="ma-nav__user-avatar-img"
								src="<?php echo esc_url( $ma_nav_avatar_url ); ?>"
								alt=""
								width="<?php echo (int) $ma_nav_avatar_sz; ?>"
								height="<?php echo (int) $ma_nav_avatar_sz; ?>"
								loading="lazy"
								decoding="async"
								onerror="this.setAttribute('hidden', ''); var s=this.nextElementSibling; if(s){ s.removeAttribute('hidden'); }"
							/>
						<?php endif; ?>
						<span class="ma-nav__user-avatar-fallback"<?php echo '' !== $ma_nav_avatar_url ? ' hidden' : ''; ?>><?php echo esc_html( $ma_nav_initials ); ?></span>
					</div>
					<div class="ma-nav__user-body">
						<p class="ma-nav__user-name"><?php echo esc_html( $ma_nav_display ); ?></p>
						<?php if ( '' !== $ma_nav_tier ) : ?>
							<p class="ma-nav__user-tier"><?php echo esc_html( $ma_nav_tier ); ?></p>
						<?php endif; ?>
						<?php if ( $ma_nav_show_status ) : ?>
							<p class="ma-nav__user-status">
								<span class="ma-nav__user-status-dot" aria-hidden="true"></span>
								<span class="ma-nav__user-status-label"><?php echo esc_html( $ma_nav_status_label ); ?></span>
							</p>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
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
		<ul class="ma-nav__list ma-nav-dropdown__list" role="listbox">
			<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
				<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
					<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
					   class="ma-nav__link"
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
