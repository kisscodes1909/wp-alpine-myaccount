<?php

class My_Account_Page {
    public function __construct() {
        add_filter( 'woocommerce_account_menu_items', array( $this, 'remove_account_dashboard_tabs' ) );
        add_filter( 'woocommerce_account_menu_items', array( $this, 'remove_account_logout_button' ) );
        add_filter( 'woocommerce_login_redirect', array( $this, 'redirect_after_login' ), 10, 2 );
        add_filter( 'woocommerce_account_menu_items', array( $this, 'rename_order_navigation_title' ) );

        // Added new wishlist endpoint
        $this->add_rewrite_wishlist_endpoint();
        add_filter('woocommerce_account_wishlist_endpoint', array($this, 'wishlist_endpoint_content'));
        add_filter('woocommerce_get_query_vars', [$this, 'add_wishlist_queryvar']);
        add_filter('woocommerce_account_menu_items', [$this, 'added_wishlist_menu_item']);


        // Added new cancel order endpoint
        $this->add_rewrite_returnorder_endpoint();
        add_filter('woocommerce_get_query_vars', [$this, 'add_returnorder_queryvar']);
        add_filter('woocommerce_account_return-order_endpoint', array($this, 'returncontent_endpoint_content'));

        // Added new address endpoint
        add_filter('woocommerce_account_menu_items', [$this, 'reorder_menu_items'], 100);

        // Login and Registration redirect to home page
        add_filter('woocommerce_login_redirect', array($this, 'redirect_to_home'), 10, 2);
        add_filter('woocommerce_registration_redirect', array($this, 'redirect_to_home'), 10, 1);

        // Redirect account dashboard to order history
        //add_action('template_redirect', [$this, 'redirect_dashboard_to_order_list']);

        // Global My Account template class (fashion/a/b/c)
        add_filter('body_class', [$this, 'add_my_account_template_class']);


        // Limit 5 orders per page
        add_filter('woocommerce_my_account_my_orders_query', [$this, 'limit_5_orders']);

        add_action('wp_footer', [$this, 'popup_container'], 5);
    }

    function popup_container(): void
    {
        ?>
        <div x-data id="popup-container" class="z-[999] fixed inset-0 overflow-y-auto flex items-center justify-center p-4" x-show="$store.popup.open" x-cloak aria-hidden="true"></div>
        <div x-data id='toast-container' class="z-[1000]"></div>
        <div x-data id='loader-container' class="z-[1001]"></div>
        <?php
    }

    function limit_5_orders($args) {
        $args['limit'] = 2;
        return $args;
    }

    function redirect_dashboard_to_order_list(): void
    {
        if (is_account_page() && !is_wc_endpoint_url() && !bricks_is_builder()) {
            // Redirect to the Orders endpoint in the My Account area
            wp_redirect(wc_get_endpoint_url('orders'));
            exit;
        }
    }

    function redirect_to_home(): ?string
    {
        return home_url('/');
    }

    function add_my_account_template_class($classes): array
    {
        if (!is_account_page() || bricks_is_builder()) {
            return $classes;
        }

        $template = get_option('myaccount_template_style', 'fashion');
        $allowed_templates = ['fashion', 'a', 'b', 'c'];

        if (!in_array($template, $allowed_templates, true)) {
            $template = 'fashion';
        }

        $classes[] = 'myaccount-template-' . sanitize_html_class($template);

        return $classes;
    }

    function reorder_menu_items($items): array
    {
//        var_dump($items);
        $reorder['orders'] = $items['orders'];
        $reorder['wishlist'] = $items['wishlist'];
        $reorder['edit-account'] = $items['edit-account'];
        $reorder['address'] = $items['address'];
        $reorder['payment-methods'] = $items['payment-methods'];
        return $reorder;
    }

    function added_wishlist_menu_item($items) {
        $items['wishlist'] = 'Wishlist';
        return $items;
    }

