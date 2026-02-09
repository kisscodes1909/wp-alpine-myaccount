<?php
class Single_Product {
    function __construct() {
        add_action('woocommerce_after_add_to_cart_button', [$this, 'enquiry_button']);
        add_action('wp_footer', [$this, 'enquiry_form_modal']);
        add_action('wp_footer', [$this, 'bulk_add_to_cart_dialog']);
    }

    function enquiry_button() {
        if( !is_product() ) return;
        wc_get_template('single-product/enquiry-button.php');
    }

    function enquiry_form_modal() {
        if( !is_product() ) return;
        wc_get_template('single-product/enquiry-form.php');
    }

    function bulk_add_to_cart_dialog() {
        if( !is_product() ) return;
        wc_get_template('single-product/product-variation-dialog.php');
    }
}