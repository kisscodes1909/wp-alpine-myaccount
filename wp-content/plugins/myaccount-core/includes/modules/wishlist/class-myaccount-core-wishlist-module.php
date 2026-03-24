<?php

defined( 'ABSPATH' ) || exit;

class MyAccount_Core_Wishlist_Module {
	private static ?MyAccount_Core_Wishlist_Module $instance = null;
	private string $plugin_dir;

	public static function instance(): MyAccount_Core_Wishlist_Module {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function register_endpoints(): void {
		add_rewrite_endpoint( 'wishlist', EP_ROOT | EP_PAGES );
	}

	private function __construct() {
		$this->plugin_dir = trailingslashit( dirname( __DIR__, 3 ) );

		if ( ! $this->is_yith_wishlist_active() ) {
			return;
		}

		add_action( 'init', array( __CLASS__, 'register_endpoints' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_yith_wishlist_styles' ), 30 );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'add_wishlist_menu_item' ) );
		add_filter( 'woocommerce_get_query_vars', array( $this, 'add_wishlist_query_var' ) );
		add_action( 'woocommerce_account_wishlist_endpoint', array( $this, 'render_wishlist_endpoint' ) );
		add_filter( 'myaccount_core_managed_templates', array( $this, 'register_managed_templates' ) );
		add_filter( 'yith_wcwl_wishlist_page_url', array( $this, 'filter_wishlist_page_url' ), 10, 2 );
		add_filter( 'yith_wcwl_wishlist_title', array( $this, 'filter_wishlist_title' ) );
		add_filter( 'yith_wcwl_show_wishlist_update_button', array( $this, 'filter_show_update_button' ), 10, 2 );
		add_filter( 'yith_wcwl_locate_template', array( $this, 'filter_yith_template_location' ), 10, 2 );
		add_filter( 'redirect_canonical', array( $this, 'filter_wishlist_canonical_redirect' ), 10, 2 );
	}

	public function add_wishlist_menu_item( array $items ): array {
		$items['wishlist'] = __( 'Wishlist', 'myaccount-core' );

		return $items;
	}

	public function add_wishlist_query_var( array $vars ): array {
		$vars['wishlist'] = 'wishlist';

		return $vars;
	}

	public function render_wishlist_endpoint(): void {
		wc_get_template( 'myaccount/wishlist.php' );
	}

	public function dequeue_yith_wishlist_styles(): void {
		if ( ! $this->is_wishlist_endpoint_request() ) {
			return;
		}

		wp_dequeue_style( 'yith-wcwl-main' );
		wp_dequeue_style( 'yith-wcwl-user-main' );
		wp_dequeue_style( 'yith-wcwl-theme' );
		wp_dequeue_style( 'jquery-selectBox' );
		wp_dequeue_style( 'yith-wcwl-font-awesome' );
	}

	public function register_managed_templates( array $templates ): array {
		$templates[] = 'myaccount/wishlist.php';

		return array_values( array_unique( $templates ) );
	}

	public function filter_wishlist_page_url( string $base_url, string $action ): string {
		if ( ! $this->is_wishlist_endpoint_request() ) {
			return $base_url;
		}

		$endpoint_url = wc_get_endpoint_url( 'wishlist', '', wc_get_page_permalink( 'myaccount' ) );

		if ( '' === $action || 'view' === $action ) {
			return $endpoint_url;
		}

		if ( 0 === strpos( $action, 'view/' ) ) {
			$wishlist_token = substr( $action, strlen( 'view/' ) );

			if ( '' !== $wishlist_token ) {
				return add_query_arg(
					array( 'wishlist_id' => rawurlencode( $wishlist_token ) ),
					$endpoint_url
				);
			}

			return $endpoint_url;
		}

		if ( 0 === strpos( $action, 'user/' ) ) {
			$user_id = substr( $action, strlen( 'user/' ) );

			if ( '' !== $user_id ) {
				return add_query_arg(
					array( 'user_id' => rawurlencode( $user_id ) ),
					$endpoint_url
				);
			}
		}

		return $base_url;
	}

	public function filter_yith_template_location( string $located, string $path ): string {
		if ( ! $this->is_wishlist_endpoint_request() ) {
			return $located;
		}

		if ( 'wishlist-view-images.php' !== $path ) {
			return $located;
		}

		$plugin_template = $this->plugin_dir . 'templates/yith-wcwl/' . $path;

		return is_readable( $plugin_template ) ? $plugin_template : $located;
	}

	public function filter_wishlist_title( string $title ): string {
		if ( $this->is_wishlist_endpoint_request() ) {
			return '';
		}

		return $title;
	}

	public function filter_show_update_button( bool $show_update, $wishlist ): bool {
		if ( $this->is_wishlist_endpoint_request() ) {
			return false;
		}

		return $show_update;
	}

	/**
	 * Keep YITH query-style pagination URLs (?paged=N) on My Account wishlist endpoint.
	 *
	 * @param string|false $redirect_url Canonical redirect URL.
	 * @param string       $requested_url Current requested URL.
	 * @return string|false
	 */
	public function filter_wishlist_canonical_redirect( $redirect_url, string $requested_url ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- WP filter signature.
		if ( $this->is_wishlist_endpoint_request() ) {
			return false;
		}

		return $redirect_url;
	}

	private function is_yith_wishlist_active(): bool {
		return defined( 'YITH_WCWL' ) || function_exists( 'YITH_WCWL' ) || class_exists( 'YITH_WCWL_Frontend' );
	}

	private function is_wishlist_endpoint_request(): bool {
		return function_exists( 'is_account_page' ) && is_account_page() && function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'wishlist' );
	}
}
