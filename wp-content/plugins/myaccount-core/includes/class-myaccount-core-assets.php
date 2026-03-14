<?php

defined( 'ABSPATH' ) || exit;

class MyAccount_Core_Assets {
	private static ?MyAccount_Core_Assets $instance = null;
	private string $plugin_dir;
	private string $plugin_url;
	private bool $use_min_assets = false;

	public static function instance( string $plugin_dir, string $plugin_url ): MyAccount_Core_Assets {
		if ( null === self::$instance ) {
			self::$instance = new self( $plugin_dir, $plugin_url );
		}

		return self::$instance;
	}

	private function __construct( string $plugin_dir, string $plugin_url ) {
		$this->plugin_dir  = trailingslashit( $plugin_dir );
		$this->plugin_url  = trailingslashit( $plugin_url );
		$this->use_min_assets = $this->should_use_min_assets();

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 20 );
		add_action( 'wp_head', array( $this, 'preload_shared_css' ), 1 );
		add_filter( 'body_class', array( $this, 'add_endpoint_body_class' ) );
		add_filter( 'script_loader_tag', array( $this, 'add_defer_attribute' ), 10, 2 );
	}

	public function enqueue_assets(): void {
		if ( ! is_account_page() ) {
			return;
		}

		$endpoint      = $this->get_account_endpoint();
		$shared_loaded = $this->enqueue_style_if_exists(
			'myaccount-core-css-shared',
			$this->asset_path( 'assets/css/ma-shared.css' )
		);

		$nav_deps = $shared_loaded ? array( 'myaccount-core-css-shared' ) : array();
		if ( is_user_logged_in() ) {
			$this->enqueue_style_if_exists(
				'myaccount-core-css-navigation',
				$this->asset_path( 'assets/css/ma-navigation.css' ),
				$nav_deps
			);
		}

		$endpoint_file   = $this->resolve_endpoint_css_file( $endpoint );
		$endpoint_loaded = false;
		$endpoint_deps   = $shared_loaded ? array( 'myaccount-core-css-shared' ) : array();

		if ( '' !== $endpoint_file ) {
			$endpoint_loaded = $this->enqueue_style_if_exists(
				'myaccount-core-css-endpoint',
				$this->asset_path( 'assets/css/' . $endpoint_file ),
				$endpoint_deps
			);
		}

		if ( ! $endpoint_loaded && 'ma-auth.css' !== $endpoint_file ) {
			$endpoint_loaded = $this->enqueue_style_if_exists(
				'myaccount-core-css-endpoint-auth-fallback',
				$this->asset_path( 'assets/css/ma-auth.css' ),
				$endpoint_deps
			);
		}

		if ( ! $shared_loaded || ! $endpoint_loaded ) {
			$this->log_asset_fallback(
				$shared_loaded,
				$endpoint_loaded,
				$this->asset_path( 'assets/css/ma-shared.css' ),
				$endpoint_file
			);
			$this->enqueue_style_if_exists(
				'myaccount-core-css',
				$this->asset_path( 'assets/css/myaccount.css' )
			);
		}

		$validation_required   = $this->endpoint_requires_validation_js( $endpoint );
		$endpoint_js_file      = $this->resolve_endpoint_js_file( $endpoint );
		$shared_js_path        = $this->asset_path( 'assets/js/alpine.shared-core.js' );
		$endpoint_js_path      = $this->asset_path( 'assets/js/' . $endpoint_js_file );
		$validation_js_path    = $this->asset_path( 'assets/js/alpine.shared-validation.js' );
		$shared_js_exists     = file_exists( $this->plugin_dir . $shared_js_path );
		$endpoint_js_exists   = file_exists( $this->plugin_dir . $endpoint_js_path );
		$validation_js_exists = ! $validation_required || file_exists( $this->plugin_dir . $validation_js_path );
		$can_use_split_loading = $shared_js_exists && $endpoint_js_exists && $validation_js_exists;
		$legacy_js_loaded      = false;
		$js_shared_loaded     = false;

		if ( $can_use_split_loading ) {
			$js_shared_loaded = $this->enqueue_script_if_exists(
				'myaccount-core-js-shared-core',
				$shared_js_path
			);

			if ( $validation_required ) {
				$validation_deps = $js_shared_loaded ? array( 'myaccount-core-js-shared-core' ) : array();
				$this->enqueue_script_if_exists(
					'myaccount-core-js-shared-validation',
					$validation_js_path,
					$validation_deps
				);
			}

			$endpoint_js_deps = array();
			if ( $js_shared_loaded ) {
				$endpoint_js_deps[] = 'myaccount-core-js-shared-core';
			}
			if ( $validation_required ) {
				$endpoint_js_deps[] = 'myaccount-core-js-shared-validation';
			}

			$this->enqueue_script_if_exists(
				'myaccount-core-js-endpoint',
				$endpoint_js_path,
				$endpoint_js_deps
			);
		} else {
			$legacy_js_loaded = $this->enqueue_script_if_exists(
				'alpine-bundle',
				$this->asset_path( 'assets/js/alpine.bundle.js' )
			);
		}

		$auth_localize_data  = array(
			'wooLoginNonce' => wp_create_nonce( 'woocommerce-login' ),
			'signupNonce'   => wp_create_nonce( 'woocommerce-register' ),
		);
		$auth_target_handle  = ( $can_use_split_loading && $js_shared_loaded ) ? 'myaccount-core-js-shared-core' : 'alpine-bundle';

		if ( ( 'myaccount-core-js-shared-core' === $auth_target_handle && $js_shared_loaded ) || ( 'alpine-bundle' === $auth_target_handle && $legacy_js_loaded ) ) {
			wp_localize_script( $auth_target_handle, 'authenicationData', $auth_localize_data );
		}
	}

	public function add_defer_attribute( string $tag, string $handle ): string {
		$defer_handles = array(
			'alpine-bundle',
			'myaccount-core-js-shared-core',
			'myaccount-core-js-shared-validation',
			'myaccount-core-js-endpoint',
		);

		if ( in_array( $handle, $defer_handles, true ) ) {
			return str_replace( ' src', ' defer="defer" src', $tag );
		}

		return $tag;
	}

	public function add_endpoint_body_class( array $classes ): array {
		if ( ! is_account_page() ) {
			return $classes;
		}

		$endpoint = $this->get_account_endpoint();
		$classes[] = 'ma-endpoint-' . sanitize_html_class( $endpoint );
		$classes[] = 'ma-shared-scope';

		return $classes;
	}

	/**
	 * Whether to enqueue minified assets (.min.css / .min.js).
	 * Uses constant MYACCOUNT_CORE_USE_MIN_ASSETS if set, else wp_get_environment_type() === 'production'.
	 */
	private function should_use_min_assets(): bool {
		if ( defined( 'MYACCOUNT_CORE_USE_MIN_ASSETS' ) ) {
			return (bool) MYACCOUNT_CORE_USE_MIN_ASSETS;
		}
		// Dev CSS watch writes *.css only — SCRIPT_DEBUG avoids loading stale *.min.css.
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			return false;
		}
		if ( function_exists( 'wp_get_environment_type' ) ) {
			return wp_get_environment_type() === 'production';
		}
		$env = getenv( 'WP_ENVIRONMENT_TYPE' );
		return $env !== false && $env === 'production';
	}

	/**
	 * Return asset path with .min suffix when using production assets.
	 * Falls back to non-min path if min file does not exist.
	 */
	private function asset_path( string $relative_path ): string {
		if ( ! $this->use_min_assets ) {
			return $relative_path;
		}
		$min_path = preg_replace( '/\.(css|js)$/', '.min.$1', $relative_path );
		if ( $min_path === $relative_path ) {
			return $relative_path;
		}
		$min_file = $this->plugin_dir . $min_path;
		if ( file_exists( $min_file ) ) {
			return $min_path;
		}
		return $relative_path;
	}

	private function get_account_endpoint(): string {
		$endpoint_keys = array(
			'orders',
			'view-order',
			'payment-methods',
			'add-payment-method',
			'edit-account',
			'edit-address',
			'lost-password',
			'reset-password',
			'customer-logout',
			'address',
		);

		foreach ( $endpoint_keys as $endpoint_key ) {
			if ( is_wc_endpoint_url( $endpoint_key ) ) {
				return $endpoint_key;
			}
		}

		if ( is_wc_endpoint_url() ) {
			return 'unknown';
		}

		return 'dashboard';
	}

	private function resolve_endpoint_css_file( string $endpoint ): string {
		$map = array(
			'orders'             => 'ma-orders.css',
			'view-order'         => 'ma-view-order.css',
			'payment-methods'    => 'ma-payment-methods.css',
			'add-payment-method' => 'ma-payment-methods.css',
			'edit-account'       => 'ma-edit-account.css',
			'edit-address'       => 'ma-edit-account.css',
			'address'            => 'ma-address.css',
			'lost-password'      => 'ma-auth.css',
			'reset-password'     => 'ma-auth.css',
			'dashboard'          => 'ma-auth.css',
			'unknown'            => 'ma-auth.css',
		);

		return isset( $map[ $endpoint ] ) ? $map[ $endpoint ] : 'ma-auth.css';
	}

	private function resolve_endpoint_js_file( string $endpoint ): string {
		$map = array(
			'orders'             => 'alpine.orders.js',
			'view-order'         => 'alpine.view-order.js',
			'payment-methods'    => 'alpine.payment-methods.js',
			'add-payment-method' => 'alpine.payment-methods.js',
			'edit-account'       => 'alpine.edit-account.js',
			'edit-address'       => 'alpine.edit-account.js',
			'address'            => 'alpine.address.js',
			'lost-password'      => 'alpine.auth.js',
			'reset-password'     => 'alpine.auth.js',
			'dashboard'          => 'alpine.auth.js',
			'unknown'            => 'alpine.auth.js',
		);

		return isset( $map[ $endpoint ] ) ? $map[ $endpoint ] : 'alpine.auth.js';
	}

	private function endpoint_requires_validation_js( string $endpoint ): bool {
		$needs_validation = array(
			'address',
			'edit-account',
			'edit-address',
			'lost-password',
			'reset-password',
			'dashboard',
			'unknown',
		);

		return in_array( $endpoint, $needs_validation, true );
	}

	/**
	 * Preload ma-shared CSS on account pages (reduces render-blocking chain when browser supports preload).
	 */
	public function preload_shared_css(): void {
		if ( ! is_account_page() ) {
			return;
		}
		$path = $this->asset_path( 'assets/css/ma-shared.css' );
		$file = $this->plugin_dir . $path;
		if ( ! file_exists( $file ) ) {
			return;
		}
		$url = esc_url( $this->plugin_url . $path );
		echo '<link rel="preload" href="' . $url . '" as="style" />' . "\n";
	}

	/**
	 * Log when split CSS failed so myaccount.css (~94KB min) loads. Enable WP_DEBUG or MYACCOUNT_CORE_LOG_MISSING_ASSETS.
	 */
	private function log_asset_fallback( bool $shared_loaded, bool $endpoint_loaded, string $shared_path, string $endpoint_file ): void {
		if ( ! $this->should_log_missing_assets() ) {
			return;
		}
		$reasons = array();
		if ( ! $shared_loaded ) {
			$reasons[] = 'missing shared: ' . $shared_path . ' (expected under ' . $this->plugin_dir . ')';
		}
		if ( ! $endpoint_loaded && '' !== $endpoint_file ) {
			$reasons[] = 'missing endpoint: assets/css/' . $endpoint_file;
		}
		if ( empty( $reasons ) ) {
			return;
		}
		error_log( '[myaccount-core] CSS fallback myaccount.css loaded. ' . implode( '; ', $reasons ) . ' — run npm run build:production and deploy ma-*.min.css + ma-shared.min.css.' );
	}

	private function should_log_missing_assets(): bool {
		if ( defined( 'MYACCOUNT_CORE_LOG_MISSING_ASSETS' ) && MYACCOUNT_CORE_LOG_MISSING_ASSETS ) {
			return true;
		}
		return defined( 'WP_DEBUG' ) && WP_DEBUG;
	}

	private function enqueue_style_if_exists( string $handle, string $relative_path, array $deps = array() ): bool {
		$file = $this->plugin_dir . $relative_path;
		if ( ! file_exists( $file ) ) {
			if ( $this->should_log_missing_assets() ) {
				error_log( '[myaccount-core] Missing asset (not enqueued): ' . $relative_path );
			}
			return false;
		}

		$version = filemtime( $file );

		wp_enqueue_style(
			$handle,
			$this->plugin_url . $relative_path,
			$deps,
			$version
		);

		return true;
	}

	private function enqueue_script_if_exists( string $handle, string $relative_path, array $deps = array() ): bool {
		$file = $this->plugin_dir . $relative_path;
		if ( ! file_exists( $file ) ) {
			if ( $this->should_log_missing_assets() ) {
				error_log( '[myaccount-core] Missing asset (not enqueued): ' . $relative_path );
			}
			return false;
		}

		$version = filemtime( $file );

		wp_enqueue_script(
			$handle,
			$this->plugin_url . $relative_path,
			$deps,
			$version,
			true
		);

		return true;
	}
}
