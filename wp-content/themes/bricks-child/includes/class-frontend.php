<?php

class Theme_Frontend {
    public function __construct() {
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
        require_once CHILD_DIR . '/includes/woocommerce/my-account-page.php';
        require_once CHILD_DIR . '/includes/woocommerce/address-book.php';

    }

    public function hook_includes(){
		add_action('wp_footer', [$this, 'apl_ui']);
    }

	function apl_ui() {
		wc_get_template('ui/apl-toast.php');
		wc_get_template('ui/apl-popup.php');
		wc_get_template('ui/apl-loader.php');
	}
}

new Theme_Frontend();