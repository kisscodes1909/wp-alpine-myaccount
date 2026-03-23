<?php

defined( 'ABSPATH' ) || exit;

/**
 * AJAX handlers for My Account. All responses use consistent JSON shape:
 * - success: { "success": true, "data": { "message": "...", ...optional } }
 * - error:   { "success": false, "data": { "message": "..." } }
 */
class MyAccount_Core_Ajax {
	private static ?MyAccount_Core_Ajax $instance = null;

	public static function instance(): MyAccount_Core_Ajax {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_ajax_save-address', array( $this, 'save_address_book' ) );
		add_action( 'wp_ajax_save_account_details', array( $this, 'save_account_details' ) );
		add_action( 'wp_ajax_change_password', array( $this, 'handle_change_password' ) );
		if ( MyAccount_Core_Returns_Module::is_enabled() ) {
			add_action( 'wp_ajax_submit_return_request', array( $this, 'submit_return_request' ) );
		}
		add_action( 'wp_ajax_handle_login', array( $this, 'handle_login_ajax' ) );
		add_action( 'wp_ajax_nopriv_handle_login', array( $this, 'handle_login_ajax' ) );
		add_action( 'wp_ajax_handle_signup', array( $this, 'handle_signup' ) );
		add_action( 'wp_ajax_nopriv_handle_signup', array( $this, 'handle_signup' ) );
	}

	/**
	 * Send consistent JSON success. Data is always { "message": string, ...$extra }.
	 *
	 * @param string $message User-facing message.
	 * @param array  $extra   Optional extra keys (e.g. 'email', 'redirect').
	 */
	private function send_json_success( string $message, array $extra = array() ): void {
		wc_nocache_headers();
		wp_send_json_success( array_merge( array( 'message' => $message ), $extra ) );
	}

	/**
	 * Send consistent JSON error with a single message key.
	 *
	 * @param string $message User-facing error message.
	 */
	private function send_json_error( string $message ): void {
		wc_nocache_headers();
		wp_send_json_error( array( 'message' => $message ) );
	}

