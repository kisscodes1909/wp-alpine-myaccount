<?php

class Theme_Frontend {
	private bool $plugin_owns_myaccount;

    public function __construct() {
		$this->plugin_owns_myaccount = function_exists( 'myaccount_core_is_plugin_owner' ) && myaccount_core_is_plugin_owner();
        $this->includes();
        $this->hook_includes();
    }

    public function includes() {
        require_once CHILD_DIR . '/includes/form-handler.php';
        require_once CHILD_DIR . '/includes/class-theme-assets.php';

        require_once CHILD_DIR . '/includes/plugin-compability/yith-wishlist.php';

        if(function_exists('afterShip')) {
            require_once CHILD_DIR . '/includes/plugin-compability/after-ship.php';
        }

        require_once CHILD_DIR . '/includes/woocommerce/user-registration.php';

        // Core My Account is handled by plugin only in plugin ownership mode.
        if ( ! $this->plugin_owns_myaccount ) {
            require_once CHILD_DIR . '/includes/woocommerce/my-account-page.php';
            require_once CHILD_DIR . '/includes/woocommerce/address-book.php';
        }

    }

    public function hook_includes(){
		if ( ! $this->plugin_owns_myaccount ) {
			add_action('wp_footer', [$this, 'apl_ui']);
		}
    }

	function apl_ui() {
		wc_get_template('ui/apl-toast.php');
		wc_get_template('ui/apl-popup.php');
		wc_get_template('ui/apl-loader.php');
	}
}

new Theme_Frontend();