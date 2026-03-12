<?php

class Theme_Ajax {
    public function __construct()
    {
        $myaccount_core_active = function_exists( 'myaccount_core_is_plugin_owner' ) && myaccount_core_is_plugin_owner();

        if ( ! $myaccount_core_active ) {
            add_action('wp_ajax_save-address', [$this, 'save_address_book']);
            add_action('wp_ajax_save_account_details', [$this,'save_account_details']);
            add_action('wp_ajax_change_password', [$this, 'handle_change_password']);
        }

        add_action('wp_ajax_handle_return_request', [$this, 'handle_return_request']);
        add_action('wp_ajax_nopriv_handle_return_request', [$this, 'handle_return_request']);
        add_action('wp_ajax_approve_return', [$this, 'handle_approve_return_ajax']);

        if ( ! $myaccount_core_active ) {
            add_action('wp_ajax_handle_login', [$this, 'handle_login_ajax']);
            add_action('wp_ajax_nopriv_handle_login', [$this, 'handle_login_ajax']);
            add_action('wp_ajax_handle_signup', [$this, 'handle_signup']);
            add_action('wp_ajax_nopriv_handle_signup', [$this, 'handle_signup']);
        }

//	    add_action('wc_ajax_nopriv_update_cart_item_quantity', [$this, 'update_cart_item_quantity']);
//
        add_action('authenticate', [$this, 'modify_authenticate_error'], 100, 3);

	    add_action('wp_ajax_wishlist_add_item', [$this, 'wishlist_add_item']);
	    add_action('wp_ajax_nopriv_wishlist_add_item', [$this, 'wishlist_add_item']);

	    add_action('wp_ajax_get_wishlist', [$this, 'get_wishlist']);
	    add_action('wp_ajax_nopriv_get_wishlist', [$this, 'get_wishlist']);

	    add_action('wp_ajax_wishlist_remove_item', [$this, 'wishlist_remove_item']);
	    add_action('wp_ajax_nopriv_wishlist_remove_item', [$this, 'wishlist_remove_item']);

	    add_action('wp_ajax_test_ajax', [$this, 'test_ajax']);
	    add_action('wp_ajax_nopriv_test_ajax', [$this, 'test_ajax']);

    }

