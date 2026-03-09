<?php

defined( 'ABSPATH' ) || exit;

class MyAccount_Core_Assets {
	private static ?MyAccount_Core_Assets $instance = null;
	private string $plugin_dir;
	private string $plugin_url;

	public static function instance( string $plugin_dir, string $plugin_url ): MyAccount_Core_Assets {
		if ( null === self::$instance ) {
			self::$instance = new self( $plugin_dir, $plugin_url );
		}

		return self::$instance;
	}

	private function __construct( string $plugin_dir, string $plugin_url ) {
		$this->plugin_dir = trailingslashit( $plugin_dir );
		$this->plugin_url = trailingslashit( $plugin_url );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 20 );
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
			'assets/css/ma-shared.css'
		);

		$endpoint_file   = $this->resolve_endpoint_css_file( $endpoint );
		$endpoint_loaded = false;
		$endpoint_deps   = $shared_loaded ? array( 'myaccount-core-css-shared' ) : array();

		if ( '' !== $endpoint_file ) {
			$endpoint_loaded = $this->enqueue_style_if_exists(
				'myaccount-core-css-endpoint',
				'assets/css/' . $endpoint_file,
				$endpoint_deps
			);
		}

		if ( ! $endpoint_loaded && 'ma-auth.css' !== $endpoint_file ) {
			$endpoint_loaded = $this->enqueue_style_if_exists(
				'myaccount-core-css-endpoint-auth-fallback',
				'assets/css/ma-auth.css',
				$endpoint_deps
			);
		}

		if ( ! $shared_loaded || ! $endpoint_loaded ) {
			$this->enqueue_style_if_exists(
				'myaccount-core-css',
				'assets/css/myaccount.css'
			);
		}

		$js_file = $this->plugin_dir . 'assets/js/alpine.bundle.js';
		if ( file_exists( $js_file ) ) {
			wp_enqueue_script(
				'alpine-bundle',
				$this->plugin_url . 'assets/js/alpine.bundle.js',
				array(),
				filemtime( $js_file ),
				true
			);

			$captcha_site_key = defined( 'CAPTCHA_SITE_KEY' ) ? CAPTCHA_SITE_KEY : '';
			wp_localize_script(
				'alpine-bundle',
				'authenicationData',
				array(
					'wooLoginNonce' => wp_create_nonce( 'woocommerce-login' ),
					'signupNonce'   => wp_create_nonce( 'woocommerce-register' ),
					'captchaSiteKey' => $captcha_site_key,
				)
			);

			if ( ! empty( $captcha_site_key ) ) {
				wp_enqueue_script(
					'myaccount-core-recaptcha',
					'https://www.google.com/recaptcha/api.js?render=' . rawurlencode( $captcha_site_key ),
					array(),
					null,
					true
				);
			}
		}
	}

	public function add_defer_attribute( string $tag, string $handle ): string {
		if ( 'alpine-bundle' === $handle ) {
			return str_replace( ' src', ' defer="defer" src', $tag );
		}

		if ( 'myaccount-core-recaptcha' === $handle ) {
			return str_replace( ' src', ' async defer src', $tag );
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

	private function enqueue_style_if_exists( string $handle, string $relative_path, array $deps = array() ): bool {
		$file = $this->plugin_dir . $relative_path;
		if ( ! file_exists( $file ) ) {
			return false;
		}

		wp_enqueue_style(
			$handle,
			$this->plugin_url . $relative_path,
			$deps,
			filemtime( $file )
		);

		return true;
	}
}
