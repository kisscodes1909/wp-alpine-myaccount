<?php

class Theme_Ajax {
    public function __construct()
    {
        add_action('wp_ajax_save-address', [$this, 'save_address_book']);
        add_action('wp_ajax_save_account_details', [$this,'save_account_details']);
        add_action('wp_ajax_change_password', [$this, 'handle_change_password']);
        add_action('wp_ajax_handle_return_request', [$this, 'handle_return_request']);
        add_action('wp_ajax_nopriv_handle_return_request', [$this, 'handle_return_request']);
    }

    function handle_return_request(): void
    {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'return_order' ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed.', 'bricks-child' ) ) );
        }
        if ( isset( $_POST['data'] ) ) {
            $data = json_decode( stripslashes( (string) $_POST['data'] ), true );
            $data = is_array( $data ) ? $data : array();
            $order_items = array();
            $items = isset( $data['items'] ) && is_array( $data['items'] ) ? $data['items'] : array();
            foreach ( $items as $item ) {
                $order_items[] = array(
                    'id'        => isset( $item['id'] ) ? absint( $item['id'] ) : 0,
                    'qty'       => isset( $item['selectedReturnQuantity'] ) ? absint( $item['selectedReturnQuantity'] ) : 0,
                    'reason'    => isset( $item['reason'] ) ? sanitize_text_field( $item['reason'] ) : '',
                    'feed_back' => isset( $item['feedback'] ) ? sanitize_textarea_field( $item['feedback'] ) : '',
                );
            }
            $order_id = isset( $data['orderId'] ) ? absint( $data['orderId'] ) : 0;
            $new_return_request = array(
                'package_label' => '',
                'order_items'   => $order_items,
            );
            add_row( 'order_return_request', $new_return_request, $order_id );
        }
        wp_send_json_success( array( 'message' => __( 'Return request processed.', 'bricks-child' ) ) );
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

        $user = new stdClass();
        $user->ID = $user_id;
        $user->user_pass = $pass1;

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
        $last_name  = wc_clean( wp_unslash( $_POST['lastName'] ?? '' ) );
        $email      = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );

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
}

new Theme_Ajax();