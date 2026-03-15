<?php
/**
 * Plugin Name: MyAccount Core
 * Description: Reusable WooCommerce My Account core module (templates, hooks, assets) for any theme.
 * Version: 0.1.0
 * Author: Project Team
 * Requires Plugins: woocommerce
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'MyAccount_Core_Plugin' ) ) {
	require_once __DIR__ . '/includes/class-myaccount-core-plugin.php';
}
define( 'MYACCOUNT_CORE_USE_MIN_ASSETS', false );
if ( ! defined( 'MYACCOUNT_CORE_USE_MIN_ASSETS' ) ) {
	define(
		'MYACCOUNT_CORE_USE_MIN_ASSETS',
		function_exists( 'wp_get_environment_type' ) && wp_get_environment_type() === 'production'
	);
}
if ( ! function_exists( 'myaccount_core_is_plugin_owner' ) ) {
	function myaccount_core_is_plugin_owner(): bool {
		if ( ! class_exists( 'MyAccount_Core_Plugin' ) ) {
			return false;
		}

		return MyAccount_Core_Plugin::is_plugin_owner();
	}
}

MyAccount_Core_Plugin::boot( __FILE__ );
