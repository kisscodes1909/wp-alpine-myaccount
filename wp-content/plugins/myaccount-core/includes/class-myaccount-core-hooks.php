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

		// Hide Hello Elementor theme page header on My Account pages only.
		add_filter( 'hello_elementor_page_title', array( $this, 'hide_hello_page_title_on_account' ), 10, 1 );

		add_filter( 'woocommerce_account_menu_items', array( $this, 'remove_dashboard_tab' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'rename_menu_labels' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'add_address_menu_item' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'reorder_menu_items' ), 100 );

		add_filter( 'woocommerce_get_query_vars', array( $this, 'add_address_query_var' ) );
		add_action( 'woocommerce_account_address_endpoint', array( $this, 'render_address_endpoint' ) );

		add_filter( 'woocommerce_login_redirect', array( $this, 'redirect_after_login' ), 10, 2 );
		add_filter( 'woocommerce_my_account_my_orders_query', array( $this, 'limit_orders_per_page' ) );
		add_action( 'woocommerce_checkout_update_user_meta', array( $this, 'sync_address_book_default_from_checkout' ), 20, 2 );
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

	public function add_address_menu_item( array $items ): array {
		$items['address'] = 'Address Book';
		return $items;
	}

	public function reorder_menu_items( array $items ): array {
		$ordered = array();
		$keys    = array( 'orders', 'edit-account', 'address', 'payment-methods', 'customer-logout' );

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

	public function render_address_endpoint(): void {
		$user_id       = get_current_user_id();
		$countries     = WC()->countries->get_countries();
		$customer      = new WC_Customer( $user_id );
		$addresses     = $this->get_user_address_book( $user_id );
		$default_country = $customer->get_id() ? $this->get_customer_default_country( $customer ) : '';

		wc_get_template( 'myaccount/apl-address.php' );
		wc_get_template( 'myaccount/ma-form-edit-address.php' );

		$address_localize_data = array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'addresses' => $addresses,
			'countries' => $countries,
			'defaultCountry' => $default_country,
			'nonce'     => wp_create_nonce( 'save_address_nonce' ),
		);
		$address_handle        = wp_script_is( 'myaccount-core-js-endpoint', 'enqueued' ) ? 'myaccount-core-js-endpoint' : 'alpine-bundle';

		if ( wp_script_is( $address_handle, 'enqueued' ) ) {
			wp_localize_script(
				$address_handle,
				'scriptData',
				$address_localize_data
			);
		}
	}

	public function redirect_after_login( string $redirect, WP_User $user ): string {
		return wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) );
	}

	public function limit_orders_per_page( array $args ): array {
		$args['limit'] = 6;
		return $args;
	}

	/**
	 * Keep Address Book default in sync with the latest checkout shipping data.
	 *
	 * @param int   $customer_id Customer ID.
	 * @param array $data        Checkout posted data (unused, kept for hook signature).
	 */
	public function sync_address_book_default_from_checkout( int $customer_id, array $data = array() ): void {
		if ( $customer_id <= 0 ) {
			return;
		}

		$customer = new WC_Customer( $customer_id );
		if ( ! $customer->get_id() ) {
			return;
		}

		$default_address = $this->build_default_address_from_customer( $customer );
		if ( ! $this->has_meaningful_address( $default_address ) ) {
			return;
		}

		$addresses = $this->get_user_address_book( $customer_id );
		$addresses = $this->upsert_default_address( $addresses, $default_address );

		update_user_meta( $customer_id, 'address_book', maybe_serialize( $addresses ) );
	}

	/**
	 * Get normalized Address Book array from user meta.
	 */
	private function get_user_address_book( int $user_id ): array {
		$stored       = get_user_meta( $user_id, 'address_book', true );
		$address_book = maybe_unserialize( $stored );
		return is_array( $address_book ) ? $address_book : array();
	}

	/**
	 * Get customer's preferred country code for Address Book defaults.
	 */
	private function get_customer_default_country( WC_Customer $customer ): string {
		$shipping_country = $this->resolve_country_code( (string) $customer->get_shipping_country() );
		if ( '' !== $shipping_country ) {
			return $shipping_country;
		}
		return $this->resolve_country_code( (string) $customer->get_billing_country() );
	}

	/**
	 * Build the Address Book default address shape from current checkout/customer shipping data.
	 */
	private function build_default_address_from_customer( WC_Customer $customer ): array {
		$shipping_country = $this->get_customer_default_country( $customer );
		$shipping_state   = (string) $customer->get_shipping_state();

		return array(
			'id'         => '',
			'fname'      => (string) $customer->get_shipping_first_name(),
			'lname'      => (string) $customer->get_shipping_last_name(),
			'address'    => (string) $customer->get_shipping_address_1(),
			'address2'   => (string) $customer->get_shipping_address_2(),
			'city'       => (string) $customer->get_shipping_city(),
			'region'     => $this->resolve_region_label( $shipping_country, $shipping_state ),
			'postalCode' => (string) $customer->get_shipping_postcode(),
			'country'    => $shipping_country,
			'phone'      => method_exists( $customer, 'get_shipping_phone' ) ? (string) $customer->get_shipping_phone() : '',
			'default'    => true,
		);
	}

	/**
	 * Require a minimum set of shipping fields before syncing into Address Book.
	 */
	private function has_meaningful_address( array $address ): bool {
		$required_keys = array( 'fname', 'lname', 'address', 'city', 'postalCode', 'country' );
		$parts         = array();

		foreach ( $required_keys as $key ) {
			$parts[] = isset( $address[ $key ] ) ? (string) $address[ $key ] : '';
		}

		return '' !== implode( '', $parts );
	}

	/**
	 * Insert or replace the current default address and enforce a single default record.
	 */
	private function upsert_default_address( array $addresses, array $default_address ): array {
		$default_idx = null;
		foreach ( $addresses as $idx => $addr ) {
			if ( ! empty( $addr['default'] ) ) {
				$default_idx = $idx;
				break;
			}
		}

		if ( null !== $default_idx ) {
			$existing_id              = isset( $addresses[ $default_idx ]['id'] ) ? sanitize_text_field( (string) $addresses[ $default_idx ]['id'] ) : '';
			$default_address['id']    = '' !== $existing_id ? $existing_id : wp_generate_uuid4();
			$addresses[ $default_idx ] = $default_address;
		} else {
			$default_address['id'] = wp_generate_uuid4();
			array_unshift( $addresses, $default_address );
		}

		foreach ( $addresses as $idx => $addr ) {
			$addresses[ $idx ]['default'] = isset( $default_address['id'], $addr['id'] ) && (string) $addr['id'] === (string) $default_address['id'];
		}

		return $addresses;
	}

	/**
	 * Resolve country to Woo country code; accepts code or label.
	 */
	private function resolve_country_code( string $country_value ): string {
		$country_value = sanitize_text_field( $country_value );
		if ( '' === $country_value ) {
			return '';
		}

		$countries = WC()->countries->get_countries();
		if ( isset( $countries[ $country_value ] ) ) {
			return $country_value;
		}

		$needle = strtolower( $country_value );
		foreach ( $countries as $code => $label ) {
			if ( strtolower( (string) $label ) === $needle ) {
				return (string) $code;
			}
		}

		return '';
	}

	/**
	 * Resolve shipping state code to human-readable label for Address Book display.
	 */
	private function resolve_region_label( string $country_code, string $state_value ): string {
		$country_code = sanitize_text_field( $country_code );
		$state_value  = sanitize_text_field( $state_value );

		if ( '' === $country_code || '' === $state_value ) {
			return $state_value;
		}

		$states = WC()->countries->get_states( $country_code );
		if ( is_array( $states ) && isset( $states[ $state_value ] ) ) {
			return (string) $states[ $state_value ];
		}

		return $state_value;
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
