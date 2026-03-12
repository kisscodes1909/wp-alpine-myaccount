<?php

defined( 'ABSPATH' ) || exit;

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

	public function handle_change_password(): void {
		check_ajax_referer( 'change-password-action', 'nonce' );
		wc_nocache_headers();

		$user_id = get_current_user_id();
		if ( $user_id <= 0 ) {
			wp_send_json_error( __( 'User not found.', 'woocommerce' ) );
		}

		$current_user = get_user_by( 'id', $user_id );

		$pass_cur = $_POST['currentPassword'] ?? '';
		$pass1    = $_POST['pass1'] ?? '';
		$pass2    = $_POST['pass2'] ?? '';

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
			wp_send_json_error( implode( ' ', $errors ) );
		}

		if ( ! empty( $pass1 ) ) {
			$user            = new stdClass();
			$user->ID        = $user_id;
			$user->user_pass = $pass1;
			wp_update_user( $user );
			wp_send_json_success( 'Password changed successfully' );
		}

		wp_send_json_error( __( 'Please provide a new password.', 'woocommerce' ) );
	}

	public function save_account_details(): void {
		check_ajax_referer( 'save-account-details', 'nonce' );
		wc_nocache_headers();

		$user_id = get_current_user_id();
		if ( $user_id <= 0 ) {
			wp_send_json_error( 'User was not found!' );
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
				wp_send_json_error( __( 'Please provide a valid email address.', 'woocommerce' ) );
			} elseif ( email_exists( $email ) && $email !== $current_user->user_email ) {
				wp_send_json_error( __( 'This email address is already registered.', 'woocommerce' ) );
			}

			$user->user_email = $email;
		}

		wp_update_user( $user );

		wp_send_json_success( 'Account details updated successfully.' );
	}

	public function save_address_book(): void {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'save_address_nonce' ) ) {
			wp_send_json_error( 'Invalid security token' );
		}

		$new_address = json_decode( stripslashes( $_POST['data'] ?? '[]' ), true );
		$user_id     = get_current_user_id();

		$serialized_data = maybe_serialize( $new_address );
		$customer        = new WC_Customer( $user_id );

		foreach ( (array) $new_address as $address ) {
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

		update_user_meta( $user_id, 'address_book', $serialized_data );
		wp_send_json_success( 'Address data saved successfully' );
	}

	public function handle_signup(): void {
		$nonce_value = isset( $_REQUEST['signupNonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['signupNonce'] ) ) : '';

		if ( ! isset( $_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['password'] ) || ! wp_verify_nonce( $nonce_value, 'woocommerce-register' ) ) {
			wp_send_json_error(
				array(
					'message' => wc_print_notice( 'Required fields are missing.', 'error', array(), true ),
				)
			);
		}

		$email      = sanitize_email( wp_unslash( $_POST['email'] ) );
		$first_name = sanitize_text_field( wp_unslash( $_POST['firstName'] ) );
		$last_name  = sanitize_text_field( wp_unslash( $_POST['lastName'] ) );
		$password   = isset( $_POST['password'] ) ? (string) $_POST['password'] : '';

		try {
			$username          = '';
			$validation_error  = new WP_Error();
			$validation_error  = apply_filters( 'woocommerce_process_registration_errors', $validation_error, $username, $password, $email );
			$validation_errors = $validation_error->get_error_messages();

			if ( 1 === count( $validation_errors ) ) {
				throw new Exception( $validation_error->get_error_message() );
			} elseif ( $validation_errors ) {
				throw new Exception( $validation_errors[0] );
			}

			$new_customer = wc_create_new_customer(
				sanitize_email( $email ),
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

			wp_send_json_success(
				array(
					'email'   => $email,
					'message' => wc_print_notice( 'Signup Successfully', 'success', array(), true ),
				)
			);
		} catch ( Exception $e ) {
			wp_send_json_error(
				array(
					'message' => wc_print_notice( $e->getMessage(), 'error', array(), true ),
				)
			);
		}
	}

	public function handle_login_ajax(): void {
		$nonce_value = isset( $_REQUEST['woocommerceLoginNonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['woocommerceLoginNonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce_value, 'woocommerce-login' ) ) {
			wp_send_json_error(
				array(
					'message' => wc_print_notice( __( 'Security check failed. Please refresh and try again.', 'woocommerce' ), 'error', array(), true ),
				)
			);
		}

		if ( ! isset( $_POST['email'], $_POST['password'] ) ) {
			wp_send_json_error(
				array(
					'message' => wc_print_notice( __( 'Required fields are missing.', 'woocommerce' ), 'error', array(), true ),
				)
			);
		}

		try {
			$creds = array(
				'user_login'    => trim( wp_unslash( $_POST['email'] ) ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				'user_password' => $_POST['password'], // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				'remember'      => isset( $_POST['rememberme'] ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
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

			wp_send_json_success(
				array(
					'email'   => $creds['user_login'],
					'message' => wc_print_notice( 'Login Successfully', 'success', array(), true ),
				)
			);
		} catch ( Exception $e ) {
			wp_send_json_error(
				array(
					'message' => wc_print_notice( $e->getMessage(), 'error', array(), true ),
				)
			);
		}
	}
}
