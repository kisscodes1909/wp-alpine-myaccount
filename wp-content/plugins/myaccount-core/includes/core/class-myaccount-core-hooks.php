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
		// Temporary: hide Addresses from nav. Set MYACCOUNT_CORE_SHOW_ADDRESSES_MENU true or use filter myaccount_core_show_addresses_in_account_menu.
		add_filter( 'woocommerce_account_menu_items', array( $this, 'maybe_hide_addresses_menu' ), 120 );
		add_filter( 'woocommerce_account_menu_item_classes', array( $this, 'account_menu_item_plugin_classes' ), 10, 2 );

		add_filter( 'woocommerce_login_redirect', array( $this, 'redirect_after_login' ), 10, 2 );
		add_filter( 'woocommerce_my_account_my_orders_query', array( $this, 'limit_orders_per_page' ) );
		add_action( 'template_redirect', array( $this, 'redirect_guests_from_account_endpoints' ), 8 );
		add_action( 'template_redirect', array( $this, 'redirect_dashboard_to_orders' ) );
		add_filter( 'body_class', array( $this, 'add_template_style_body_class' ) );
		add_action( 'wp_footer', array( $this, 'render_overlay_containers' ), 5 );
		add_action( 'wp_footer', array( $this, 'render_ui_templates' ), 6 );
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

	/**
	 * Temporarily remove Addresses from the account menu (custom address book + Woo default).
	 *
	 * Re-enable: define MYACCOUNT_CORE_SHOW_ADDRESSES_MENU as true in wp-config.php, or
	 * add_filter( 'myaccount_core_show_addresses_in_account_menu', '__return_true' ); from a mu-plugin/theme.
	 *
	 * @param array $items Endpoint => label.
	 * @return array
	 */
	public function maybe_hide_addresses_menu( array $items ): array {
		// Default hidden until re-enabled via constant or filter.
		$show = false;
		if ( defined( 'MYACCOUNT_CORE_SHOW_ADDRESSES_MENU' ) ) {
			$show = (bool) MYACCOUNT_CORE_SHOW_ADDRESSES_MENU;
		}
		$show = (bool) apply_filters( 'myaccount_core_show_addresses_in_account_menu', $show );

		if ( $show ) {
			return $items;
		}

		unset( $items['address'], $items['edit-address'] );

		return $items;
	}

	public function rename_menu_labels( array $items ): array {
		$items['orders']             = __( 'Orders', 'myaccount-core' );
		$items['payment-methods']    = __( 'Payments', 'myaccount-core' );
		$items['edit-account']       = __( 'Profile', 'myaccount-core' );
		$items['address']            = __( 'Addresses', 'myaccount-core' );
		$items['customer-logout']    = __( 'Sign out', 'myaccount-core' );
		if ( isset( $items['downloads'] ) ) {
			$items['downloads'] = __( 'Downloads', 'myaccount-core' );
		}
		return $items;
	}

	public function reorder_menu_items( array $items ): array {
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

		if ( '1' === get_option( 'myaccount_preserve_third_party_menu_items', '0' ) ) {
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
	 * Display name for the account navigation user block.
	 *
	 * @param WP_User $user Current user.
	 * @return string
	 */
	public static function get_navigation_display_name( WP_User $user ): string {
		$name = trim( $user->display_name );
		if ( '' === $name ) {
			$name = trim( $user->user_login );
		}

		return (string) apply_filters( 'myaccount_core_navigation_display_name', $name, $user );
	}

	/**
	 * Gravatar / remote image size (max 512) for account navigation avatar.
	 *
	 * @param WP_User $user Current user.
	 * @return int
	 */
	public static function get_navigation_avatar_size( WP_User $user ): int {
		$size = absint( apply_filters( 'myaccount_core_navigation_avatar_size', 96, $user ) );

		return $size > 0 ? min( $size, 512 ) : 96;
	}

	/**
	 * Avatar image URL for account navigation.
	 * Uses Gravatar when the URL points at gravatar.com — verified via HTTP HEAD + transient
	 * so missing Gravatar does not render a broken <img> (and CSP does not need inline onerror).
	 * Non-Gravatar URLs (local avatar plugins) skip remote verification.
	 * Override via filter `myaccount_core_navigation_avatar_url` (non-empty string skips this logic).
	 *
	 * @param WP_User $user Current user.
	 * @return string
	 */
	public static function get_navigation_avatar_url( WP_User $user ): string {
		$custom = apply_filters( 'myaccount_core_navigation_avatar_url', null, $user );
		if ( is_string( $custom ) && '' !== $custom ) {
			return esc_url_raw( $custom );
		}

		$size = self::get_navigation_avatar_size( $user );
		$prim = get_avatar_url(
			$user->ID,
			array(
				'size' => $size,
			)
		);

		if ( ! is_string( $prim ) || '' === $prim ) {
			return '';
		}

		$lower = strtolower( $prim );
		if ( false === strpos( $lower, 'gravatar.com' ) ) {
			return $prim;
		}

		if ( ! self::navigation_gravatar_image_exists( $user, $size ) ) {
			return '';
		}

		return $prim;
	}

	/**
	 * Whether Gravatar has a real image for this email (HEAD to d=404 endpoint; result cached).
	 *
	 * @param WP_User $user Current user.
	 * @param int     $size Avatar size in pixels.
	 * @return bool
	 */
	private static function navigation_gravatar_image_exists( WP_User $user, int $size ): bool {
		if ( ! (bool) apply_filters( 'myaccount_core_navigation_verify_gravatar_with_remote', true, $user ) ) {
			return true;
		}

		$email = strtolower( trim( $user->user_email ) );
		if ( '' === $email || ! is_email( $email ) ) {
			return false;
		}

		$cache_key = 'ma_core_nav_grav_' . md5( $email . '|' . (string) $size );
		$cached    = get_transient( $cache_key );
		if ( false !== $cached ) {
			return ( '1' === $cached );
		}

		$hash      = md5( $email );
		$check_url = sprintf(
			'https://secure.gravatar.com/avatar/%s?s=%d&d=404',
			$hash,
			$size
		);

		$response = wp_remote_head(
			$check_url,
			array(
				'timeout'     => 3,
				'redirection' => 3,
				'httpversion' => '1.1',
				'headers'     => array(
					'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url( '/' ),
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( 200 === $code ) {
			set_transient( $cache_key, '1', DAY_IN_SECONDS );
			return true;
		}

		set_transient( $cache_key, '0', DAY_IN_SECONDS );
		return false;
	}

	/**
	 * Two-letter initials for the account navigation avatar (fallback when no image).
	 *
	 * @param WP_User $user Current user.
	 * @return string
	 */
	public static function get_navigation_user_initials( WP_User $user ): string {
		$first = trim( $user->first_name );
		$last  = trim( $user->last_name );

		if ( '' !== $first && '' !== $last ) {
			$initials = mb_strtoupper( mb_substr( $first, 0, 1 ) . mb_substr( $last, 0, 1 ), 'UTF-8' );
		} elseif ( '' !== $first ) {
			$initials = mb_strtoupper( mb_substr( $first, 0, 2 ), 'UTF-8' );
		} elseif ( '' !== $last ) {
			$initials = mb_strtoupper( mb_substr( $last, 0, 2 ), 'UTF-8' );
		} else {
			$parts = preg_split( '/\s+/', trim( $user->display_name ), -1, PREG_SPLIT_NO_EMPTY );
			if ( is_array( $parts ) && count( $parts ) >= 2 ) {
				$initials = mb_strtoupper( mb_substr( $parts[0], 0, 1 ) . mb_substr( $parts[ count( $parts ) - 1 ], 0, 1 ), 'UTF-8' );
			} elseif ( is_array( $parts ) && 1 === count( $parts ) ) {
				$initials = mb_strtoupper( mb_substr( $parts[0], 0, 2 ), 'UTF-8' );
			} else {
				$initials = mb_strtoupper( mb_substr( $user->user_login, 0, 2 ), 'UTF-8' );
			}
		}

		return (string) apply_filters( 'myaccount_core_navigation_user_initials', $initials, $user );
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
