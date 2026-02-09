<?php

class Initializer
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
    protected function __construct()
    {
        // TODO: Analytic this file.
        $this->define_constants();
        $this->include_files();
        $this->init_hooks();

    }

    private function is_request( $type )
    {
        switch ( $type ) {
        case 'admin':
            return is_admin();
        case 'ajax':
            return defined('DOING_AJAX');
        case 'cron':
            return defined('DOING_CRON');
        case 'frontend':
            return ( ! is_admin() || defined('DOING_AJAX') ) && ! defined('DOING_CRON');
        }
    }

    function define_constants()
    {

    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name  Constant name.
     * @param string|bool $value Constant value.
     */
    private function define( $name, $value )
    {
        if (! defined($name) ) {
            define($name, $value);
        }
    }

    /**
     * Cloning and unserialization are not permitted for singletons.
     */
    protected function __clone()
    { 
    }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
    
    
    private function include_files()
    {

        //        require_once CHILD_DIR . '/vendor/autoload.php';
	    include_once CHILD_DIR . '/includes/settings.php';
        include_once CHILD_DIR . '/includes/wp-script-module.php';
        include_once CHILD_DIR . '/includes/theme-core-functions.php';

        // Core Class
        include_once CHILD_DIR . '/includes/class-singleton.php';
        include_once CHILD_DIR . '/includes/woocommerce/custom-order-status.php';
        include_once CHILD_DIR . '/includes/class-theme-setup.php';

        if($this->is_request('admin') ) {

            include_once CHILD_DIR . '/includes/admin/admin-manager.php';
        }

        if($this->is_request('frontend') ) {
            include_once CHILD_DIR . '/includes/services/order-cancellation.php';
            include_once CHILD_DIR . '/includes/class-frontend.php';
        }

        if($this->is_request('ajax') ) {
            include_once CHILD_DIR . '/includes/class-ajax.php';
        }

    }


    private function init_hooks()
    {
        add_action(
            'woocommerce_init', function () {
                // Adding custom email class for return confirmation
                add_filter('woocommerce_email_classes', 'add_return_confirmation_email_class');

                // Adding custom email class for admin approval of return
                add_filter('woocommerce_email_classes', 'add_admin_approve_return_email_class');

                // Adding custom email class for new return notification to admin
                add_filter('woocommerce_email_classes', 'add_admin_new_return_email_class'); // Add this line

                add_filter(
                    'woocommerce_kses_notice_allowed_tags', function ($allowed_tags) {

                        $allowed_tags['svg'] = [
                        'xmlns' => true,
                        'width' => true,
                        'height' => true,
                        'viewBox' => true,
                        'fill' => true,
                        'stroke-width' => true,
                        'stroke' => true,
                        'class' => true
                        ];

                        $allowed_tags['path'] = [
                        'd' => true,
                        'fill' => true,
                        'stroke-linecap' => true,
                        'stroke-linejoin' => true
                        ];

                        return $allowed_tags;

                    }
                );

            }
        );

        // Function to add return confirmation email class
        function add_return_confirmation_email_class($email_classes)
        {
            // Include the file for the return confirmation email class
            include_once CHILD_DIR . '/includes/woocommerce/emails/return-confirmation.php';
            // Add the email class to the list of WooCommerce email classes
            $email_classes['WC_Return_Confirmation'] = new WC_Return_Confirmation();
            return $email_classes;
        }

        // Function to add admin approve return email class
        function add_admin_approve_return_email_class($email_classes)
        {
            // Include the file for the admin approve return email class
            include_once CHILD_DIR . '/includes/woocommerce/emails/admin-approve-return.php';
            // Add the email class to the list of WooCommerce email classes
            $email_classes['WC_Admin_Approve_Return'] = new WC_Admin_Approve_Return();
            return $email_classes;
        }

        // New function to add your custom 'new return' email class
        function add_admin_new_return_email_class($email_classes)
        {
            // Include the file for the new return email class
            include_once CHILD_DIR . '/includes/woocommerce/emails/admin-new-return.php';
            // Replace 'WC_Admin_New_Return' with the actual class name of your new email class
            $email_classes['WC_Admin_New_Return'] = new WC_Admin_New_Return();
            return $email_classes;
        }


	    add_action('wc_ajax_update_cart_item_quantity', 'update_cart_item_quantity');

	    function update_cart_item_quantity(): void {
		    ob_start();

		    // Lấy và làm sạch dữ liệu đầu vào
		    $cart_item_key = wc_clean( isset( $_POST['cart_item_key'] ) ? wp_unslash( $_POST['cart_item_key'] ) : '' );
		    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;


		    // Kiểm tra xem giỏ hàng đã khởi tạo chưa
		    if (!WC()->cart) {
			    wp_send_json_error('Cart not initialized');
			    return;
		    }

		    // Cập nhật số lượng mục trong giỏ hàng
		    if ($cart_item_key && $quantity > 0) {
			    WC()->cart->set_quantity($cart_item_key, $quantity, true);
			    WC_AJAX::get_refreshed_fragments();
		    } else {
			    wp_send_json_error('Invalid cart item key or quantity');
		    }
	    }


	    function custom_blockui_defaults() {
		    ?>
		    <script>
                jQuery(document).ready(function($) {
                    $.blockUI.defaults.overlayCSS = {
                        backgroundColor: '#fff',
                        opacity: 0.6,
                        cursor: 'wait'
                    };
                });
		    </script>
		    <?php
	    }
	    add_action('wp_footer', 'custom_blockui_defaults', 100);
    }


    function load_api()
    {
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

return Initializer::getInstance();