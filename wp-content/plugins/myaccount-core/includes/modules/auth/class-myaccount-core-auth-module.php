<?php

defined( 'ABSPATH' ) || exit;

/**
 * Optional auth UI (login / lost password / reset password) for My Account.
 *
 * Toggle per site: filter `myaccount_core_auth_module_enabled` (default true).
 * When false, WooCommerce default templates are used and plugin auth assets are skipped for guests on auth-related endpoints.
 */
class MyAccount_Core_Auth_Module {
	private static ?MyAccount_Core_Auth_Module $instance = null;

	/**
	 * Template paths relative to templates/woocommerce/ that this module owns when enabled.
	 *
	 * @var array<int, string>
	 */
	private const MANAGED_AUTH_TEMPLATES = array(
		'myaccount/form-login.php',
		'myaccount/form-lost-password.php',
		'myaccount/form-reset-password.php',
	);

	/**
	 * Account endpoint slugs where guest users skip plugin assets when the module is off.
	 *
	 * @var array<int, string>
	 */
	private const GUEST_BYPASS_ENDPOINTS = array(
		'lost-password',
		'reset-password',
		'dashboard',
		'unknown',
	);

	public static function instance(): MyAccount_Core_Auth_Module {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function is_enabled(): bool {
		return (bool) apply_filters( 'myaccount_core_auth_module_enabled', true );
	}

	/**
	 * Whether to skip enqueueing plugin My Account assets (used by MyAccount_Core_Assets).
	 */
	public static function should_bypass_plugin_auth_assets( string $endpoint ): bool {
		return ! is_user_logged_in()
			&& ! self::is_enabled()
			&& in_array( $endpoint, self::GUEST_BYPASS_ENDPOINTS, true );
	}

	private function __construct() {
		if ( self::is_enabled() ) {
			return;
		}

		add_filter( 'myaccount_core_managed_templates', array( $this, 'remove_auth_templates_from_managed' ), 999 );
	}

	/**
	 * @param array<int, string> $templates
	 * @return array<int, string>
	 */
	public function remove_auth_templates_from_managed( array $templates ): array {
		$templates = array_map(
			static function ( $template ): string {
				return ltrim( is_string( $template ) ? $template : '', '/' );
			},
			$templates
		);

		$templates = array_values( array_diff( $templates, self::MANAGED_AUTH_TEMPLATES ) );

		return array_values( array_unique( array_filter( $templates ) ) );
	}
}