	function test_ajax() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'test_ajax' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'bricks-child' ) ) );
		}
		wp_send_json_success( array( 'message' => 'ok' ) );
	}

    function modify_authenticate_error(WP_Error | WP_User $result, $user, $password): WP_Error|WP_User
    {
        if(is_wp_error($result)) {
            //var_dump($result);

            if($result->get_error_messages('invalid_email')) {
                $result->errors['invalid_email'][0] = 'Your email address or password is incorrect.';
            }

            if($result->get_error_messages('incorrect_password')) {
                $result->errors['incorrect_password'][0] = 'Your email address or password is incorrect.';
            }
            //die;
        }

        return $result;
    }

    function handle_approve_return_ajax() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'approve_return' ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed.', 'bricks-child' ) ) );
        }
        $order_id   = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
        $row_index  = isset( $_POST['row_index'] ) ? absint( $_POST['row_index'] ) : -1;
        $fields_raw = isset( $_POST['fields'] ) ? wp_unslash( $_POST['fields'] ) : '';
        $fields     = is_string( $fields_raw ) ? json_decode( stripslashes( $fields_raw ), true ) : array();
        $fields     = is_array( $fields ) ? $fields : array();

        $packageLabelACFKey = $fields['package_label']['key'];
        $statusACFKey = $fields['status']['key'];
        $requestIdACFKey = $fields['id']['key']; // Add this line to get the key of the id field

        if ($order_id && $row_index !== -1) {
            $repeater_field_key = 'order_return_request'; // Key of the repeater field
            $repeater_value = get_field($repeater_field_key, $order_id, false);

            if (is_array($repeater_value) && isset($repeater_value[$row_index])) {
                // Update status and package label of the row
                $repeater_value[$row_index][$statusACFKey] = 'approved';
                $repeater_value[$row_index][$packageLabelACFKey] = $fields['package_label']['value'];

                // Assign requestId with id field
                $requestId = $repeater_value[$row_index][$requestIdACFKey];

                // Update the entire repeater field
                update_field($repeater_field_key, $repeater_value, $order_id);

                // Trigger email notification
                do_action('send_admin_approve_return_notification', $order_id, false, $requestId);

                wp_send_json_success( array( 'message' => __( 'Update successful.', 'bricks-child' ) ) );
            }
        }
        wp_send_json_error( array( 'message' => __( 'Update failed.', 'bricks-child' ) ) );
    }



    // Handle return request
    // This function will handle the return request from the user
    // It will add a new row to the order_return_request field
    public function handle_return_request(): void {
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => __( 'User is not logged in.', 'bricks-child' ) ) );
            return;
        }
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'return_order' ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed.', 'bricks-child' ) ) );
            return;
        }
        if ( isset( $_POST['data'] ) ) {
            $data = json_decode( stripslashes( (string) $_POST['data'] ), true );
            $data = is_array( $data ) ? $data : array();
            $order_items = [];

            $requestId = uniqid();

            $items = isset( $data['items'] ) && is_array( $data['items'] ) ? $data['items'] : array();
            foreach ( $items as $item ) {
                $order_items[] = array(
                    'id'       => isset( $item['id'] ) ? absint( $item['id'] ) : 0,
                    'qty'      => isset( $item['selectedReturnQuantity'] ) ? absint( $item['selectedReturnQuantity'] ) : 0,
                    'reason'   => isset( $item['reason'] ) ? sanitize_text_field( $item['reason'] ) : '',
                    'feed_back' => isset( $item['feedback'] ) ? sanitize_textarea_field( $item['feedback'] ) : '',
                );
            }

            $order_id_for_request = isset( $data['orderId'] ) ? absint( $data['orderId'] ) : 0;
            $new_return_request   = array(
                'id'             => $requestId,
                'createAt'       => current_time( 'm/d/Y g:i a' ),
                'status'         => 'processing',
                'package_label'  => '',
                'order_items'    => $order_items,
                'email_sent'     => 0,
            );
            $rowNumber = add_row( 'order_return_request', $new_return_request, $order_id_for_request );
            if ( $rowNumber && $order_id_for_request ) {
                do_action( 'send_return_confirmation_notification', $order_id_for_request, false, $requestId );
                do_action( 'send_admin_new_return_notification', $order_id_for_request, false, $requestId );
                wp_send_json_success( array( 'message' => __( 'Return request processed.', 'bricks-child' ) ) );
            }
            wp_send_json_error( array( 'message' => __( 'Return request failed.', 'bricks-child' ) ) );
        }
        wp_send_json_error( array( 'message' => __( 'Invalid request data.', 'bricks-child' ) ) );
    }



    function handle_change_password(): void
    {
        check_ajax_referer('change-password-action', 'nonce');

        // No cache
        wc_nocache_headers();

        $user_id = get_current_user_id();

        if ( $user_id <= 0 ) {
            return;
        }

        // Current user data.
        $current_user       = get_user_by( 'id', $user_id );

        // Capture and sanitize data from the request
        $pass_cur           = $_POST['currentPassword'] ?? '';
        $pass1              = $_POST['pass1'] ?? '';
        $pass2              = $_POST['pass2'] ?? '';
        $keepSignedIn       = $_POST['keepSignedIn'] ?? '';
        $save_pass          = true;

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
            wp_send_json_error( array( 'message' => implode( ' ', $errors ) ) );
        }

        $user             = new stdClass();
        $user->ID         = $user_id;
        $user->user_pass  = $pass1;

        if ( $pass1 && $save_pass ) {
            wp_update_user( $user );
            wp_send_json_success( array( 'message' => __( 'Password changed successfully.', 'bricks-child' ) ) );
        }
        wp_send_json_error( array( 'message' => __( 'Please provide a new password.', 'woocommerce' ) ) );
    }

    function save_account_details(): void
    {

         // Check ajax nonce
         $result =  check_ajax_referer('save-account-details', 'nonce');

         // No cache
         wc_nocache_headers();

        $user_id = get_current_user_id();

        if ( $user_id <= 0 ) {
            wp_send_json_error( array( 'message' => __( 'User was not found!', 'bricks-child' ) ) );
        }

        $first_name = wc_clean( wp_unslash( $_POST['firstName'] ?? '' ) );
        $last_name = wc_clean( wp_unslash( $_POST['lastName'] ?? '' ) );
        $email     = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );

        // Current user data.
        $current_user       = get_user_by( 'id', $user_id );

        // New user data.
        $user               = new stdClass();
        $user->ID           = $user_id;
        $user->first_name   = ucfirst($first_name);
        $user->last_name    = ucfirst($last_name);

        if ( $email ) {
            if ( ! is_email( $email ) ) {
                wp_send_json_error( array( 'message' => __( 'Please provide a valid email address.', 'woocommerce' ) ) );
            } elseif ( email_exists( $email ) && $email !== $current_user->user_email ) {
                wp_send_json_error( array( 'message' => __( 'This email address is already registered.', 'woocommerce' ) ) );
            }
            $user->user_email = $email;
        }

        wp_update_user( $user );
        wp_send_json_success( array( 'message' => __( 'Account details updated successfully.', 'bricks-child' ) ) );
    }

    function save_address_book() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'save_address_nonce' ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid security token.', 'bricks-child' ) ) );
        }
        $data_raw     = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : '';
        $new_address  = is_string( $data_raw ) ? json_decode( stripslashes( $data_raw ), true ) : array();
        $new_address  = is_array( $new_address ) ? $new_address : array();

        $user_id = get_current_user_id();

        $serialized_data = maybe_serialize($new_address);

        // Set default address to customer shipping address.
        $customer = new WC_Customer($user_id);

        // Find the default address and update shipping information
        foreach ($new_address as $address) {
            if (!empty($address['default']) && $address['default'] === true) {
                // Set shipping information using WC_Customer methods
                $customer->set_shipping_first_name(sanitize_text_field($address['fname']));
                $customer->set_shipping_last_name(sanitize_text_field($address['lname']));
                $customer->set_shipping_address_1(sanitize_text_field($address['address']));
                $customer->set_shipping_address_2(sanitize_text_field($address['address2']));
                $customer->set_shipping_city(sanitize_text_field($address['city']));
                $customer->set_shipping_state(sanitize_text_field($address['region']));
                $customer->set_shipping_postcode(sanitize_text_field($address['postalCode']));
                $customer->set_shipping_country(sanitize_text_field($address['country']));
                // ... set other shipping details ...

                $customer->save();
                break; // Exit the loop once the default address is found and updated
            }
        }

        update_user_meta( $user_id, 'address_book', $serialized_data );
        wp_send_json_success( array( 'message' => __( 'Address data saved successfully.', 'bricks-child' ) ) );
    }


    function handle_signup() {
        $nonce_value = isset( $_REQUEST['signupNonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['signupNonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce_value, 'woocommerce-register' ) ) {
            wp_send_json_error( array( 'message' => wc_print_notice( __( 'Security check failed.', 'bricks-child' ), 'error', array(), true ) ) );
        }
        if ( isset( $_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['password'], $_POST['captchaToken'] ) ) {
            $email = sanitize_email($_POST['email']);
            $first_name = sanitize_text_field($_POST['firstName']);
            $last_name = sanitize_text_field($_POST['lastName']);
            $password = $_POST['password'];

            try {
                $username = '';
                $email    = wp_unslash( $_POST['email'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                $validation_error  = new WP_Error();
                $validation_error  = apply_filters( 'woocommerce_process_registration_errors', $validation_error, $username, $password, $email );
                $validation_errors = $validation_error->get_error_messages();

	            $captchaToken = $_POST['captchaToken'];
	            $captchaSecretKey = '6Lemz_YpAAAAABcCKloM1gjuRKWi-Zgj18VM-kOT';

	            $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
		            'body' => [
			            'secret' => $captchaSecretKey,
			            'response' => $captchaToken,
		            ],
	            ]);

	            if (is_wp_error($response)) {
		            // Handle the error
		            throw new Exception('The request is unacceptable, please contact admin!');
	            } else {
		            $responseBody = wp_remote_retrieve_body($response);
		            $result = json_decode($responseBody, true);
		            if ($result['success'] === false) {
			            throw new Exception('The request is unacceptable, please contact admin!');
		            }
	            }

                if ( 1 === count( $validation_errors ) ) {
                    throw new Exception( $validation_error->get_error_message() );
                } elseif ( $validation_errors ) {
                    throw new Exception($validation_errors[0]);
                }

                $new_customer = wc_create_new_customer( sanitize_email( $email ), wc_clean( $username ), $password , [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                ]);

                if ( is_wp_error( $new_customer ) ) {
                    throw new Exception( $new_customer->get_error_message() );
                }

                wc_set_customer_auth_cookie( $new_customer );

                wp_send_json_success(
                    [
                        'email' => $email,
                        'message' => wc_print_notice('Signup Successfully', 'success', [], true)
                    ]);
            } catch (Exception $e) {
                wp_send_json_error(array('message' => wc_print_notice($e->getMessage(), 'error', [], true)));
            }
        }
        wp_send_json_error( array( 'message' => wc_print_notice( __( 'Required fields are missing.', 'bricks-child' ), 'error', array(), true ) ) );
    }

    function handle_login_ajax() {
        $nonce_value = isset( $_REQUEST['woocommerceLoginNonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['woocommerceLoginNonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce_value, 'woocommerce-login' ) ) {
            wp_send_json_error( array( 'message' => wc_print_notice( __( 'Security check failed. Please refresh and try again.', 'woocommerce' ), 'error', array(), true ) ) );
        }
        if ( isset( $_POST['email'], $_POST['password'] ) ) {

            try {
                $creds = array(
                    'user_login'    => trim( wp_unslash( $_POST['email'] ) ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                    'user_password' => $_POST['password'], // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
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

                // Peform the login.
                $user = wp_signon( apply_filters( 'woocommerce_login_credentials', $creds ), is_ssl() );

                if ( is_wp_error( $user ) ) {
                    throw new Exception( $user->get_error_message() );
                }
                wp_send_json_success( array(
                    'email'   => $creds['user_login'],
                    'message' => wc_print_notice( __( 'Login Successfully', 'bricks-child' ), 'success', array(), true ),
                ) );
            } catch ( Exception $e ) {
                wp_send_json_error( array( 'message' => wc_print_notice( $e->getMessage(), 'error', array(), true ) ) );
            }
        }
        wp_send_json_error( array( 'message' => wc_print_notice( __( 'Required fields are missing.', 'woocommerce' ), 'error', array(), true ) ) );
    }

	function wishlist_add_item() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'wishlist-add-item' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'bricks-child' ) ) );
		}

		try {
			YITH_WCWL()->add();

			$wishlists = YITH_WCWL_Wishlist_Factory::get_default_wishlist();

			$return = array_map(function ($item) {
				return $item->get_product_id();
			}, $wishlists->get_items());

			wp_send_json_success( array( 'items' => $return ) );
		} catch ( YITH_WCWL_Exception $e ) {
			$return  = $e->getTextualCode();
			$message = $return === 'exists' ? __( 'The product already exists in the wishlist.', 'bricks-child' ) : $e->getMessage();
			wp_send_json_error( array( 'message' => $message, 'code' => $return ) );
		}
	}

	function get_wishlist() {
		$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'get_wishlist' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'bricks-child' ) ) );
		}
		$wishlists = YITH_WCWL_Wishlist_Factory::get_default_wishlist();

		$return = array_map( function ( $item ) {
			return $item->get_product_id();
		}, $wishlists->get_items() );
		wp_send_json_success( array( 'items' => $return ) );
	}

	function wishlist_remove_item() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'wishlist-remove-item' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'bricks-child' ) ) );
		}
		try {
			YITH_WCWL()->remove();

			/**
			 * APPLY_FILTERS: yith_wcwl_product_removed_text
			 *
			 * Filter the message when an item has been removed from the wishlist.
			 *
			 * @param string $message Message
			 *
			 * @return string
			 */

			$wishlists = YITH_WCWL_Wishlist_Factory::get_default_wishlist();

			$return = array_map(function ($item) {
				return $item->get_product_id();
			}, $wishlists->get_items());

			$message = apply_filters( 'yith_wcwl_product_removed_text', __( 'Product successfully removed.', 'yith-woocommerce-wishlist' ) );
			wp_send_json_success( array( 'items' => $return, 'message' => $message ) );
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'message' => $e->getMessage() ) );
		}
	}
}

new Theme_Ajax();