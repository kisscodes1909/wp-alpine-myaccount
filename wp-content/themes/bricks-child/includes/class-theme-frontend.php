<?php

class Theme_Frontend {
    public function __construct() {
        $this->includes();
        $this->hook_includes();
    }

    public function includes() {
        require_once CHILD_DIR . '/includes/form-handler.php';
        require_once CHILD_DIR . '/includes/theme-assets.php';

        require_once CHILD_DIR . '/includes/plugin-compability/yith-wishlist.php';

        if(function_exists('afterShip')) {
            require_once CHILD_DIR . '/includes/plugin-compability/after-ship.php';
        }

        require_once CHILD_DIR . '/includes/woocommerce/user-registration.php';
        require_once CHILD_DIR . '/includes/woocommerce/my-account-page.php';
        require_once CHILD_DIR . '/includes/woocommerce/address-book.php';

    }

    public function hook_includes(){
//        add_action('woocommerce_init', [$this, 'init_credit_exchanger']);
    }

    function init_credit_exchanger() {
        //$credit_exchanger = Credit_Exchanger::getInstance();
        //$credit_exchanger->set_checkout_credit_discount('400');
        //$credit_exchanger->remove_checkout_credit_discount();
    }
}

new Theme_Frontend();