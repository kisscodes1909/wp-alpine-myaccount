<?php

class Nonstock_Item {
    public function __construct() {
        add_action('woocommerce_product_options_stock_status', [$this, 'add_nonstock_option']);
        add_action('woocommerce_admin_process_product_object', [$this, 'disable_stock_status']);
        add_action('woocommerce_process_product_meta_simple', [$this, 'save_nonstock_value']);
        add_action('woocommerce_process_product_meta_variable', [$this, 'save_nonstock_value']);
    }

    function add_nonstock_option() {
        global $product_object;
        $nonstock_item_val = get_post_meta($product_object->get_id(), 'nonstock_item', true);

        woocommerce_wp_checkbox(
            array(
                'id'            => '_nonstock_item',
                'value'         =>  $nonstock_item_val ? 'yes' : 'no',
                'wrapper_class' => 'show_if_simple show_if_variable',
                'label'         => __( 'Nonstock Item?', 'woocommerce' ),
                //'description'   => __( 'I', 'woocommerce' ),
            )
        );
    }

    function disable_stock_status($product) {
        $is_nonstock_item = $_POST['_nonstock_item'];
        if( $is_nonstock_item == "yes" ) {
            $product->set_props([
                'stock_status' => null,
                'manage_stock' => 'no'
            ] ) ;           // update_post_meta($product->get_id(), 'nonstock_item', $is_nonstock_item);
        }
    }

    function save_nonstock_value($productId) {
        $is_nonstock_item = $_POST['_nonstock_item'];
        update_post_meta($productId, 'nonstock_item', $is_nonstock_item);
    }
}