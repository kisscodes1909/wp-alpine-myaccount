<?php

class Product_Extra_Fields {

    public $custom_fields = [
        'upc_ean',
        'handling_time',
        'origin_country',
        'catch_shipping_cat',
        'shipping_option_id',
        'manufacture_model_no',
        'ebay_product_title'
    ];

    function __construct() {
        add_filter('woocommerce_product_data_tabs', [$this, 'the_extra_tab'] );
        add_filter('woocommerce_product_data_panels', [$this, 'the_extra_fields']);

        add_action('woocommerce_process_product_meta_simple', [$this, 'save_data']  );
        //add_action('woocommerce_process_product_meta_variable', [$this, 'save_data']  );


        add_action('woocommerce_product_after_variable_attributes', [$this, 'the_variable_extra_field'], 10, 3);
        add_action('woocommerce_save_product_variation', [$this, 'save_custom_field_variations'], 10, 2 );

    }

    function the_variable_extra_field($loop, $variation_data, $variation) {
        ?>
            <div id='the_extra_field'>
                <?php $this->the_extra_field($variation->ID, $loop); ?>
            </div>
        <?php
 
    }

    function the_extra_field($postId, $loop = null) {

        foreach($this->custom_fields as $k => $field) {
            woocommerce_wp_text_input( array(
                'id' => $field . (is_numeric($loop) ? "[{$loop}]" : ''),
                'class' => 'short',
                'label' => ucwords(str_replace('_',' ',$field)),
                'value' => get_post_meta( $postId, $field, true ), 
                'wrapper_class' => 'form-row ' . ($k%2 === 0 ? 'form-row-first' : 'form-row-last')
            ));
        }
        

        // woocommerce_wp_text_input( array(
        //     'id' => 'upc_ean[' . $loop . ']',
        //     'class' => 'short',
        //     'label' => 'UPC/EAN',
        //     'value' => get_post_meta( $postId, 'upc_ean', true ),
        //     'wrapper_class' => 'form-row form-row-first form-field'
        // ));

        // woocommerce_wp_text_input( array(
        //     'id' => 'handling_time[' . $loop . ']',
        //     'class' => 'short',
        //     'label' => 'Handling Time',
        //     'value' => get_post_meta( $postId, 'handling_time', true ),
        //     'wrapper_class' => 'form-row form-row-last form-field'
        // ));

        // woocommerce_wp_text_input( array(
        //     'id' => 'origin_country[' . $loop . ']',
        //     'class' => 'short',
        //     'label' => 'Origin Country',
        //     'value' => get_post_meta( $postId, 'origin_country', true ),
        //     'wrapper_class' => 'form-row form-row-first form-field'
        // ));

        // woocommerce_wp_text_input( array(
        //     'id' => 'catch_shipping_cat[' . $loop . ']',
        //     'class' => 'short',
        //     'label' => 'Catch Shipping Category',
        //     'value' => get_post_meta( $postId, 'origin_country', true ),
        //     'wrapper_class' => 'form-row form-row-last form-field'
        // ));

        // woocommerce_wp_text_input( array(
        //     'id' => 'shipping_option_id[' . $loop . ']',
        //     'class' => 'short',
        //     'label' => 'Mydeal Shipping Option ID',
        //     'value' => get_post_meta( $postIdD, 'origin_country', true ),
        //     'wrapper_class' => 'form-row form-row-first form-field'
        // ));

        // woocommerce_wp_text_input( array(
        //     'id' => 'manufacture_model_no[' . $loop . ']',
        //     'class' => 'short',
        //     'label' => 'Manufacture Model No',
        //     'value' => get_post_meta( $postId, 'origin_country', true ),
        //     'wrapper_class' => 'form-row form-row-last form-field'
        // ));

        // woocommerce_wp_text_input( array(
        //     'id' => 'ebay_product_title[' . $loop . ']',
        //     'class' => 'short',
        //     'label' => 'Ebay Product Title',
        //     'value' => get_post_meta( $postId, 'origin_country', true ),
        //     'wrapper_class' => 'form-row form-row-first form-field'
        // ));
    }

    function save_custom_field_variations($variation_id, $i) {

        foreach($this->custom_fields as $field) {
            if(isset($_POST[$field][$i])) {
                update_post_meta( $variation_id, $field, esc_attr($_POST[$field][$i]));
            }
        }
    }
    

    function the_extra_tab($tabs) {
        $tabs['extra_fields'] = array(
            'label'    => __( 'Extra field', 'woocommerce' ),
            'target'   => 'the_extra_tab', // same as the ID in the next function.
            'class'    => array( 'show_if_simple' ),
            'priority' => 99,
        );
        return $tabs;
    }

    function the_extra_fields() {
        global $post;

        ?>
            <div id='the_extra_tab' class='panel wc-metaboxes-wrapper' style="padding: 0 10px; float:right; box-sizing: border-box;"><?php
                ?>
                    <?php $this->the_extra_field($post->ID); ?>
            </div>
        <?php
    }

    function save_data($post_id) {
        foreach($this->custom_fields as $field) {
            if(isset($_POST[$field])) {
                update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
            }
        }

        //debug($_POST);die;
    }
}