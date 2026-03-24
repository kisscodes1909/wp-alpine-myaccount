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
		add_filter( 'woocommerce_account_menu_items', array( $this, 'rename_menu_labels' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'reorder_menu_items' ), 100 );

		add_filter( 'woocommerce_login_redirect', array( $this, 'redirect_after_login' ), 10, 2 );
		add_filter( 'woocommerce_my_account_my_orders_query', array( $this, 'limit_orders_per_page' ) );
		add_action( 'template_redirect', array( $this, 'redirect_guests_from_account_endpoints' ), 8 );
		add_action( 'template_redirect', array( $this, 'redirect_dashboard_to_orders' ) );
		add_filter( 'body_class', array( $this, 'add_template_style_body_class' ) );
		add_action( 'wp_footer', array( $this, 'render_overlay_containers' ), 5 );
		add_action( 'wp_footer', array( $this, 'render_ui_templates' ), 6 );
	}

	public function remove_dashboard_tab( array $items ): array {
		unset( $items['dashboard'] );
		return $items;
	}

	public function rename_menu_labels( array $items ): array {
		$items['orders']             = 'Order History';
		$items['payment-methods']    = 'Saved Payments';
		$items['edit-account']       = 'My Info';
		$items['customer-logout']    = __( 'Sign out', 'myaccount-core' );
		return $items;
	}

	public function reorder_menu_items( array $items ): array {
		$ordered = array();
		$keys    = array( 'orders', 'wishlist', 'edit-account', 'address', 'payment-methods', 'customer-logout' );

		foreach ( $keys as $key ) {
			if ( isset( $items[ $key ] ) ) {
				$ordered[ $key ] = $items[ $key ];
			}
		}

		return ! empty( $ordered ) ? $ordered : $items;
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
}
