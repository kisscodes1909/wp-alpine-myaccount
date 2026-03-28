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
		MyAccount_Core_Auth_Module::instance();
		MyAccount_Core_Template_Loader::instance( self::$plugin_dir );
		MyAccount_Core_Assets::instance( self::$plugin_dir, self::$plugin_url );
		MyAccount_Core_Account_Asset_Optimizer::instance();
		MyAccount_Core_Address_Module::instance();
		MyAccount_Core_Wishlist_Module::instance( self::$plugin_dir, self::$plugin_url );
		MyAccount_Core_Tracking_Module::instance();
		MyAccount_Core_Returns_Module::instance( self::$plugin_dir, self::$plugin_url );
		MyAccount_Core_Ajax::instance();
	}

	public static function activate(): void {
		add_option( self::OPTION_OWNER_MODE, 'plugin' );

		if ( class_exists( 'WooCommerce' ) ) {
			if ( MyAccount_Core_Address_Module::is_enabled() ) {
				MyAccount_Core_Address_Module::register_endpoints();
			}

			if ( MyAccount_Core_Wishlist_Module::is_enabled() ) {
				MyAccount_Core_Wishlist_Module::register_endpoints();
			}

			flush_rewrite_rules();
		}
	}

	private static function register_autoloader(): void {
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	private static function autoload( string $class ): void {
		if ( 0 !== strpos( $class, 'MyAccount_Core_' ) ) {
			return;
		}

		$path = self::resolve_class_path( $class );

		if ( '' !== $path && is_readable( $path ) ) {
			require_once $path;
		}
	}

	private static function resolve_class_path( string $class ): string {
		$filename = 'class-' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
		$core_classes = array(
			'MyAccount_Core_Plugin',
			'MyAccount_Core_Admin',
			'MyAccount_Core_Ajax',
			'MyAccount_Core_Assets',
			'MyAccount_Core_Account_Asset_Optimizer',
			'MyAccount_Core_Hooks',
			'MyAccount_Core_Template_Loader',
		);

		if ( in_array( $class, $core_classes, true ) ) {
			return self::$plugin_dir . 'includes/core/' . $filename;
		}

		if ( 'MyAccount_Core_Returns' === $class || 'MyAccount_Core_Returns_Service' === $class || 0 === strpos( $class, 'MyAccount_Core_Returns_' ) ) {
			$returns_filename = 'MyAccount_Core_Returns' === $class ? 'class-myaccount-core-returns-service.php' : $filename;

			return self::$plugin_dir . 'includes/modules/returns/' . $returns_filename;
		}

		if ( 0 === strpos( $class, 'MyAccount_Core_Auth_' ) ) {
			return self::$plugin_dir . 'includes/modules/auth/' . $filename;
		}

		if ( 0 === strpos( $class, 'MyAccount_Core_Address_' ) ) {
			return self::$plugin_dir . 'includes/modules/address/' . $filename;
		}

		if ( 0 === strpos( $class, 'MyAccount_Core_Wishlist_' ) ) {
			return self::$plugin_dir . 'includes/modules/wishlist/' . $filename;
		}

		if ( 0 === strpos( $class, 'MyAccount_Core_Tracking_Adapter_' ) ) {
			return self::$plugin_dir . 'includes/modules/tracking/adapters/' . $filename;
		}

		if ( 0 === strpos( $class, 'MyAccount_Core_Tracking_' ) ) {
			return self::$plugin_dir . 'includes/modules/tracking/' . $filename;
		}

		return '';
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
