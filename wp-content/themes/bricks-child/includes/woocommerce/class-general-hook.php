<?php

class General_Hook {
    function __construct() {
        add_filter('woocommerce_cart_shipping_method_full_label', [$this, 'display_price_in_shipping_method_label'], 100, 2);

        // Remove default actions from store front theme
        remove_action( 'woocommerce_after_single_product_summary', 'storefront_single_product_pagination', 30 );
        remove_action( 'woocommerce_before_shop_loop', 'storefront_woocommerce_pagination', 30 );
        remove_action( 'woocommerce_after_shop_loop', 'storefront_sorting_wrapper', 9 );
        remove_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10 );
        remove_action( 'woocommerce_after_shop_loop', 'woocommerce_result_count', 20 );
        remove_action( 'woocommerce_after_shop_loop', 'storefront_sorting_wrapper_close', 31 );


        add_filter( 'woocommerce_available_variation', [$this, 'add_variation_name_to_variation_data'], 100, 2 );

        // Change query name in search form
        add_filter('get_search_form', [$this, 'change_query_name']);

        //
        add_filter('woocommerce_get_availability_text', [$this, 'add_instock_availability_text'], 100, 2);

        // Add the Woof filter to archive pages
        add_action('wp_footer', [$this, 'mobile_filter']);

        

    }

    function mobile_filter() {
        if( !is_shop() && !is_product_category() ) return;
        echo wp_sprintf("<div id='mobile-filter' class='cursor-pointer site-product-filter -translate-x-full duration-500 h-full overflow-y-auto px-6 py-6 lg:hidden fixed left-0 top-0 bg-white w-3/4 sm:w-1/2 z-[100] shadow-sm invisible opacity-0 transition-all'>%s %s</div>", 
            '<span id="close-mobile-filter" class="p-0 absolute right-1 top-1"> 
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
             </span>',
             do_shortcode('[woof]'),

        );
    }

    
    /**
     * Add an availability text for In-Stock status
     *
     * @param string $availability
     * @param WC_Product $product
     * @return string $availability
     */
    function add_instock_availability_text(string $availability, WC_Product $product) {
        if( $product->is_in_stock() ) {
            $availability = wc_format_stock_for_display( $product );
        }
        return $availability;
    }

    function change_query_name($form) {
        $form = str_replace([
            'name="s"',
            wp_sprintf('action="%s"', esc_url( home_url( '/' ) ))
        ], 
        [
            'name="woof_text"',
            wp_sprintf('action="%sshop/?%s"', esc_url( home_url( '/' ) ), 'swoof=1')
        ], 
        $form);
    
        return $form;
    }

    function display_price_in_shipping_method_label($label, $method) {
       // print_r($method) . "<br>";
        if( '0.00' == $method->cost ) {
            $label = $method->get_label() . ': ' . wc_price(0);
        }
        return $label;
    }

    function add_variation_name_to_variation_data( $variation, $variationObj ) {
        $variation['name'] = get_the_title($variation[ 'variation_id' ]);
        //$variation['availability'] = $variationObj->get_availability();

        // var_dump($variationObj->child_is_in_stock());

        return $variation;
     }

}