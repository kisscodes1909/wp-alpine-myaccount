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
	private function send_json_success( string $message, array $extra = [] ): void {
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
			$this->send_json_success( __( 'Password changed successfully.', 'woocommerce' ) );
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
		$email      = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );

		$current_user = get_user_by( 'id', $user_id );

		$user             = new stdClass();
		$user->ID         = $user_id;
		$user->first_name = ucfirst( $first_name );
		$user->last_name  = ucfirst( $last_name );

		if ( $email ) {
			if ( ! is_email( $email ) ) {
				$this->send_json_error( __( 'Please provide a valid email address.', 'woocommerce' ) );
			}
			if ( email_exists( $email ) && $email !== $current_user->user_email ) {
				$this->send_json_error( __( 'This email address is already registered.', 'woocommerce' ) );
			}
			$user->user_email = $email;
		}

		wp_update_user( $user );
		$this->send_json_success( __( 'Account details updated successfully.', 'woocommerce' ) );
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
				$customer->set_shipping_first_name( sanitize_text_field( $address['fname'] ?? '' ) );
				$customer->set_shipping_last_name( sanitize_text_field( $address['lname'] ?? '' ) );
				$customer->set_shipping_address_1( sanitize_text_field( $address['address'] ?? '' ) );
				$customer->set_shipping_address_2( sanitize_text_field( $address['address2'] ?? '' ) );
				$customer->set_shipping_city( sanitize_text_field( $address['city'] ?? '' ) );
				$customer->set_shipping_state( sanitize_text_field( $address['region'] ?? '' ) );
				$customer->set_shipping_postcode( sanitize_text_field( $address['postalCode'] ?? '' ) );
				$customer->set_shipping_country( sanitize_text_field( $address['country'] ?? '' ) );
				$customer->save();
				break;
			}
		}

		$sanitized_addresses = array();
		foreach ( $new_address as $addr ) {
			$sanitized_addresses[] = array(
				'id'         => isset( $addr['id'] ) ? sanitize_text_field( (string) $addr['id'] ) : '',
				'fname'      => sanitize_text_field( $addr['fname'] ?? '' ),
				'lname'      => sanitize_text_field( $addr['lname'] ?? '' ),
				'address'    => sanitize_text_field( $addr['address'] ?? '' ),
				'address2'   => sanitize_text_field( $addr['address2'] ?? '' ),
				'city'       => sanitize_text_field( $addr['city'] ?? '' ),
				'region'     => sanitize_text_field( $addr['region'] ?? '' ),
				'postalCode' => sanitize_text_field( $addr['postalCode'] ?? '' ),
				'country'    => sanitize_text_field( $addr['country'] ?? '' ),
				'phone'      => sanitize_text_field( $addr['phone'] ?? '' ),
				'default'    => ! empty( $addr['default'] ),
			);
		}
		update_user_meta( $user_id, 'address_book', maybe_serialize( $sanitized_addresses ) );

		$this->send_json_success( __( 'Address data saved successfully.', 'myaccount-core' ) );
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

			$message = wc_print_notice( __( 'Signup successful.', 'woocommerce' ), 'success', array(), true );
			$this->send_json_success( is_string( $message ) ? $message : __( 'Signup successful.', 'woocommerce' ), array( 'email' => $email ) );
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

			$message = wc_print_notice( __( 'Login successful.', 'woocommerce' ), 'success', array(), true );
			$this->send_json_success( is_string( $message ) ? $message : __( 'Login successful.', 'woocommerce' ), array( 'email' => $creds['user_login'] ) );
		} catch ( Exception $e ) {
			$msg = $e->getMessage();
			$this->send_json_error( wp_strip_all_tags( $msg ) ? $msg : __( 'Login failed.', 'woocommerce' ) );
		}
	}
}
