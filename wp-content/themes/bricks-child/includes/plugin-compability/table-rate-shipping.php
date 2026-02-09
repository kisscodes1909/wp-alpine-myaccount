<?php

if( !class_exists('BE_Table_Rate_Calculate') ) return;

class Table_Rate_Shipping {

    protected $bukyItemIds = [
        3267 // HW1 shipping class
    ];

    function __construct() {
        add_filter('betrs_shipping_method_rate', [$this, 'adjust_subtotal_behavior'], 100, 2);
    }


    function adjust_subtotal_behavior($rates, $shipping_method){

        // $shipping_method = $be_table_rate->get_method();
        $nonbulky_free_shipping = $shipping_method->get_instance_option( 'nonbulky_free_shipping' );
        $order_subtotal = $shipping_method->get_instance_option( 'order_subtotal' );

        //debug($rates);


        if( $nonbulky_free_shipping !== 'yes') return $rates;

        // Get Cart total 

        $cartSubTotal = WC()->cart->get_subtotal();

        // Match the order subtotal condition
        if($cartSubTotal > $order_subtotal) {
            foreach($rates as $shipping_class_id => &$dataRates) {
                if( !in_array($shipping_class_id, $this->bukyItemIds) ){
                    foreach($dataRates as &$rate) {
                        $rate['cost'] = 0;
                    }
                }
            }
        }

        // debug($rates);

        //
        
        return $rates;
        //debug($items);
    }
}

