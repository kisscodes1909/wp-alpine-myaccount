<?php

class Bootstrap_Theme
{
    /**
     * The actual singleton's instance almost always resides inside a static
     * field. In this case, the static field is an array, where each subclass of
     * the Singleton stores its own instance.
     */
    private static $instances = [];

    /**
     * Singleton's constructor should not be public. However, it can't be
     * private either if we want to allow subclassing.
     */
    protected function __construct() {
        $this->define_constants();
        $this->include_files();
        $this->init_hooks();
    }

    private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

    function define_constants() {

    }

    /**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

    /**
     * Cloning and unserialization are not permitted for singletons.
     */
    protected function __clone() { }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
    
    
    private function include_files() {

//        require_once CHILD_DIR . '/vendor/autoload.php';


        require_once CHILD_DIR . '/includes/core-functions.php';

        // Core Class
        require_once CHILD_DIR . '/includes/class-singleton.php';

        require_once CHILD_DIR . '/includes/woocommerce/custom-order-status.php';
        require_once CHILD_DIR . '/includes/class-theme-installer.php';

        if( $this->is_request('admin') ) {
//            require_once CHILD_DIR . '/includes/admin/class-theme-admin.php';
        }

        if( $this->is_request('frontend') ) {
            require_once CHILD_DIR . '/includes/services/order-cancellation.php';
            require_once CHILD_DIR . '/includes/class-theme-frontend.php';
        }

        if( $this->is_request('ajax') ) {
            require_once CHILD_DIR . '/includes/class-theme-ajax.php';
        }


//        if ( defined( 'WP_CLI' ) && WP_CLI ) {
//            require_once CHILD_DIR . '/includes/class-theme-cli.php';
//            WP_CLI::add_command( 'finance valid_normal_order', ['Finance_Tool', 'valid_normal_order']);
//            WP_CLI::add_command( 'finance exchanger', ['Finance_Tool', 'exchange_to_coupon']);
//            WP_CLI::add_command( 'finance upgrade_transactions', ['Finance_Tool', 'upgrade_transaction_data']);
//        }


    }

    private function init_hooks(){
        add_action('woocommerce_init', function() {
            add_filter('woocommerce_email_classes', 'add_return_confirmation_email_class');
        });

        function add_return_confirmation_email_class($email_classes) {
            require_once CHILD_DIR . '/includes/woocommerce/emails/return-confirmation.php';
            $email_classes['WC_Return_Confirmation'] = new WC_Return_Confirmation();
            return $email_classes;
        }
    }

    function load_api() {
    }

    /**
     * The method you use to get the Singleton's instance.
     */
    public static function getInstance()
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            // Note that here we use the "static" keyword instead of the actual
            // class name. In this context, the "static" keyword means "the name
            // of the current class". That detail is important because when the
            // method is called on the subclass, we want an instance of that
            // subclass to be created here.

            self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }
}

return Bootstrap_Theme::getInstance();