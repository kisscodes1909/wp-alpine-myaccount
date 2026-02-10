<?php

class Theme_Ajax {
    public function __construct()
    {
        add_action('wp_ajax_save-address', [$this, 'save_address_book']);
        add_action('wp_ajax_save_account_details', [$this,'save_account_details']);
        add_action('wp_ajax_change_password', [$this, 'handle_change_password']);
        add_action('wp_ajax_handle_return_request', [$this, 'handle_return_request']);
        add_action('wp_ajax_nopriv_handle_return_request', [$this, 'handle_return_request']);
        add_action('wp_ajax_approve_return', [$this, 'handle_approve_return_ajax']);
        add_action('wp_ajax_handle_login', [$this, 'handle_login_ajax']);
        add_action('wp_ajax_nopriv_handle_login', [$this, 'handle_login_ajax']);
        add_action('wp_ajax_handle_signup', [$this, 'handle_signup']);
        add_action('wp_ajax_nopriv_handle_signup', [$this, 'handle_signup']);

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
		die('ok');
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
        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
        $row_index = isset($_POST['row_index']) ? intval($_POST['row_index']) : -1;
        $fields = isset($_POST['fields']) ? json_decode(stripslashes($_POST['fields']), true) : [];

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

                wp_send_json_success('Update successful');
            }
        }

        wp_send_json_error('Update failed');
    }



    // Handle return request
    // This function will handle the return request from the user
    // It will add a new row to the order_return_request field
    public function handle_return_request(): void {
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'User is not logged in'), 403);
            return;
        }
        $nonce = $_POST['nonce'];

        if (!wp_verify_nonce($nonce, 'return_order')) {
            wp_send_json_error(array('message' => 'Nonce verification failed'), 403);
            return;
        }

        if (isset($_POST['data'])) {
            $data = json_decode(stripslashes($_POST['data']), true);
            $order_items = [];

            $requestId = uniqid();

            foreach ($data['items'] as $item) {
                $order_items[] = array(
                    'id' => $item['id'],
                    'qty' => $item['selectedReturnQuantity'],
                    'reason' => $item['reason'],
                    'feed_back' => $item['feedback']
                );
            }

            $new_return_request = array(
                'id' => $requestId,
                'createAt' => current_time('m/d/Y g:i a'), // Đặt ngày giờ hiện tại
                'status' => 'processing', // Đặt trạng thái processing
                'package_label' => '',
                'order_items'   => $order_items,
                'email_sent' => 0 // Đánh dấu email đã được gửi
            );


            $rowNumber = add_row('order_return_request', $new_return_request, $data['orderId']); // Return the row number if successful

            if($rowNumber) {
                do_action('send_return_confirmation_notification', $data['orderId'], false, $requestId);
                do_action('send_admin_new_return_notification', $data['orderId'], false, $requestId);
                wp_send_json_success('Return request processed');
                // Send email here if needed
            } else {
                wp_send_json_error('Return request failed');
            }
        }

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
            wp_send_json_error( implode( ' ', $errors ) );
        }

        // New user data.
        $user = new stdClass();
        $user->ID = $user_id;

        if ( $pass1 && $save_pass ) {

            $user->user_pass = $pass1;
            // You might want to use wp_update_user here to save the new password
            $result =  wp_update_user( $user );
            wp_send_json_success('Password changed successfully');
        }

        wp_die();
    }

    function save_account_details(): void
    {

         // Check ajax nonce
         $result =  check_ajax_referer('save-account-details', 'nonce');

         // No cache
         wc_nocache_headers();

        $user_id = get_current_user_id();

        if ( $user_id <= 0 ) {
            wp_send_json_error('User was not found!');
        }

        // Assuming you have proper validation and sanitization here
        $first_name = wc_clean(wp_unslash($_POST['firstName']));
        $last_name = wc_clean(wp_unslash($_POST['lastName']));
        $email = sanitize_email(wp_unslash($_POST['email']));

        // Current user data.
        $current_user       = get_user_by( 'id', $user_id );

        // New user data.
        $user               = new stdClass();
        $user->ID           = $user_id;
        $user->first_name   = ucfirst($first_name);
        $user->last_name    = ucfirst($last_name);

        if ( $email ) {
            if ( ! is_email( $email ) ) {
                wp_send_json_error(__( 'Please provide a valid email address.', 'woocommerce' ));
            } elseif ( email_exists( $email ) && $email !== $current_user->user_email ) {
                wp_send_json_error(__( 'This email address is already registered.', 'woocommerce' ));
            }
            $user->user_email = $email;
        }

        wp_update_user( $user );

        wp_send_json_success('Account details updated successfully.');
    }

    function save_address_book() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'save_address_nonce')) {
            wp_send_json_error('Invalid security token');
            exit;
        }

        $new_address = json_decode(stripslashes($_POST['data']), true);

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

        update_user_meta($user_id, 'address_book', $serialized_data);

        wp_send_json_success('Address data saved successfully');
        exit;

    }


    function handle_signup() {

        $nonce_value = $_REQUEST['signupNonce'];  // Assuming you send this in your AJAX request

        if (isset($_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['password'], $_POST['captchaToken']) && wp_verify_nonce( $nonce_value, 'woocommerce-register' ) ) {
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
        } else {
            wp_send_json_error(['message' => wc_print_notice('Required fields are missing.', 'error', [], true)]);
        }
        exit;
    }

    function handle_login_ajax() {
        $nonce_value = $_REQUEST['woocommerceLoginNonce']; // @codingStandardsIgnoreLine.
        $valid_nonce = wp_verify_nonce( $nonce_value, 'woocommerce-login' );

        if ( isset($_POST['email'], $_POST['password'] )) {

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
                } else {
                      wp_send_json_success(
                          [
                              'email' => $email,
                              'message' => wc_print_notice('Login Successfully', 'success', [], true)
                          ]);

                }
            } catch ( Exception $e ) {
                    wp_send_json_error(array('message' => wc_print_notice($e->getMessage(), 'error', [], true)));
            }
        }
    }

	function wishlist_add_item() {
		$nonce = $_POST['nonce'];

		if (!wp_verify_nonce($nonce, 'wishlist-add-item')) {
			wp_send_json_error(array('message' => 'Nonce verification failed'), 403);
			return;
		}

		try {
			YITH_WCWL()->add();

			$wishlists = YITH_WCWL_Wishlist_Factory::get_default_wishlist();

			$return = array_map(function ($item) {
				return $item->get_product_id();
			}, $wishlists->get_items());

			wp_send_json_success($return);


		} catch ( YITH_WCWL_Exception $e ) {
			$return = $e->getTextualCode();

//			/**
//			 * APPLY_FILTERS: yith_wcwl_error_adding_to_wishlist_message
//			 *
//			 * Filter the error message shown when adding an item to the wishlist.
//			 *
//			 * @param string $message Message
//			 *
//			 * @return string
//			 */
//			$message = apply_filters( 'yith_wcwl_error_adding_to_wishlist_message', $e->getMessage() );

			if($return === 'exists') {
				$message = 'The product was exist';
			}

			wp_send_json_error(['message' => $message, 'code' => $return]);
		}

	}

	function get_wishlist() {
		$wishlists = YITH_WCWL_Wishlist_Factory::get_default_wishlist();

		$return = array_map(function ($item) {
			return $item->get_product_id();
		}, $wishlists->get_items());

		wp_send_json_success($return);
	}

	function wishlist_remove_item() {
		$nonce = $_POST['nonce'];

		if (!wp_verify_nonce($nonce, 'wishlist-remove-item')) {
			wp_send_json_error(array('message' => 'Nonce verification failed'), 403);
			return;
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

			wp_send_json_success($return);

		} catch ( Exception $e ) {
			$message = $e->getMessage();

			wp_send_json_error($message);
		}
	}

}

new Theme_Ajax();