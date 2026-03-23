<?php

defined( 'ABSPATH' ) || exit;

class MyAccount_Core_Address_Module {
	private static ?MyAccount_Core_Address_Module $instance = null;

	public static function instance(): MyAccount_Core_Address_Module {
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
		add_filter( 'woocommerce_account_menu_items', array( $this, 'add_address_menu_item' ) );
		add_filter( 'woocommerce_get_query_vars', array( $this, 'add_address_query_var' ) );
		add_action( 'woocommerce_account_address_endpoint', array( $this, 'render_address_endpoint' ) );
		add_action( 'woocommerce_checkout_update_user_meta', array( $this, 'sync_address_book_default_from_checkout' ), 20, 2 );
		add_action( 'wp_ajax_save-address', array( $this, 'save_address_book' ) );
		add_filter( 'myaccount_core_managed_templates', array( $this, 'register_managed_templates' ) );
	}

	public function add_address_menu_item( array $items ): array {
		$items['address'] = 'Address Book';

		return $items;
	}

	public function add_address_query_var( array $vars ): array {
		$vars['address'] = 'address';

		return $vars;
	}

	public function render_address_endpoint(): void {
		$user_id         = get_current_user_id();
		$countries       = WC()->countries->get_countries();
		$customer        = new WC_Customer( $user_id );
		$addresses       = $this->get_user_address_book( $user_id );
		$default_country = $customer->get_id() ? $this->get_customer_default_country( $customer ) : '';

		wc_get_template( 'myaccount/apl-address.php' );
		wc_get_template( 'myaccount/ma-form-edit-address.php' );

		$address_localize_data = array(
			'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
			'addresses'      => $addresses,
			'countries'      => $countries,
			'defaultCountry' => $default_country,
			'nonce'          => wp_create_nonce( 'save_address_nonce' ),
		);
		$address_handle = wp_script_is( 'myaccount-core-js-endpoint', 'enqueued' ) ? 'myaccount-core-js-endpoint' : 'alpine-bundle';

		if ( wp_script_is( $address_handle, 'enqueued' ) ) {
			wp_localize_script( $address_handle, 'scriptData', $address_localize_data );
		}
	}