	/**
	 * Verify nonce from POST/REQUEST. Sends JSON error and exits on failure.
	 *
	 * @param string $action Nonce action.
	 * @param string $key    Request key (e.g. 'nonce', 'signupNonce').
	 * @return void Exits on failure.
	 */
	private function verify_nonce_or_die( string $action, string $key = 'nonce' ): void {
		$value = isset( $_REQUEST[ $key ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $key ] ) ) : '';
		if ( ! wp_verify_nonce( $value, $action ) ) {
			$this->send_json_error( __( 'Invalid security token. Please refresh and try again.', 'myaccount-core' ) );
		}
	}

	/**
	 * Normalize country value to Woo country code (e.g. "US").
	 * Supports legacy payloads where country is sent as country name.
	 */
	private function normalize_country_code( string $value ): string {
		$value     = sanitize_text_field( $value );
		$countries = WC()->countries->get_countries();

		if ( isset( $countries[ $value ] ) ) {
			return $value;
		}

		$needle = strtolower( $value );
		foreach ( $countries as $code => $label ) {
			if ( strtolower( $label ) === $needle ) {
				return (string) $code;
			}
		}

		return '';
	}

	public function handle_change_password(): void {
		wc_nocache_headers();
		$this->verify_nonce_or_die( 'change-password-action', 'nonce' );

		$user_id = get_current_user_id();
		if ( $user_id <= 0 ) {
			$this->send_json_error( __( 'User not found.', 'woocommerce' ) );
		}

		$current_user = get_user_by( 'id', $user_id );
		if ( ! $current_user ) {
			$this->send_json_error( __( 'User not found.', 'woocommerce' ) );
		}

		$pass_cur = isset( $_POST['currentPassword'] ) ? wp_unslash( $_POST['currentPassword'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- password
		$pass1    = isset( $_POST['pass1'] ) ? wp_unslash( $_POST['pass1'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- password
		$pass2    = isset( $_POST['pass2'] ) ? wp_unslash( $_POST['pass2'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- password

		$errors = array();

		if ( ! empty( $pass_cur ) && empty( $pass1 ) && empty( $pass2 ) ) {
			$errors[] = __( 'Please fill out all password fields.', 'woocommerce' );
		} elseif ( ! empty( $pass1 ) && empty( $pass_cur ) ) {
			$errors[] = __( 'Please enter your current password.', 'woocommerce' );
		} elseif ( ! empty( $pass1 ) && empty( $pass2 ) ) {
			$errors[] = __( 'Please re-enter your password.', 'woocommerce' );
		} elseif ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
			$errors[] = __( 'New passwords do not match.', 'woocommerce' );
		} elseif ( ! empty( $pass1 ) && ! wp_check_password( $pass_cur, $current_user->user_pass, $current_user->ID ) ) {
			$errors[] = __( 'Your current password is incorrect.', 'woocommerce' );
		}

		if ( ! empty( $errors ) ) {
			$this->send_json_error( implode( ' ', $errors ) );
		}

		if ( ! empty( $pass1 ) ) {
			$user            = new stdClass();
			$user->ID        = $user_id;
			$user->user_pass = $pass1;
			wp_update_user( $user );
			$this->send_json_success( __( 'Your password has been changed. You can now sign in with your new password.', 'myaccount-core' ) );
		}

		$this->send_json_error( __( 'Please provide a new password.', 'woocommerce' ) );
	}

	public function save_account_details(): void {
		wc_nocache_headers();
		$this->verify_nonce_or_die( 'save-account-details', 'nonce' );

		$user_id = get_current_user_id();
		if ( $user_id <= 0 ) {
			$this->send_json_error( __( 'User was not found!', 'woocommerce' ) );
		}

		$first_name = wc_clean( wp_unslash( $_POST['firstName'] ?? '' ) );
		$last_name  = wc_clean( wp_unslash( $_POST['lastName'] ?? '' ) );

		$user             = new stdClass();
		$user->ID         = $user_id;
		$user->first_name = ucfirst( $first_name );
		$user->last_name  = ucfirst( $last_name );

		// Billing name fields follow Personal (WC requires them on save); not shown in Contact UI.
		$_POST['billing_first_name'] = $first_name;
		$_POST['billing_last_name']  = $last_name;

		$billing_errors = $this->validate_and_save_billing_address( $user_id );
		if ( true !== $billing_errors ) {
			$this->send_json_error( $billing_errors );
		}

		wp_update_user( $user );

		$this->send_json_success( __( 'Your account and contact details have been updated.', 'myaccount-core' ) );
	}

	public function submit_return_request(): void {
		wc_nocache_headers();
		$this->verify_nonce_or_die( 'submit-return-request', 'nonce' );

		$user_id = get_current_user_id();
		if ( $user_id <= 0 ) {
			$this->send_json_error( __( 'Please sign in again and try submitting your return request.', 'myaccount-core' ) );
		}

		$order_id = isset( $_POST['orderId'] ) ? absint( wp_unslash( $_POST['orderId'] ) ) : 0;
		$order    = $order_id ? wc_get_order( $order_id ) : false;

		if ( ! $order instanceof WC_Order ) {
			$this->send_json_error( __( 'We could not find that order.', 'myaccount-core' ) );
		}

		$returns = MyAccount_Core_Returns_Service::instance();
		if ( ! $returns->user_owns_order( $order, $user_id ) ) {
			$this->send_json_error( __( 'You can only create return requests for your own orders.', 'myaccount-core' ) );
		}

		$raw_items = isset( $_POST['items'] ) ? wp_unslash( $_POST['items'] ) : '[]'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON payload.
		$items     = json_decode( is_string( $raw_items ) ? $raw_items : '[]', true );

		$result = $returns->create_request(
			$order,
			array(
				'requestType' => wc_clean( wp_unslash( $_POST['requestType'] ?? '' ) ),
				'reason'      => wc_clean( wp_unslash( $_POST['reason'] ?? '' ) ),
				'note'        => sanitize_textarea_field( wp_unslash( $_POST['note'] ?? '' ) ),
				'items'       => is_array( $items ) ? $items : array(),
			)
		);

		if ( is_wp_error( $result ) ) {
			$this->send_json_error( $result->get_error_message() );
		}

		$this->send_json_success(
			__( 'Your return request has been submitted.', 'myaccount-core' ),
			array(
				'request' => $result,
			)
		);
	}

	/**
	 * Validate billing fields like WC_Form_Handler::save_address, persist WC_Customer billing, fire Woo hook.
	 *
	 * @param int $user_id Current user.
	 * @return true|string True on success, error message on failure.
	 */
	private function validate_and_save_billing_address( int $user_id ) {
		$country = isset( $_POST['billing_country'] ) ? wc_clean( wp_unslash( $_POST['billing_country'] ) ) : '';
		if ( '' === $country ) {
			return __( 'Please select a country / region.', 'woocommerce' );
		}

		$customer = new WC_Customer( $user_id );
		if ( ! $customer || ! $customer->get_id() ) {
			return __( 'User was not found!', 'woocommerce' );
		}

		$address_type = 'billing';
		$address      = WC()->countries->get_address_fields( $country, $address_type . '_' );

		foreach ( $address as $key => $field ) {
			if ( ! isset( $field['type'] ) ) {
				$field['type'] = 'text';
			}

			if ( 'checkbox' === $field['type'] ) {
				$value = (int) isset( $_POST[ $key ] );
			} else {
				$value = isset( $_POST[ $key ] ) ? wc_clean( wp_unslash( $_POST[ $key ] ) ) : '';
			}

			$value = apply_filters( 'woocommerce_process_myaccount_field_' . $key, $value );

			if ( ! empty( $field['required'] ) && ( '' === $value || null === $value ) ) {
				return sprintf(
					/* translators: %s: Field label */
					__( '%s is a required field.', 'woocommerce' ),
					isset( $field['label'] ) ? wp_strip_all_tags( $field['label'] ) : $key
				);
			}

			if ( ! empty( $value ) && ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
				foreach ( $field['validate'] as $rule ) {
					switch ( $rule ) {
						case 'postcode':
							$value = wc_format_postcode( $value, $country );
							if ( '' !== $value && ! WC_Validation::is_postcode( $value, $country ) ) {
								return __( 'Please enter a valid postcode / ZIP.', 'woocommerce' );
							}
							break;
						case 'phone':
							if ( '' !== $value && ! WC_Validation::is_phone( $value ) ) {
								return __( 'Please enter a valid phone number.', 'woocommerce' );
							}
							break;
						case 'email':
							$value = strtolower( $value );
							if ( ! is_email( $value ) ) {
								return __( 'Please enter a valid billing email address.', 'woocommerce' );
							}
							break;
					}
				}
			}

			try {
				if ( is_callable( array( $customer, 'set_' . $key ) ) ) {
					$customer->{ 'set_' . $key }( $value );
				} else {
					$customer->update_meta_data( $key, $value );
				}
			} catch ( WC_Data_Exception $e ) {
				if ( 'customer_invalid_billing_email' !== $e->getErrorCode() ) {
					return $e->getMessage();
				}
			}
		}

		/**
		 * Same hook as Woo when saving address from My Account.
		 *
		 * @param int         $user_id      User ID.
		 * @param string      $address_type billing|shipping.
		 * @param array       $address      Field definitions.
		 * @param WC_Customer $customer     Customer object.
		 */
		do_action( 'woocommerce_after_save_address_validation', $user_id, $address_type, $address, $customer );

		$customer->save();

		do_action( 'woocommerce_customer_save_address', $user_id, $address_type );

		return true;
	}

	public function save_address_book(): void {
		wc_nocache_headers();
		$this->verify_nonce_or_die( 'save_address_nonce', 'nonce' );

		$raw_data = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : '[]'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON
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

	public function handle_signup(): void {
		wc_nocache_headers();
		$this->verify_nonce_or_die( 'woocommerce-register', 'signupNonce' );

		if ( ! isset( $_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['password'] ) ) {
			$this->send_json_error( __( 'Required fields are missing.', 'woocommerce' ) );
		}

		$email      = sanitize_email( wp_unslash( $_POST['email'] ) );
		$first_name = sanitize_text_field( wp_unslash( $_POST['firstName'] ) );
		$last_name  = sanitize_text_field( wp_unslash( $_POST['lastName'] ) );
		$password   = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- password

		try {
			$username         = '';
			$validation_error = new WP_Error();
			$validation_error  = apply_filters( 'woocommerce_process_registration_errors', $validation_error, $username, $password, $email );
			$validation_errors = $validation_error->get_error_messages();

			if ( 1 === count( $validation_errors ) ) {
				throw new Exception( $validation_error->get_error_message() );
			}
			if ( $validation_errors ) {
				throw new Exception( $validation_errors[0] );
			}

			$new_customer = wc_create_new_customer(
				$email,
				wc_clean( $username ),
				$password,
				array(
					'first_name' => $first_name,
					'last_name'  => $last_name,
				)
			);

			if ( is_wp_error( $new_customer ) ) {
				throw new Exception( $new_customer->get_error_message() );
			}

			wc_set_customer_auth_cookie( $new_customer );

			$this->send_json_success( __( 'Account created. Welcome!', 'myaccount-core' ), array( 'email' => $email ) );
		} catch ( Exception $e ) {
			$message = $e->getMessage();
			$this->send_json_error( wp_strip_all_tags( $message ) ? $message : __( 'Registration failed.', 'woocommerce' ) );
		}
	}

	public function handle_login_ajax(): void {
		wc_nocache_headers();
		$this->verify_nonce_or_die( 'woocommerce-login', 'woocommerceLoginNonce' );

		if ( ! isset( $_POST['email'], $_POST['password'] ) ) {
			$this->send_json_error( __( 'Required fields are missing.', 'woocommerce' ) );
		}

		$user_login = sanitize_text_field( wp_unslash( $_POST['email'] ) );
		$password   = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- password
		$remember   = isset( $_POST['rememberme'] );

		try {
			$creds = array(
				'user_login'    => trim( $user_login ),
				'user_password' => $password,
				'remember'      => $remember,
			);

			$validation_error = new WP_Error();
			$validation_error = apply_filters( 'woocommerce_process_login_errors', $validation_error, $creds['user_login'], $creds['user_password'] );

			if ( $validation_error->get_error_code() ) {
				throw new Exception( '<strong>' . __( 'Error:', 'woocommerce' ) . '</strong> ' . $validation_error->get_error_message() );
			}

			if ( empty( $creds['user_login'] ) ) {
				throw new Exception( '<strong>' . __( 'Error:', 'woocommerce' ) . '</strong> ' . __( 'Username is required.', 'woocommerce' ) );
			}

			$user = wp_signon( apply_filters( 'woocommerce_login_credentials', $creds ), is_ssl() );

			if ( is_wp_error( $user ) ) {
				throw new Exception( $user->get_error_message() );
			}

			$this->send_json_success( __( "You're signed in. Welcome back!", 'myaccount-core' ), array( 'email' => $creds['user_login'] ) );
		} catch ( Exception $e ) {
			$msg = $e->getMessage();
			$this->send_json_error( wp_strip_all_tags( $msg ) ? $msg : __( 'Login failed.', 'woocommerce' ) );
		}
	}
}
