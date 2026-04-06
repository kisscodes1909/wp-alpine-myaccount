<?php

defined( 'ABSPATH' ) || exit;

class MyAccount_Core_Hooks {
	private static ?MyAccount_Core_Hooks $instance = null;

	public static function instance(): MyAccount_Core_Hooks {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		// Hide Hello Elementor theme page header on My Account pages only.
		add_filter( 'hello_elementor_page_title', array( $this, 'hide_hello_page_title_on_account' ), 10, 1 );

		add_filter( 'woocommerce_account_menu_items', array( $this, 'remove_dashboard_tab' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'rename_menu_labels' ), 20 );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'reorder_menu_items' ), 100 );
		add_filter( 'woocommerce_account_menu_item_classes', array( $this, 'account_menu_item_plugin_classes' ), 10, 2 );

		add_filter( 'woocommerce_login_redirect', array( $this, 'redirect_after_login' ), 10, 2 );
		add_filter( 'woocommerce_my_account_my_orders_query', array( $this, 'limit_orders_per_page' ) );
		add_action( 'template_redirect', array( $this, 'redirect_guests_from_account_endpoints' ), 8 );
		add_action( 'template_redirect', array( $this, 'redirect_dashboard_to_orders' ) );
		add_filter( 'body_class', array( $this, 'add_template_style_body_class' ) );
		add_action( 'wp_footer', array( $this, 'render_overlay_containers' ), 5 );
		add_action( 'wp_footer', array( $this, 'render_ui_templates' ), 6 );

		// Kadence outputs a duplicate account nav avatar on woocommerce_before_account_navigation; plugin nav already shows the same avatar via get_avatar_url().
		add_action( 'wp', array( $this, 'maybe_disable_kadence_account_navigation_extras' ), 1 );
	}

	/**
	 * Remove Kadence theme wrappers and dashboard avatar from WooCommerce account navigation when the same UI is provided by this plugin.
	 *
	 * Kadence registers these on Kadence\Woocommerce\Component — same user avatar source as core get_avatar() / get_avatar_url() (Gravatar + filters).
	 *
	 * @return void
	 */
	public function maybe_disable_kadence_account_navigation_extras(): void {
		if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
			return;
		}

		if ( ! (bool) apply_filters( 'myaccount_core_disable_kadence_account_navigation_extras', true ) ) {
			return;
		}

		if ( ! function_exists( '\Kadence\kadence' ) ) {
			return;
		}

		try {
			$theme = \Kadence\kadence();
			if ( ! is_object( $theme ) || ! method_exists( $theme, 'component' ) ) {
				return;
			}

			$woo = $theme->component( 'woocommerce' );
			if ( ! is_object( $woo ) ) {
				return;
			}

			remove_action( 'woocommerce_before_account_navigation', array( $woo, 'myaccount_nav_wrap_start' ), 2 );
			remove_action( 'woocommerce_before_account_navigation', array( $woo, 'myaccount_nav_avatar' ), 20 );
			remove_action( 'woocommerce_after_account_navigation', array( $woo, 'myaccount_nav_wrap_end' ), 50 );
		} catch ( \Throwable $e ) {
			// Kadence API unavailable or incompatible; leave theme output intact.
			return;
		}
	}

	/**
	 * Replace WooCommerce navigation li classes with plugin BEM (avoids theme CSS targeting Woo selectors).
	 *
	 * @param array  $classes WooCommerce classes (ignored; signature for filter).
	 * @param string $endpoint Account endpoint slug.
	 * @return array
	 */
	public function account_menu_item_plugin_classes( array $classes, $endpoint ): array {
		$endpoint = is_string( $endpoint ) ? $endpoint : '';

		$out = array(
			'ma-nav__item',
			'ma-nav__item--' . sanitize_html_class( $endpoint ),
		);

		if ( wc_is_current_account_menu_item( $endpoint ) ) {
			$out[] = 'is-active';
		}

		return $out;
	}

	public function remove_dashboard_tab( array $items ): array {
		unset( $items['dashboard'] );
		return $items;
	}

	public function rename_menu_labels( array $items ): array {
		$items['orders']             = __( 'Orders', 'myaccount-core' );
		$items['payment-methods']    = __( 'Payments', 'myaccount-core' );
		$items['edit-account']       = __( 'Profile', 'myaccount-core' );
		$items['address']            = __( 'Address Book', 'myaccount-core' );
		$items['customer-logout']    = __( 'Sign out', 'myaccount-core' );
		$preserve_third_party = ( '1' === get_option( 'myaccount_preserve_third_party_menu_items', '0' ) );
		if ( $preserve_third_party && isset( $items['downloads'] ) ) {
			$items['downloads'] = __( 'Downloads', 'myaccount-core' );
		}
		return $items;
	}

	public function reorder_menu_items( array $items ): array {
		$preserve_third_party = ( '1' === get_option( 'myaccount_preserve_third_party_menu_items', '0' ) );
		if ( ! $preserve_third_party ) {
			unset( $items['downloads'] );
		}

		$keys    = array( 'orders', 'downloads', 'wishlist', 'edit-account', 'address', 'payment-methods', 'customer-logout' );
		$ordered = array();

		foreach ( $keys as $key ) {
			if ( isset( $items[ $key ] ) ) {
				$ordered[ $key ] = $items[ $key ];
			}
		}

		if ( empty( $ordered ) ) {
			return $items;
		}

		if ( $preserve_third_party ) {
			$logout_label = null;
			if ( isset( $ordered['customer-logout'] ) ) {
				$logout_label = $ordered['customer-logout'];
				unset( $ordered['customer-logout'] );
			}

			foreach ( $items as $endpoint => $label ) {
				if ( ! isset( $ordered[ $endpoint ] ) ) {
					$ordered[ $endpoint ] = $label;
				}
			}

			if ( null !== $logout_label ) {
				$ordered['customer-logout'] = $logout_label;
			}
		}

		return $ordered;
	}

	/**
	 * Hide Hello Elementor theme page title only on WooCommerce My Account pages.
	 *
	 * @param bool $show Whether to show the page title.
	 * @return bool
	 */
	public function hide_hello_page_title_on_account( $show ): bool {
		if ( function_exists( 'is_account_page' ) && is_account_page() ) {
			return false;
		}
		return (bool) $show;
	}

	public function redirect_after_login( string $redirect, WP_User $user ): string {
		return wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) );
	}

	public function limit_orders_per_page( array $args ): array {
		$args['limit'] = 6;
		return $args;
	}

	/**
	 * Redirect guests away from account endpoints, except lost-password.
	 */
	public function redirect_guests_from_account_endpoints(): void {
		$is_builder = function_exists( 'bricks_is_builder' ) && bricks_is_builder();

		if ( ! is_account_page() || is_user_logged_in() || $is_builder || ! is_wc_endpoint_url() ) {
			return;
		}

		if ( is_wc_endpoint_url( 'lost-password' ) ) {
			return;
		}

		wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
		exit;
	}

	public function redirect_dashboard_to_orders(): void {
		$is_builder = function_exists( 'bricks_is_builder' ) && bricks_is_builder();

		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( is_account_page() && ! is_wc_endpoint_url() && ! $is_builder ) {
			wp_safe_redirect( wc_get_endpoint_url( 'orders' ) );
			exit;
		}
	}

	public function add_template_style_body_class( array $classes ): array {
		$is_builder = function_exists( 'bricks_is_builder' ) && bricks_is_builder();
		if ( ! is_account_page() || $is_builder ) {
			return $classes;
		}

		$template = get_option( 'myaccount_template_style', 'fashion' );
		$allowed  = array( 'fashion', 'a', 'b', 'c' );

		if ( ! in_array( $template, $allowed, true ) ) {
			$template = 'fashion';
		}

		$classes[] = 'myaccount-template-' . sanitize_html_class( $template );

		if ( get_option( 'myaccount_layout' ) === 'stacked' ) {
			$classes[] = 'ma-layout-stacked';
		} else {
			$classes[] = 'ma-layout-vertical';
		}

		return $classes;
	}

	public function render_overlay_containers(): void {
		?>
		<div x-data id="popup-container" class="ma-ui-overlay-container ma-ui-popup-container" x-show="$store.popup.open" x-cloak :aria-hidden="!$store.popup.open"></div>
		<div x-data id="toast-container" class="ma-ui-overlay-container ma-ui-toast-container"></div>
		<div x-data id="loader-container" class="ma-ui-overlay-container ma-ui-loader-container"></div>
		<?php
	}

	public function render_ui_templates(): void {
		wc_get_template( 'ui/apl-toast.php' );
		wc_get_template( 'ui/apl-popup.php' );
		wc_get_template( 'ui/apl-loader.php' );
	}

	/**
	 * Optional membership tier line (empty string hides the row).
	 *
	 * @param WP_User $user Current user.
	 * @return string
	 */
	public static function get_navigation_membership_label( WP_User $user ): string {
		return (string) apply_filters( 'myaccount_core_navigation_membership_label', '', $user );
	}
}
