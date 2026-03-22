<?php

defined( 'ABSPATH' ) || exit;

class MyAccount_Core_Plugin {
	public const OPTION_OWNER_MODE = 'myaccount_core_owner_mode';
	private static string $plugin_file = '';
	private static string $plugin_dir = '';
	private static string $plugin_url = '';

	public static function boot( string $plugin_file ): void {
		self::$plugin_file = $plugin_file;
		self::$plugin_dir  = plugin_dir_path( $plugin_file );
		self::$plugin_url  = plugin_dir_url( $plugin_file );

		self::register_autoloader();

		register_activation_hook( $plugin_file, array( __CLASS__, 'activate' ) );
		register_deactivation_hook( $plugin_file, array( __CLASS__, 'deactivate' ) );

		add_action( 'plugins_loaded', array( __CLASS__, 'init' ), 20 );
	}

	public static function init(): void {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		MyAccount_Core_Admin::instance();

		if ( ! self::is_plugin_owner() ) {
			return;
		}

		MyAccount_Core_Hooks::instance();
		MyAccount_Core_Template_Loader::instance( self::$plugin_dir );
		MyAccount_Core_Assets::instance( self::$plugin_dir, self::$plugin_url );
		MyAccount_Core_Returns_Module::instance( self::$plugin_dir, self::$plugin_url );
		MyAccount_Core_Ajax::instance();
	}

	public static function activate(): void {
		add_option( self::OPTION_OWNER_MODE, 'plugin' );

		if ( class_exists( 'WooCommerce' ) ) {
			MyAccount_Core_Hooks::register_endpoints();
			flush_rewrite_rules();
		}
	}

	/**
	 * WordPress class file naming: MyAccount_Core_Foo_Bar → includes/class-myaccount-core-foo-bar.php
	 */
	private static function register_autoloader(): void {
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	private static function autoload( string $class ): void {
		if ( 0 !== strpos( $class, 'MyAccount_Core_' ) ) {
			return;
		}

		$path = self::$plugin_dir . 'includes/class-' . str_replace( '_', '-', strtolower( $class ) ) . '.php';

		if ( is_readable( $path ) ) {
			require_once $path;
		}
	}

	public static function deactivate(): void {
		flush_rewrite_rules();
	}

	public static function get_owner_mode(): string {
		$mode = (string) get_option( self::OPTION_OWNER_MODE, 'plugin' );

		return in_array( $mode, array( 'plugin', 'theme' ), true ) ? $mode : 'plugin';
	}

	public static function is_plugin_owner(): bool {
		return 'plugin' === self::get_owner_mode();
	}
}