	/**
	 * Keep Address Book default in sync with the latest checkout shipping data.
	 *
	 * @param int   $customer_id Customer ID.
	 * @param array $data        Checkout posted data (unused, kept for hook signature).
	 */
	public function sync_address_book_default_from_checkout( int $customer_id, array $data = array() ): void {
		unset( $data );

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

	public function save_address_book(): void {
		wc_nocache_headers();
		$this->verify_nonce_or_die( 'save_address_nonce', 'nonce' );

		$raw_data    = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : '[]'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON
		$new_address = json_decode( is_string( $raw_data ) ? $raw_data : '[]', true );
		if ( ! is_array( $new_address ) ) {
			$this->send_json_error( __( 'Invalid address data.', 'myaccount-core' ) );
		}

		$user_id = get_current_user_id();
		if ( $user_id <= 0 ) {
			$this->send_json_error( __( 'You must be logged in to save addresses.', 'myaccount-core' ) );
		}

		$customer = new WC_Customer( $user_id );

		foreach ( $new_address as $address ) {
			if ( ! empty( $address['default'] ) ) {
				$country_code = $this->normalize_country_code( (string) ( $address['country'] ?? '' ) );

				$customer->set_shipping_first_name( sanitize_text_field( $address['fname'] ?? '' ) );
				$customer->set_shipping_last_name( sanitize_text_field( $address['lname'] ?? '' ) );
				$customer->set_shipping_address_1( sanitize_text_field( $address['address'] ?? '' ) );
				$customer->set_shipping_address_2( sanitize_text_field( $address['address2'] ?? '' ) );
				$customer->set_shipping_city( sanitize_text_field( $address['city'] ?? '' ) );
				$customer->set_shipping_state( sanitize_text_field( $address['region'] ?? '' ) );
				$customer->set_shipping_postcode( sanitize_text_field( $address['postalCode'] ?? '' ) );
				$customer->set_shipping_country( $country_code );
				$customer->save();
				break;
			}
		}

		$sanitized_addresses = array();
		foreach ( $new_address as $addr ) {
			$country_code          = $this->normalize_country_code( (string) ( $addr['country'] ?? '' ) );
			$sanitized_addresses[] = array(
				'id'         => isset( $addr['id'] ) ? sanitize_text_field( (string) $addr['id'] ) : '',
				'fname'      => sanitize_text_field( $addr['fname'] ?? '' ),
				'lname'      => sanitize_text_field( $addr['lname'] ?? '' ),
				'address'    => sanitize_text_field( $addr['address'] ?? '' ),
				'address2'   => sanitize_text_field( $addr['address2'] ?? '' ),
				'city'       => sanitize_text_field( $addr['city'] ?? '' ),
				'region'     => sanitize_text_field( $addr['region'] ?? '' ),
				'postalCode' => sanitize_text_field( $addr['postalCode'] ?? '' ),
				'country'    => $country_code,
				'phone'      => sanitize_text_field( $addr['phone'] ?? '' ),
				'default'    => ! empty( $addr['default'] ),
			);
		}

		update_user_meta( $user_id, 'address_book', maybe_serialize( $sanitized_addresses ) );

		$this->send_json_success( __( 'Your address has been saved.', 'myaccount-core' ) );
	}

	public function register_managed_templates( array $templates ): array {
		$templates[] = 'myaccount/apl-address.php';
		$templates[] = 'myaccount/ma-form-edit-address.php';

		return array_values( array_unique( $templates ) );
	}

	private function send_json_success( string $message, array $extra = array() ): void {
		wc_nocache_headers();
		wp_send_json_success( array_merge( array( 'message' => $message ), $extra ) );
	}

	private function send_json_error( string $message ): void {
		wc_nocache_headers();
		wp_send_json_error( array( 'message' => $message ) );
	}

	private function verify_nonce_or_die( string $action, string $key = 'nonce' ): void {
		$value = isset( $_REQUEST[ $key ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $key ] ) ) : '';
		if ( ! wp_verify_nonce( $value, $action ) ) {
			$this->send_json_error( __( 'Invalid security token. Please refresh and try again.', 'myaccount-core' ) );
		}
	}

	private function get_user_address_book( int $user_id ): array {
		$stored       = get_user_meta( $user_id, 'address_book', true );
		$address_book = maybe_unserialize( $stored );

		return is_array( $address_book ) ? $address_book : array();
	}

	private function get_customer_default_country( WC_Customer $customer ): string {
		$shipping_country = $this->normalize_country_code( (string) $customer->get_shipping_country() );
		if ( '' !== $shipping_country ) {
			return $shipping_country;
		}

		return $this->normalize_country_code( (string) $customer->get_billing_country() );
	}

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

	private function has_meaningful_address( array $address ): bool {
		$required_keys = array( 'fname', 'lname', 'address', 'city', 'postalCode', 'country' );
		$parts         = array();

		foreach ( $required_keys as $key ) {
			$parts[] = isset( $address[ $key ] ) ? (string) $address[ $key ] : '';
		}

		return '' !== implode( '', $parts );
	}

	private function upsert_default_address( array $addresses, array $default_address ): array {
		$default_idx = null;
		foreach ( $addresses as $idx => $addr ) {
			if ( ! empty( $addr['default'] ) ) {
				$default_idx = $idx;
				break;
			}
		}

		if ( null !== $default_idx ) {
			$existing_id               = isset( $addresses[ $default_idx ]['id'] ) ? sanitize_text_field( (string) $addresses[ $default_idx ]['id'] ) : '';
			$default_address['id']     = '' !== $existing_id ? $existing_id : wp_generate_uuid4();
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

	private function normalize_country_code( string $country_value ): string {
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
}
