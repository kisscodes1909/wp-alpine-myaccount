<?php

defined( 'ABSPATH' ) || exit;

class MyAccount_Core_Returns_Module {
	private static ?MyAccount_Core_Returns_Module $instance = null;
	private bool $section_assets_enqueued = false;
	private string $plugin_dir;
	private string $plugin_url;
	private bool $use_min_assets = false;

	public static function instance( string $plugin_dir = '', string $plugin_url = '' ): MyAccount_Core_Returns_Module {
		if ( null === self::$instance ) {
			self::$instance = new self( $plugin_dir, $plugin_url );
		}

		return self::$instance;
	}

	private function __construct( string $plugin_dir, string $plugin_url ) {
		$this->plugin_dir      = trailingslashit( $plugin_dir );
		$this->plugin_url      = trailingslashit( $plugin_url );
		$this->use_min_assets  = $this->should_use_min_assets();

		if ( ! self::is_enabled() ) {
			return;
		}

		MyAccount_Core_Returns::instance();
		MyAccount_Core_Returns_Admin::instance();

		add_action( 'myaccount_core_view_order_after_items_summary', array( $this, 'render_view_order_section' ), 10, 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 25 );
		add_filter( 'myaccount_core_endpoint_js_dependencies', array( $this, 'filter_endpoint_js_dependencies' ), 10, 2 );
		add_filter( 'script_loader_tag', array( $this, 'add_defer_attribute' ), 10, 2 );
	}

	public static function is_enabled(): bool {
		return (bool) apply_filters( 'myaccount_core_returns_module_enabled', false );
	}

	public function enqueue_assets(): void {
		if ( ! $this->should_load_for_current_request() ) {
			return;
		}

		$this->enqueue_section_assets();

		if ( wp_script_is( 'alpine-bundle', 'enqueued' ) ) {
			$js_dependency = 'alpine-bundle';

			$this->enqueue_script_if_exists(
				'myaccount-core-module-returns-js',
				$this->asset_path( 'assets/js/alpine.module-returns.js' ),
				array( $js_dependency )
			);
		}
	}

	public function filter_endpoint_js_dependencies( array $deps, string $endpoint ): array {
		if ( 'view-order' !== $endpoint || ! $this->should_load_for_current_request() ) {
			return $deps;
		}

		$this->enqueue_section_assets();

		$module_script_loaded = wp_script_is( 'myaccount-core-module-returns-js', 'enqueued' );

		if ( $module_script_loaded ) {
			$deps[] = 'myaccount-core-module-returns-js';
		}

		return array_values( array_unique( $deps ) );
	}

	public function localize_view_order_data( WC_Order $order ): void {
		if ( ! self::is_enabled() || ! wp_script_is( 'myaccount-core-module-returns-js', 'enqueued' ) ) {
			return;
		}

		wp_localize_script(
			'myaccount-core-module-returns-js',
			'viewOrderReturnsData',
			$this->build_view_order_payload( $order )
		);
	}

	public function render_view_order_section( $order ): void {
		if ( ! $this->should_render_view_order_section( $order ) ) {
			return;
		}

		$this->enqueue_section_assets();

		$returns_service = MyAccount_Core_Returns::instance();
		$returns_policy  = $returns_service->get_policy_context( $order );
		$return_requests = $returns_service->get_requests( $order );
		$eligible_items  = $returns_service->get_eligible_items( $order );

		$this->localize_view_order_data( $order );

		wc_get_template(
			'order/order-returns.php',
			array(
				'order'             => $order,
				'section_id'        => 'ma-view-order-returns-' . (int) $order->get_id(),
				'existing_requests' => $return_requests,
				'eligible_items'    => $eligible_items,
				'policy'            => $returns_policy,
				'request_types'     => $returns_service->get_request_type_labels(),
			)
		);
	}

	private function should_load_for_current_request(): bool {
		return self::is_enabled() && is_account_page() && is_wc_endpoint_url( 'view-order' );
	}

