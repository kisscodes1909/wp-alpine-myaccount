<?php
/**
 * BFCM Banner Handler
 *
 * @package  Decorator
 */

if (!defined('ABSPATH')) {
    exit;
}

class WT_Decorator_BFCM_Banner {
    /**
     * The single instance of the class
     *
     * @var self
     */
    private static $instance = null;

    /**
     * The dismiss option name in WP Options table
     *
     * @var string
     */
    private $dismiss_option = 'wt_decorator_xmas_2025_dismiss';

    /**
     * BFCM sale link
     *
     * @var string
     */
    private $sale_link = 'https://marketing.webtoffee.com/campaigns?slug=holiday';

    /**
     * Constructor
     */
    private function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_notices', array($this, 'ema_display_banner'));
        add_action('wp_ajax_wt_decorator_dismiss_bfcm', array($this, 'ema_dismiss_bfcm_banner'));
        add_action('admin_footer', array($this, 'ema_inject_analytics_script'));
    }

    /**
     * Ensures only one instance is loaded or can be loaded.
     *
     * @return self
     */
    public static function ema_get_instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Check if we should display the banner
     *
     * @return boolean
     */
    private function ema_should_display_banner() {
        // Check if banner is dismissed
        if (get_option($this->dismiss_option)) {
            return false;
        }

        // Get current screen
        $screen = get_current_screen();
        if (!$screen) {
            return false;
        }

        // Check StoreFrog connection status
        $sf_connection_data = get_option('sf_connector_data', array());
        if (empty($sf_connection_data)) {
            return false;
        }

        // Array of screens where we want to display the banner
        $allowed_screens = array(
            'dashboard',                    // WordPress Dashboard
            'edit-shop_coupon',            // WooCommerce Coupons listing
            'woocommerce_page_wc-orders',             // Orders listing
            'woocommerce_page_wc-admin',
            'toplevel_page_wbte-decorator-connector'
        );

        // For analytics page, also check the path
        if ($screen->id === 'woocommerce_page_wc-admin') {
            // Only show on analytics overview
            if (!isset($_GET['path']) || $_GET['path'] !== '/analytics/overview') { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
                return false;
            }
        }
        $is_allowed = in_array($screen->id, $allowed_screens);

        return $is_allowed;
    }

    /**
     * Enqueue banner styles
     */
    public function enqueue_styles() {
        if (!$this->ema_should_display_banner()) {
            return;
        }

        wp_enqueue_style(
            'wt-decorator-bfcm-banner',
            plugins_url('/assets/css/bfcm-banner.css', dirname(dirname(__FILE__))),
            array(),
            WT_EMAIL_DECORATOR_VERSION
        );

        wp_enqueue_script(
            'wt-decorator-bfcm-banner',
            plugins_url('/assets/js/bfcm-banner.js', dirname(dirname(__FILE__))),
            array('jquery'),
            WT_EMAIL_DECORATOR_VERSION,
            true
        );

        wp_localize_script('wt-decorator-bfcm-banner', 'wtDecoratorBFCM', array(
            'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
            'nonce' => wp_create_nonce('wt_decorator_bfcm_nonce'),
        ));
    }

    /**
     * Display the BFCM banner
     */
    public function ema_display_banner() {
        if (!$this->ema_should_display_banner()) {
            return;
        }

        // Get StoreFrog connection data for email display
        $sf_connection_data = get_option('sf_connector_data', array());
        $connected_email = isset($sf_connection_data['email']) ? $sf_connection_data['email'] : '';
        
        // If no email in connection data, use current user email as fallback
        if (!$connected_email) {
            $connected_email = wp_get_current_user()->user_email;
        }

        // Check if we're on the connector page
        $notice_class = '';
        if (!isset($_GET['page']) || $_GET['page'] !== 'wbte-decorator-connector') { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $notice_class = 'notice';
        }
        ?>
        <div class="wt-decorator-bfcm-banner <?php echo esc_attr($notice_class); ?>">
            <div class="wt-decorator-bfcm-banner-content">
                <div class="wt-decorator-bfcm-text">
                    <h2><?php esc_html_e('Ready for Holiday sale with WebToffee Marketing', 'decorator-woocommerce-email-customizer'); ?></h2>
                    <p><?php esc_html_e('Get exclusive templates to maximize your holiday sales', 'decorator-woocommerce-email-customizer'); ?></p>
                    <div class="wt-decorator-bfcm-actions">
                        <a href="<?php echo esc_url($this->sale_link); ?>" class="wt-decorator-bfcm-button" target="_blank">
                            <?php esc_html_e('View templates', 'decorator-woocommerce-email-customizer'); ?>
                        </a>
                        <div class="wt-decorator-bfcm-connection">
                            <span class="wt-decorator-bfcm-connected-icon">✓</span>
                            <?php esc_html_e('Connected to', 'decorator-woocommerce-email-customizer'); ?> <?php echo esc_html($connected_email); ?>
                        </div>
                    </div>
                </div>
                <div class="wt-decorator-bfcm-preview">
                    <img src="<?php echo esc_url(plugins_url('/assets/images/bfcm/xmas.svg', dirname(dirname(__FILE__)))); ?>" alt="<?php esc_attr_e('BFCM Email Templates Preview', 'decorator-woocommerce-email-customizer'); ?>" class="wt-decorator-bfcm-preview-img">
                </div>
            </div>
            <button type="button" class="notice-dismiss wt-decorator-bfcm-dismiss">
                <span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'decorator-woocommerce-email-customizer'); ?></span>
            </button>
        </div>
        <?php
    }

    /**
     * Ajax handler to dismiss the BFCM banner
     */
    public function ema_dismiss_bfcm_banner() {
        check_ajax_referer('wt_decorator_bfcm_nonce', 'nonce');
        update_option($this->dismiss_option, true);
        wp_send_json_success();
    }

    /**
     * Inject analytics script in admin footer
     * Only runs on WooCommerce analytics pages
     */
    public function ema_inject_analytics_script() {
        $screen = get_current_screen();
        
        // Only inject on analytics page
        if (!$screen || $screen->id !== 'woocommerce_page_wc-admin' || !isset($_GET['path']) || $_GET['path'] !== '/analytics/overview') { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return;
        }
        ob_start();

        // ema_display_banner() html output
        $this->ema_display_banner();
        
        $output = ob_get_clean();
        
        // Only proceed if we have banner HTML
        if (empty(trim($output))) {
            return;
        }
        ?>
        <script type="text/javascript">
            // Wait for DOM to be fully loaded and give extra time for dynamic content
            setTimeout(function() {
                var ema_output = document.createElement('div');
                ema_output.innerHTML = <?php echo wp_json_encode(wp_kses_post($output)); ?>;
                
                // Add margin to the banner
                var banner = ema_output.querySelector('.wt-decorator-bfcm-banner');
                if (banner) {
                    banner.style.margin = '15px 40px 5px 40px';
                }
                
                // Find the header element
                var header = document.querySelector('.woocommerce-layout__header');
                if (header && header.parentNode) {
                    // Insert after the header
                    header.parentNode.insertBefore(ema_output, header.nextSibling);
                } 
            }, 1000); // 1 second delay
        </script>
        <?php
    }
}

// Initialize the BFCM banner
// add_action('admin_init', array('WT_Decorator_BFCM_Banner', 'ema_get_instance'));
