<?php
/*
 * Plugin Name: Typesense Search for WooCommerce
 * Description: Typesense Search for WooCommerce supercharges your WooCommerce site search functionality with Typesense providing search-as-you-type experience. Product searching will be instant and super-fast which enhances user experience on your site.
 * Plugin URI: https://www.codemanas.com/downloads/typesense-search-for-woocommerce/
 * Author: Codemanas
 * Author URI: https://www.codemanas.com/
 * Version: 1.7.2
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * License: http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * Text Domain: typesense-search-for-woocommerce
*/

defined( 'ABSPATH' ) || die( 'Script Kiddies Go Away' );
defined( 'CM_TSFWC_VERSION' ) || define( 'CM_TSFWC_VERSION', '1.7.2' );
defined( 'CM_TSFWC_FILE_PATH' ) || define( 'CM_TSFWC_FILE_PATH', __FILE__ );
defined( 'CM_TSFWC_ROOT_DIR_PATH' ) || define( 'CM_TSFWC_ROOT_DIR_PATH', DIRNAME( __FILE__ ) );
defined( 'CM_TSFWC_ROOT_URI_PATH' ) || define( 'CM_TSFWC_ROOT_URI_PATH', plugin_dir_url( __FILE__ ) );
defined( 'CM_TSFWC_TYPESENSE_BASE_FILE' ) || define( 'CM_TSFWC_TYPESENSE_BASE_FILE', plugin_basename( __FILE__ ) );

require_once CM_TSFWC_ROOT_DIR_PATH . '/includes/Bootstrap.php';