	private function should_render_view_order_section( $order ): bool {
		return $this->should_load_for_current_request() && $order instanceof WC_Order;
	}

	private function enqueue_section_assets(): void {
		if ( $this->section_assets_enqueued ) {
			return;
		}

		$css_deps = wp_style_is( 'myaccount-core-css-endpoint', 'enqueued' ) ? array( 'myaccount-core-css-endpoint' ) : array();
		$this->enqueue_style_if_exists(
			'myaccount-core-module-returns-css',
			$this->asset_path( 'assets/css/ma-module-returns.css' ),
			$css_deps
		);

		if ( wp_script_is( 'myaccount-core-js-shared-core', 'enqueued' ) ) {
			$this->enqueue_script_if_exists(
				'myaccount-core-module-returns-js',
				$this->asset_path( 'assets/js/alpine.module-returns.js' ),
				array( 'myaccount-core-js-shared-core' )
			);
		}

		$this->section_assets_enqueued = true;
	}

	private function build_view_order_payload( WC_Order $order ): array {
		$returns_service = MyAccount_Core_Returns::instance();

		return array(
			'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
			'nonce'         => wp_create_nonce( 'submit-return-request' ),
			'orderId'       => $order->get_id(),
			'policy'        => $returns_service->get_policy_context( $order ),
			'requests'      => $returns_service->get_requests( $order ),
			'eligibleItems' => $returns_service->get_eligible_items( $order ),
			'requestTypes'  => $returns_service->get_request_type_labels(),
			'i18n'          => array(
				'selectItem'      => __( 'Please select at least one item to return or exchange.', 'myaccount-core' ),
				'missingReason'   => __( 'Please tell us why you want to return or exchange these items.', 'myaccount-core' ),
				'invalidQuantity' => __( 'One or more quantities are not valid for return.', 'myaccount-core' ),
				'genericError'    => __( 'Something went wrong. Please try again.', 'myaccount-core' ),
			),
		);
	}

	public function add_defer_attribute( string $tag, string $handle ): string {
		if ( 'myaccount-core-module-returns-js' !== $handle ) {
			return $tag;
		}

		return str_replace( ' src', ' defer="defer" src', $tag );
	}

	private function should_use_min_assets(): bool {
		if ( defined( 'MYACCOUNT_CORE_USE_MIN_ASSETS' ) ) {
			return (bool) MYACCOUNT_CORE_USE_MIN_ASSETS;
		}

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			return false;
		}

		if ( function_exists( 'wp_get_environment_type' ) ) {
			return wp_get_environment_type() === 'production';
		}

		$env = getenv( 'WP_ENVIRONMENT_TYPE' );

		return false !== $env && 'production' === $env;
	}

	private function asset_path( string $relative_path ): string {
		if ( ! $this->use_min_assets ) {
			return $relative_path;
		}

		$min_path = preg_replace( '/\.(css|js)$/', '.min.$1', $relative_path );
		if ( ! is_string( $min_path ) || $min_path === $relative_path ) {
			return $relative_path;
		}

		return file_exists( $this->plugin_dir . $min_path ) ? $min_path : $relative_path;
	}

	private function enqueue_style_if_exists( string $handle, string $relative_path, array $deps = array() ): bool {
		$file = $this->plugin_dir . $relative_path;

		if ( ! file_exists( $file ) ) {
			return false;
		}

		wp_enqueue_style( $handle, $this->plugin_url . $relative_path, $deps, (string) filemtime( $file ) );

		return true;
	}

	private function enqueue_script_if_exists( string $handle, string $relative_path, array $deps = array() ): bool {
		$file = $this->plugin_dir . $relative_path;

		if ( ! file_exists( $file ) ) {
			return false;
		}

		wp_enqueue_script( $handle, $this->plugin_url . $relative_path, $deps, (string) filemtime( $file ), true );

		return true;
	}
}
