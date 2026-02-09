<?php

/**
 * Add form field to 'Additional Options' section in the Table of Rates
 *
 * @return string
 */
function betrs_add_settings_field( $settings ) {
    // array of shipping provider codes

    $settings['other']['settings']['nonbulky_free_shipping'] = array(
        'title'             => __( 'Nonbulky Free Shipping', 'be-table-ship' ),
        'type'              => 'checkbox',
        'default'           => '',
        'desc'              => 'Free shipping for nonbulky item class',

    );

    $settings['other']['settings']['order_subtotal'] = array(
        'title'             => __( 'Order Subtotal', 'be-table-ship' ),
        'type'              => 'number',
        'class'             => 'input-text regular-input',
        'default'           => 0,
        'description'              => 'only enter data if you have ticked the Nonbulky Free Shipping field',
    );



    return $settings;
}
add_filter( 'woocommerce_shipping_instance_form_fields_betrs_shipping', 'betrs_add_settings_field', 10, 1 );