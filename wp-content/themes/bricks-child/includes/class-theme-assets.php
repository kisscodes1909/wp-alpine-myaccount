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
        if ('alpine-bundle' === $handle) {
            return str_replace(' src', ' defer="defer" src', $tag);
        }

	    if ('gg-recaptcha' === $handle) {
		    return str_replace(' src', ' async defer src', $tag);
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
	    wp_register_script('gg-recaptcha', "https://www.google.com/recaptcha/api.js?render=" . CAPTCHA_SITE_KEY);
	    wp_register_script('wishlist', CHILD_URL . '/assets/js/wishlist.js', [], filemtime(CHILD_DIR . '/assets/js/wishlist.js'));

	    wp_enqueue_script('wishlist');

	    $plugin_owns_myaccount_assets = function_exists( 'myaccount_core_is_plugin_owner' ) && myaccount_core_is_plugin_owner() && is_account_page();

	    if ( ! $plugin_owns_myaccount_assets ) {
	        // Alpine bundle (includes Alpine.js + Yup + theme stores/components/directives)
	        $alpine_bundle = CHILD_DIR . '/assets/js/alpine.bundle.js';
	        if (file_exists($alpine_bundle)) {
	            wp_enqueue_script(
	                'alpine-bundle',
	                CHILD_URL . '/assets/js/alpine.bundle.js',
	                [],
	                filemtime($alpine_bundle),
	                true
	            );
	        }
	    }

		wp_localize_script('wishlist', 'wishlistData', [
			'addItemNonce' => wp_create_nonce('wishlist-add-item'),
			'removeItemNonce' => wp_create_nonce('wishlist-remove-item'),
			'isLoggedIn' => is_user_logged_in(),
		]);
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
