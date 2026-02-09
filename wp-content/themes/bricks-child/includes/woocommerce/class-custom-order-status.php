<?php

class Custom_Order_Status {
    public function __construct() {
        $this->register_order_status();
        add_filter( 'wc_order_statuses', [$this, 'add_status_to_order_status'], 100 );
    }

    function register_order_status() {
        register_post_status( 'wc-quote', array(
            'label'                     => 'Quote',
            'public'                    => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'exclude_from_search'       => false,
            'label_count'               => _n_noop( 'Quote <span class="count">(%s)</span>', 'Quote <span class="count">(%s)</span>' )
        ) );
    }

    function add_status_to_order_status($order_status) {
        $order_status['wc-quote'] = 'Quote';
        return $order_status;
    }
}