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
		add_filter( 'script_loader_tag', array( $this, 'add_defer_attribute' ), 10, 2 );
	}

	public function enqueue_assets(): void {
		if ( ! is_account_page() ) {
			return;
		}

		$css_file = $this->plugin_dir . 'assets/css/myaccount.css';
		if ( file_exists( $css_file ) ) {
			wp_enqueue_style(
				'myaccount-core-css',
				$this->plugin_url . 'assets/css/myaccount.css',
				array(),
				filemtime( $css_file )
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
		}
	}

	public function add_defer_attribute( string $tag, string $handle ): string {
		if ( 'alpine-bundle' === $handle ) {
			return str_replace( ' src', ' defer="defer" src', $tag );
		}

		return $tag;
	}
}
