<?php

class Custom_Order_Columns {
    public function __construct() {
        add_filter('manage_edit-shop_order_columns', [$this, 'order_key_column']);
        add_action('manage_shop_order_posts_custom_column', [$this, 'cw_add_order_key_column_content'] );

        add_action( 'woocommerce_admin_order_data_after_order_details', [$this, 'display_purchase_order_number'] );
    }

    function order_key_column($columns) {
        $new_columns = array();
        foreach ($columns as $column_name => $column_info) {
            $new_columns[$column_name] = $column_info;
            if ('order_number' === $column_name) {
                $new_columns['order_key'] = __('Order Key', 'thornado');
            }
        }
        return $new_columns;
    }

    function cw_add_order_key_column_content($column) {
        global $post;
        if ( 'order_key' === $column ) {
            $order    = wc_get_order( $post->ID );
            echo $order->get_order_key();
        }
    }

    function display_purchase_order_number($order) {
        $purchase_order_number = get_post_meta($order->get_id(), 'order_purchase_number', true);
        if( !$purchase_order_number ) return;
        
        printf("<p class='form-field form-field-wide'><strong>Order Purchase Number:</strong><br/><span>%s</span></p>", $purchase_order_number);
    }
}