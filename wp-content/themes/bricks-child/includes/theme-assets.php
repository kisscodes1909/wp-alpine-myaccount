<?php

class Theme_Assets {
    public function __construct()
    {
        //checkoutwc compability
//        add_action('cfw_enqueue_scripts', [$this, 'enqueue_scripts']);
//        add_action('cfw_blocked_script_handles', [$this,'unblock_checkoutjs'], 100);
//        add_action('cfw_enqueue_scripts', [$this, 'enqueue_styles']);
//        add_action('cfw_blocked_style_handles', [$this, 'unblock_checkoutStyle'], 100);

        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        add_filter('script_loader_tag', [$this, 'add_defer_attribute'], 10, 2);
    }

    function add_defer_attribute($tag, $handle) {
//        if ('alpinejs' === $handle) {
//            return str_replace(' src', ' defer="defer" src', $tag);
//        }

        if ('yup-js' === $handle) {
            return str_replace(' src', ' defer="defer" src', $tag);
        }

        return $tag;
    }


    function unblock_checkoutjs($scripts) {
        $unblockScriptKey = array_search('theme-checkout', $scripts);

        if( $unblockScriptKey !== false ) {
            unset($scripts[$unblockScriptKey]);
        }
        return $scripts;
    }

    function unblock_checkoutStyle($styles) {
        $unblockStyleKey = array_search('theme-checkout-style', $styles);

        if( $unblockStyleKey !== false ) {
            unset($styles[$unblockStyleKey]);
        }

        return $styles;
    }

    function enqueue_scripts(): void
    {
        if (!wp_script_is('alpinejs', 'registered')) {
            // Register Alpine.js
            wp_register_script('alpinejs', CHILD_URL . '/assets/js/alpinejs.min.js', array('underscore'), '3.0.0', true);
            wp_register_script('yup-js', CHILD_URL . '/assets/build/js/yup.js', array(), '1.3.3', true);
        }

        if(is_account_page()) {
            wp_enqueue_script('alpinejs');
            wp_enqueue_script('yup-js');
        }
    }
    function enqueue_styles() {
//        if (cfw_is_checkout()) {
//            wp_register_style( 'theme-checkout-style', CHILD_URL . '/css/checkout.css');
//
//            wp_enqueue_style( 'theme-checkout-style' );
//        }

        if ( ! bricks_is_builder_main() ) {
            wp_enqueue_style('bricks-child', get_stylesheet_uri(), ['bricks-frontend'], filemtime(get_stylesheet_directory() . '/style.css'));
            wp_enqueue_style('app-css', CHILD_URL . '/assets/css/app.css', ['bricks-woocommerce'], filemtime(CHILD_DIR . '/assets/css/app.css'));
        }
    }
}


new Theme_Assets();