    function add_wishlist_page_title($title, $endpoint, $action): string
    {
        return 'Wishlist';
    }

    public function add_wishlist_queryvar($vars) {
        $vars['wishlist'] = 'wishlist';
        return $vars;
    }

    public function add_returnorder_queryvar($vars) {
        $vars['return-order'] = 'return-order';
        return $vars;
    }


    public function add_rewrite_wishlist_endpoint() {
        add_rewrite_endpoint('wishlist', EP_ROOT | EP_PAGES);
        flush_rewrite_rules(); // This might be needed when adding endpoints programmatically
    }

    public function add_rewrite_returnorder_endpoint() {
        add_rewrite_endpoint('return-order', EP_ROOT | EP_PAGES);
        flush_rewrite_rules(); // This might be needed when adding endpoints programmatically
    }

    public function wishlist_endpoint_content($content) {
        global $wp;
        wc_get_template('/myaccount/wishlist.php');

    }

    public function returncontent_endpoint_content() {
        $order_id = get_query_var('return-order', 0);



        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Assume you have the order ID passed from the form in a field named 'order_id'
//            $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
            $reason = isset($_POST['reason']) ? sanitize_text_field($_POST['reason']) : '';
            $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

            $errors = new WP_Error();

            if ( ! isset( $_POST['return-order-nonce'] ) || ! wp_verify_nonce( $_POST['return-order-nonce'], 'return-order' ) ) {
                // Handle the case where the nonce is invalid
                wc_add_notice( __( 'Error: Your form submission could not be verified. Please try again.', 'textdomain' ), 'error' );
            }

            // Check if order ID is valid and the current user owns this order
            $order = wc_get_order($order_id);

            if ($order) {
                // Check if the current user is the owner of the order
                if ($order->get_customer_id() !== get_current_user_id()) {
                    $errors->add('order_owner_error', __('You do not have permission to cancel this order.', 'textdomain'));
                }

                // Check if the order status is not already cancelled
                if ($order->has_status('cancelled')) {
                    $errors->add('order_status_error', __('This order is already cancelled.', 'textdomain'));
                }
            } else {
                $errors->add('order_not_found', __('The order does not exist.', 'textdomain'));
            }

            // Perform other validations
            if (empty($reason)) {
                $errors->add('reason_error', __('Please select a reason for cancellation.', 'textdomain'));
            }

            if (empty($message)) {
                $errors->add('message_error', __('Please enter a message.', 'textdomain'));
            }

            // If there are errors, handle them accordingly
            if (!empty($errors->get_error_codes())) {
                foreach ($errors->get_error_messages() as $error) {
                    wc_add_notice($error, 'error');
                }
            } else {
                // Process the cancellation here

                // Update the order status to 'cancelled'
                $order->update_status('cancelled', 'Order cancelled by user: ' . $message);

                // Redirect or notify the user of successful submission
                wc_add_notice(__('Your cancellation request has been submitted.', 'textdomain'), 'success');
            }
        }

        wc_get_template('order/order-return-form.php', [
            'order' => wc_get_order($order_id),
            'shipment' => aftership_get_shipment($order_id),
        ]);

        wp_enqueue_script('accounting');

    }

    public function remove_account_logout_button($items) {
        unset($items['customer-logout']);
        return $items;
    }

    public function remove_account_dashboard_tabs( $items ) {
        // Example: Remove the 'Dashboard' tab
        unset( $items['dashboard'] );
        return $items;
    }

    public function redirect_after_login( $redirect, $user ) {
        // Redirect customers to the 'Order History' page after login
        return wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) );
    }

    public function rename_order_navigation_title( $items ) {

        $items['orders'] = 'Order History'; // Change 'Order History' to your desired title
        $items['payment-methods'] = 'Saved Payments';
        $items['edit-account'] = 'My Info';
        return $items;
    }
}

return new My_Account_Page();