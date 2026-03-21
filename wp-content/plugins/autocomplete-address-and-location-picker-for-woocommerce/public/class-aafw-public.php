<?php

use Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils;
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://powerfulwp.com/
 * @since      1.0.0
 *
 * @package    Aafw
 * @subpackage Aafw/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Aafw
 * @subpackage Aafw/public
 * @author     powerfulwp <apowerfulwp@gmail.com>
 */
class Aafw_Public {
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name       The name of the plugin.
     * @param      string $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Detect the actual checkout type by checking page content
     * 
     * @since 1.2.0
     * @return bool True if blocks checkout, false if classic/legacy
     */
    private function detect_checkout_type() {
        // Check if we're in the main query
        if ( !is_main_query() ) {
            return false;
        }
        // Get the current page content
        global $post;
        if ( !$post ) {
            return false;
        }
        $content = $post->post_content;
        // Check for BLOCKS checkout indicators
        $blocks_indicators = array('<!-- wp:woocommerce/checkout', 'wp-block-woocommerce-checkout');
        foreach ( $blocks_indicators as $indicator ) {
            if ( strpos( $content, $indicator ) !== false ) {
                return true;
                // Blocks checkout detected
            }
        }
        // Check for LEGACY checkout indicators
        $legacy_indicators = array('[woocommerce_checkout]', 'woocommerce-checkout', 'checkout-form');
        foreach ( $legacy_indicators as $indicator ) {
            if ( strpos( $content, $indicator ) !== false ) {
                return false;
                // Legacy checkout detected
            }
        }
        // Fallback: check body classes
        $body_classes = get_body_class();
        if ( in_array( 'woocommerce-checkout', $body_classes ) ) {
            // Check if it's blocks checkout by looking for blocks-specific classes
            $blocks_classes = array('wc-blocks-checkout', 'woocommerce-blocks-checkout');
            foreach ( $blocks_classes as $class ) {
                if ( in_array( $class, $body_classes ) ) {
                    return true;
                    // Blocks checkout
                }
            }
            return false;
            // Legacy checkout
        }
        // Final fallback: use WooCommerce's official method
        if ( class_exists( 'Automattic\\WooCommerce\\Blocks\\Utils\\CartCheckoutUtils' ) ) {
            return CartCheckoutUtils::is_checkout_block_default();
        }
        return false;
        // Default to legacy
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Aafw_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Aafw_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        if ( is_checkout() ) {
            if ( true === AAFW_AUTOCOMPLETE ) {
                wp_enqueue_style(
                    $this->plugin_name,
                    plugin_dir_url( __FILE__ ) . 'css/aafw-public.css',
                    array(),
                    $this->version,
                    'all'
                );
            }
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Aafw_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Aafw_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        // Check if is checkout page.
        if ( is_checkout() ) {
            if ( true === AAFW_AUTOCOMPLETE ) {
                $aafw_google_api_key = get_option( 'aafw_google_api_key', '' );
                $aafw_api_type = 'Autocomplete';
                $aafw_billing_autocomplete = get_option( 'aafw_billing_autocomplete', '' );
                $aafw_shipping_autocomplete = get_option( 'aafw_shipping_autocomplete', '' );
                $aafw_pickup_autocomplete = get_option( 'aafw_pickup_autocomplete', '' );
                $aafw_map = get_option( 'aafw_initial_map', '' );
                $aafw_address_format = get_option( 'aafw_address_format', '' );
                $aafw_debug_logs = get_option( 'aafw_debug_logs', '0' );
                $aafw_restrictions = '';
                $aafw_location_picker = '';
                $aafw_coordinates = '';
                $aafw_customer_location = '';
                $aafw_center_map_latitude = '';
                $aafw_center_map_longitude = '';
                $aafw_location_picker_type = '';
                $aafw_customer_location_auto_select = '';
                $aafw_map_zoom = '';
                // Normalize debug toggle.
                $aafw_debug_logs = ( '1' === (string) $aafw_debug_logs ? '1' : '0' );
                // Set map zoom.
                $aafw_map_zoom = ( '' !== $aafw_map_zoom ? $aafw_map_zoom : 11 );
                // Set center map coordinates.
                if ( !is_numeric( $aafw_center_map_latitude ) || !is_numeric( $aafw_center_map_longitude ) ) {
                    $aafw_center_map_latitude = '40.730610';
                    $aafw_center_map_longitude = '-73.935242';
                }
                // Detect checkout type
                $is_blocks_checkout = $this->detect_checkout_type();
                if ( '' !== $aafw_google_api_key ) {
                    $language = get_locale();
                    if ( strlen( $language ) > 0 ) {
                        $language = explode( '_', $language )[0];
                    } else {
                        $language = 'en';
                    }
                    // Build Google Maps API URL based on API type
                    $api_libraries = 'places';
                    $google_maps_url = 'https://maps.googleapis.com/maps/api/js?key=' . $aafw_google_api_key . '&loading=async&language=' . $language . '&libraries=' . $api_libraries . '&v=weekly';
                    if ( $is_blocks_checkout ) {
                    } else {
                        if ( $aafw_api_type === 'Autocomplete' ) {
                            // CLASSIC CHECKOUT: Load existing JavaScript (Autocomplete API)
                            wp_enqueue_script(
                                'aafw-googleapis',
                                $google_maps_url . '&callback=aafw_initAutocomplete',
                                array('jquery'),
                                $this->version,
                                true
                            );
                            wp_enqueue_script(
                                $this->plugin_name,
                                plugin_dir_url( __FILE__ ) . 'js/aafw-public.js',
                                array('jquery'),
                                $this->version,
                                false
                            );
                            wp_enqueue_style(
                                $this->plugin_name,
                                plugin_dir_url( __FILE__ ) . 'css/aafw-public.css',
                                array(),
                                $this->version,
                                'all'
                            );
                            wp_localize_script( $this->plugin_name, 'aafw_autocomplete', array(
                                'aafw_billing'                       => esc_js( $aafw_billing_autocomplete ),
                                'aafw_shipping'                      => esc_js( $aafw_shipping_autocomplete ),
                                'aafw_pickup'                        => esc_js( $aafw_pickup_autocomplete ),
                                'aafw_map'                           => esc_js( $aafw_map ),
                                'aafw_restrictions'                  => $aafw_restrictions,
                                'aafw_location_picker'               => esc_js( $aafw_location_picker ),
                                'aafw_coordinates'                   => esc_js( $aafw_coordinates ),
                                'aafw_customer_location'             => esc_js( $aafw_customer_location ),
                                'aafw_select_address_text'           => esc_js( __( 'Select address', 'aafw' ) ),
                                'aafw_address_selected_text'         => esc_js( __( 'Address selected', 'aafw' ) ),
                                'aafw_search_address_text'           => esc_js( __( 'Search Address for Autofill:', 'aafw' ) ),
                                'aafw_center_map_latitude'           => esc_js( $aafw_center_map_latitude ),
                                'aafw_center_map_longitude'          => esc_js( $aafw_center_map_longitude ),
                                'aafw_location_picker_type'          => esc_js( $aafw_location_picker_type ),
                                'aafw_select_location_text'          => esc_js( __( 'Select location', 'aafw' ) ),
                                'aafw_location_selected_text'        => esc_js( __( 'Location selected', 'aafw' ) ),
                                'aafw_map_zoom'                      => esc_js( $aafw_map_zoom ),
                                'aafw_customer_location_auto_select' => esc_js( $aafw_customer_location_auto_select ),
                                'aafw_address_format'                => esc_js( $aafw_address_format ),
                                'aafw_api_type'                      => esc_js( $aafw_api_type ),
                                'aafw_debug_logs'                    => esc_js( $aafw_debug_logs ),
                                'checkout_type'                      => 'classic',
                            ) );
                        } else {
                        }
                    }
                }
            }
        }
    }

    /**
     * Adds 'async' attribute to a specific enqueued script.
     *
     * This function is hooked to the 'script_loader_tag' filter and checks
     * if the script being loaded is the one with the handle 'aafw-googleapis'.
     * If it is, it adds the 'async' attribute to the script tag.
     *
     * @param    string $tag    The HTML script tag of the enqueued script.
     * @param    string $handle The handle of the enqueued script.
     * @return   string    The modified script tag if the handle matches, otherwise the original tag.
     */
    public function async_script_loader_tags( $tag, $handle ) {
        if ( !in_array( $handle, array('aafw-googleapis'), true ) ) {
            return $tag;
        }
        return str_replace( '<script src', '<script async src', $tag );
    }

    /**
     * Billing map on checkout.
     *
     * @since     1.0.0
     * @return    html    The billing map.
     */
    public function billing_map( $checkout ) {
        $aafw_map = get_option( 'aafw_initial_map', '' );
        if ( '1' === $aafw_map ) {
            echo '<div id="aafw_billing_map"></div>';
        }
    }

    /**
     * Shipping map on checkout.
     *
     * @since     1.0.0
     * @return    html    The shipping map.
     */
    public function shipping_map( $checkout ) {
        $aafw_map = get_option( 'aafw_initial_map', '' );
        if ( '1' === $aafw_map ) {
            echo '<div id="aafw_shipping_map"></div>';
        }
    }

    /**
     * Pickup map on checkout.
     *
     * @since     1.0.0
     * @return    html    The pickup map.
     */
    public function pickup_map( $checkout ) {
        if ( in_array( 'pickup-and-delivery-from-customer-locations-for-woocommerce-pro', AAFW_PLUGINS, true ) ) {
            $aafw_map = get_option( 'aafw_initial_map', '' );
            if ( '1' === $aafw_map ) {
                echo '<div id="aafw_pickup_map"></div>';
            }
        }
    }

}
