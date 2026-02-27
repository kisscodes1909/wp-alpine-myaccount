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

	public static function register_endpoints(): void {
		add_rewrite_endpoint( 'address', EP_ROOT | EP_PAGES );
	}

	private function __construct() {
		add_action( 'init', array( __CLASS__, 'register_endpoints' ) );

		add_filter( 'woocommerce_account_menu_items', array( $this, 'remove_dashboard_tab' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'remove_logout_menu_item' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'rename_menu_labels' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'add_address_menu_item' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'reorder_menu_items' ), 100 );

		add_filter( 'woocommerce_get_query_vars', array( $this, 'add_address_query_var' ) );
		add_action( 'woocommerce_account_address_endpoint', array( $this, 'render_address_endpoint' ) );

		add_filter( 'woocommerce_login_redirect', array( $this, 'redirect_after_login' ), 10, 2 );
		add_filter( 'woocommerce_my_account_my_orders_query', array( $this, 'limit_orders_per_page' ) );
		add_action( 'template_redirect', array( $this, 'redirect_dashboard_to_orders' ) );
		add_filter( 'body_class', array( $this, 'add_template_style_body_class' ) );
		add_action( 'wp_footer', array( $this, 'render_overlay_containers' ), 5 );
		add_action( 'wp_footer', array( $this, 'render_ui_templates' ), 6 );
	}

	public function remove_dashboard_tab( array $items ): array {
		unset( $items['dashboard'] );
		return $items;
	}

	public function remove_logout_menu_item( array $items ): array {
		unset( $items['customer-logout'] );
		return $items;
	}

	public function rename_menu_labels( array $items ): array {
		$items['orders']          = 'Order History';
		$items['payment-methods'] = 'Saved Payments';
		$items['edit-account']    = 'My Info';
		return $items;
	}

	public function add_address_menu_item( array $items ): array {
		$items['address'] = 'Address Book';
		return $items;
	}

	public function reorder_menu_items( array $items ): array {
		$ordered = array();
		$keys    = array( 'orders', 'edit-account', 'address', 'payment-methods' );

		foreach ( $keys as $key ) {
			if ( isset( $items[ $key ] ) ) {
				$ordered[ $key ] = $items[ $key ];
			}
		}

		return ! empty( $ordered ) ? $ordered : $items;
	}

	public function add_address_query_var( array $vars ): array {
		$vars['address'] = 'address';
		return $vars;
	}

	public function render_address_endpoint(): void {
		$user_id       = get_current_user_id();
		$countries     = WC()->countries->get_shipping_countries();
		$serialized    = get_user_meta( $user_id, 'address_book', true );
		$address_book  = maybe_unserialize( $serialized );
		$addresses     = is_array( $address_book ) ? $address_book : array();

		wc_get_template( 'myaccount/apl-address.php' );
		wc_get_template( 'myaccount/ma-form-edit-address.php' );

		wp_localize_script(
			'alpine-bundle',
			'scriptData',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'addresses' => $addresses,
				'countries' => $countries,
				'nonce'     => wp_create_nonce( 'save_address_nonce' ),
			)
		);

		wp_enqueue_script(
			'myaccount-core-address-googleapis',
			'https://maps.googleapis.com/maps/api/js?key=AIzaSyD-42Ska0L9w12EoymnnOFAPaF5uCdiPgU&language=en&loading=async',
			array( 'alpine-bundle' ),
			'1.0',
			true
		);
	}

	public function redirect_after_login( string $redirect, WP_User $user ): string {
		return wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) );
	}

	public function limit_orders_per_page( array $args ): array {
		$args['limit'] = 2;
		return $args;
	}

	public function redirect_dashboard_to_orders(): void {
		$is_builder = function_exists( 'bricks_is_builder' ) && bricks_is_builder();

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

		return $classes;
	}

	public function render_overlay_containers(): void {
		?>
		<div x-data id="popup-container" class="z-[999] fixed inset-0 overflow-y-auto flex items-center justify-center p-4" x-show="$store.popup.open" x-cloak aria-hidden="true"></div>
		<div x-data id="toast-container" class="z-[1000]"></div>
		<div x-data id="loader-container" class="z-[1001]"></div>
		<?php
	}

	public function render_ui_templates(): void {
		wc_get_template( 'ui/apl-toast.php' );
		wc_get_template( 'ui/apl-popup.php' );
		wc_get_template( 'ui/apl-loader.php' );
	}
}
