<?php

class Custom_Order_Status {
    public function __construct() {
        $this->register_order_status();
        add_filter( 'wc_order_statuses', [$this, 'add_status_to_order_status'], 100 );
        add_filter( 'wc_order_statuses', [$this, 'rename_woocommerce_status_label'], 100 );
    }

    function rename_woocommerce_status_label($order_statuses) {
        if ( isset( $order_statuses['wc-processing'] ) ) {
            $order_statuses['wc-processing'] = 'Order Placed';
        }
        
        if ( isset( $order_statuses['wc-completed'] ) ) {
            $order_statuses['wc-completed'] = 'Delivered';
        }
        return $order_statuses;
    }

    function register_order_status() {
        register_post_status( 'wc-shipped', array(
            'label'                     => 'Shipped',
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => false,
            'label_count'               => _n_noop( 'Shipped <span class="count">(%s)</span>', 'Shipped <span class="count">(%s)</span>' )
        ));

    }

    function add_status_to_order_status($order_status) {
        $order_status['wc-shipped'] = 'Shipped';
        return $order_status;
    }

    function record_time($order_id, $old_status, $new_status, $order) {
        // Check if the new status is 'processing'
        if ($new_status == 'processing' || $new_status == 'shipped') {

            // Get the current time
            $dt = new WC_DateTime();

            // Save the current time as order meta
            update_post_meta($order_id, "{$new_status}At", $dt->getTimestamp());
        }
    }
}

return new Custom_Order_Status();