<?php
/*
 * Plugin Name: Search with Typesense
 * Description: Turbocharge your WordPress search with Typesense
 * Plugin URI: https://typesense.codemanas.com/
 * Author: codemanas
 * Author URI: https://www.codemanas.com/
 * Version: 2.0.4
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * License: http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * Text Domain: search-with-typesense
 * Domain Path: /languages
*/

defined( 'ABSPATH' ) or die( 'Script Kiddies Go Away' );
defined( 'CODEMANAS_TYPESENSE_VERSION' ) || define( 'CODEMANAS_TYPESENSE_VERSION', '2.0.4' );
defined( 'CODEMANAS_TYPESENSE_FILE_PATH' ) || define( 'CODEMANAS_TYPESENSE_FILE_PATH', __FILE__ );
defined( 'CODEMANAS_TYPESENSE_ROOT_DIR_PATH' ) || define( 'CODEMANAS_TYPESENSE_ROOT_DIR_PATH', DIRNAME( __FILE__ ) );
defined( 'CODEMANAS_TYPESENSE_ROOT_URI_PATH' ) || define( 'CODEMANAS_TYPESENSE_ROOT_URI_PATH', plugin_dir_url( __FILE__ ) );
defined( 'CODEMANAS_TYPESENSE_BASE_FILE' ) || define( 'CODEMANAS_TYPESENSE_BASE_FILE', plugin_basename( __FILE__ ) );
defined( 'CODEMANAS_TYPESENSE_THUMBNAIL_IMAGE_URL' ) || define( 'CODEMANAS_TYPESENSE_THUMBNAIL_IMAGE_URL', plugins_url( '/assets/images/placeholder-300x300.jpg', CODEMANAS_TYPESENSE_FILE_PATH ) );
require_once CODEMANAS_TYPESENSE_ROOT_DIR_PATH . '/includes/Bootstrap.php';
