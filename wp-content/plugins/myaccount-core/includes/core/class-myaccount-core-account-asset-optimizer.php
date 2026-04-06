<?php
/**
 * Dequeue third-party CSS/JS on WooCommerce My Account only (not cart, shop, etc.).
 *
 * @package MyAccount_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Account-scoped asset optimization.
 */
class MyAccount_Core_Account_Asset_Optimizer {

	private static ?MyAccount_Core_Account_Asset_Optimizer $instance = null;

	public static function instance(): MyAccount_Core_Account_Asset_Optimizer {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_filter( 'woocommerce_enqueue_styles', array( $this, 'filter_woocommerce_enqueue_styles' ), 10, 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_third_party_assets' ), 200 );
		// Disable WordPress emoji scripts/styles on My Account only (scope-safe).
		add_action( 'wp', array( $this, 'maybe_disable_emoji_on_account' ), 1 );
		// WooCommerce may enqueue wc-blocks-style on wp_head (e.g. Notices::enqueue_notice_styles) after wp_print_styles — dequeue again.
		add_action( 'wp_head', array( $this, 'dequeue_third_party_styles_after_late_enqueues' ), 99 );
		add_action( 'wp_footer', array( $this, 'dequeue_third_party_styles_after_late_enqueues' ), 1 );
		// Scripts may be enqueued late; strip before wp_print_footer_scripts (priority 20).
		add_action( 'wp_footer', array( $this, 'dequeue_third_party_scripts_after_late_enqueues' ), 19 );
		// Block output even if something enqueues after our dequeue (late wp_head / footer).
		add_filter( 'style_loader_src', array( $this, 'block_third_party_style_src' ), 20, 2 );
		add_filter( 'script_loader_src', array( $this, 'block_third_party_script_src' ), 20, 2 );
	}

	/**
	 * Whether optimization runs on this request. Scoped to My Account in each callback.
	 */
	private function is_optimizer_enabled(): bool {
		if ( defined( 'MYACCOUNT_CORE_DISABLE_ACCOUNT_ASSET_OPTIMIZER' ) && MYACCOUNT_CORE_DISABLE_ACCOUNT_ASSET_OPTIMIZER ) {
			return false;
		}

		return (bool) apply_filters( 'myaccount_core_account_asset_optimizer_enabled', true );
	}

	/**
	 * Remove Woo layout/smallscreen on My Account only.
	 *
	 * @param array|false $styles Styles passed through woocommerce_enqueue_styles.
	 * @return array|false
	 */
	public function filter_woocommerce_enqueue_styles( $styles ) {
		if ( ! $this->is_optimizer_enabled() || ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
			return $styles;
		}

		if ( ! is_array( $styles ) ) {
			return $styles;
		}

		unset( $styles['woocommerce-layout'], $styles['woocommerce-smallscreen'] );

		return $styles;
	}

	/**
	 * Whether this request is My Account and optimizer should run.
	 */
	private function should_optimize_account_assets(): bool {
		return $this->is_optimizer_enabled() && function_exists( 'is_account_page' ) && is_account_page();
	}

	/**
	 * Disable WordPress emoji scripts/styles on My Account only.
	 *
	 * Keeps plugin footprint outside My Account.
	 *
	 * @return void
	 */
	public function maybe_disable_emoji_on_account(): void {
		if ( ! $this->should_optimize_account_assets() ) {
			return;
		}

		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );

		// Prevent loading emoji SVGs if some content still attempts to render them.
		add_filter( 'emoji_svg_url', '__return_false' );
	}

	/**
	 * Dequeue handles after Woo and other plugins have enqueued.
	 */
	public function dequeue_third_party_assets(): void {
		if ( ! $this->should_optimize_account_assets() ) {
			return;
		}

		$this->dequeue_registered_styles();
		$this->dequeue_registered_scripts();
	}

	/**
	 * Second pass for styles enqueued on wp_head / wp_footer after wp_enqueue_scripts.
	 */
	public function dequeue_third_party_styles_after_late_enqueues(): void {
		if ( ! $this->should_optimize_account_assets() ) {
			return;
		}

		$this->dequeue_registered_styles();
	}

	/**
	 * Second pass for scripts enqueued after wp_enqueue_scripts priority 200.
	 */
	public function dequeue_third_party_scripts_after_late_enqueues(): void {
		if ( ! $this->should_optimize_account_assets() ) {
			return;
		}

		$this->dequeue_registered_scripts();
	}

	/**
	 * @return void
	 */
	private function dequeue_registered_styles(): void {
		foreach ( $this->get_style_handles_to_dequeue() as $handle ) {
			wp_dequeue_style( $handle );
		}
	}

	/**
	 * @return void
	 */
	private function dequeue_registered_scripts(): void {
		foreach ( $this->get_script_handles_to_dequeue() as $handle ) {
			wp_dequeue_script( $handle );
		}
	}

	/**
	 * Prevent late-enqueued styles from printing (e.g. wc-blocks-style on wp_head).
	 *
	 * @param string|false $src   Source URL.
	 * @param string       $handle Style handle.
	 * @return string|false
	 */
	public function block_third_party_style_src( $src, $handle ) {
		if ( ! $this->should_optimize_account_assets() ) {
			return $src;
		}

		if ( ! is_string( $handle ) || '' === $handle ) {
			return $src;
		}

		if ( in_array( $handle, $this->get_style_handles_to_dequeue(), true ) ) {
			return false;
		}

		return $src;
	}

	/**
	 * Prevent late-enqueued scripts from printing when dequeue order is wrong.
	 *
	 * @param string|false $src    Source URL.
	 * @param string       $handle Script handle.
	 * @return string|false
	 */
	public function block_third_party_script_src( $src, $handle ) {
		if ( ! $this->should_optimize_account_assets() ) {
			return $src;
		}

		if ( ! is_string( $handle ) || '' === $handle ) {
			return $src;
		}

		if ( in_array( $handle, $this->get_script_handles_to_dequeue(), true ) ) {
			return false;
		}

		return $src;
	}

	/**
	 * Default style handles to dequeue on My Account.
	 *
	 * @return string[]
	 */
	private function get_default_style_handles(): array {
		return array(
			'wp-block-library',
			'wp-block-library-theme',
			'classic-theme-styles',
			'wc-blocks-style',
			'wc-blocks-packages-style',
			'select2',
			'woocommerce_prettyPhoto_css',
			'yith-wcwl-main',
			'yith-wcwl-user-main',
			'yith-wcwl-theme',
			'jquery-selectBox',
			'yith-wcwl-font-awesome',
			'woo-variation-swatches',
		);
	}

	/**
	 * WooCommerce Cart / Checkout **Blocks** runtime (React, wc-cart-checkout-*, wc-blocks-*).
	 * Safe to dequeue on My Account when no block on the page needs this stack (see guard below).
	 *
	 * Handles follow WooCommerce core `src/Blocks` registration (WC 9.x).
	 *
	 * @return string[]
	 */
	private function get_wc_blocks_cart_checkout_script_handles(): array {
		return array(
			'wc-cart-checkout-vendors',
			'wc-cart-checkout-base',
			'wc-blocks-checkout',
			'wc-blocks-components',
			'wc-blocks-data-store',
			'wc-blocks-registry',
			'wc-blocks-middleware',
			'wc-blocks-shared-context',
			'wc-blocks-shared-hocs',
			'wc-types',
			'wc-price-format',
			'wc-cart-block-frontend',
			'wc-checkout-block-frontend',
			'wc-blocks-vendors',
			'wc-blocks',
			'wc-blocks-frontend-vendors',
		);
	}

	/**
	 * If any of these block frontends is queued, keep the shared WC cart/checkout runtime
	 * (e.g. Mini Cart in the header still works on My Account).
	 *
	 * Filter `myaccount_core_account_needs_wc_cart_checkout_runtime`: return true to force keep,
	 * false to force strip, null (default) to auto-detect from queued scripts.
	 *
	 * @return bool
	 */
	private function account_page_needs_wc_cart_checkout_runtime(): bool {
		$override = apply_filters( 'myaccount_core_account_needs_wc_cart_checkout_runtime', null );
		if ( true === $override ) {
			return true;
		}
		if ( false === $override ) {
			return false;
		}

		$guard_handles = array(
			'wc-mini-cart-block-frontend',
			'wc-cart-block-frontend',
			'wc-checkout-block-frontend',
		);

		foreach ( $guard_handles as $handle ) {
			if ( wp_script_is( $handle, 'enqueued' ) ) {
				return true;
			}
		}

		$wp_scripts = wp_scripts();
		if ( $wp_scripts && ! empty( $wp_scripts->queue ) ) {
			foreach ( $guard_handles as $handle ) {
				if ( in_array( $handle, $wp_scripts->queue, true ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Default script handles to dequeue on My Account.
	 *
	 * Order attribution (`sourcebuster-js`, `wc-order-attribution`) is left loaded so storefront tracking is unchanged.
	 * WooCommerce Cart/Checkout **Blocks** scripts are removed only when no Mini Cart / Cart / Checkout block
	 * frontend is on the page (often true for classic My Account templates).
	 *
	 * @return string[]
	 */
	private function get_default_script_handles(): array {
		$handles = array(
			'selectWoo',
			'select2',
			'prettyPhoto',
			'prettyPhoto-init',
			'woo-variation-swatches',
			'wc-add-to-cart',
		);

		if ( ! $this->account_page_needs_wc_cart_checkout_runtime() ) {
			$handles = array_merge( $handles, $this->get_wc_blocks_cart_checkout_script_handles() );
		}

		return $handles;
	}

	/**
	 * @return string[]
	 */
	private function get_style_handles_to_dequeue(): array {
		$handles = $this->get_default_style_handles();
		$handles = apply_filters( 'myaccount_core_account_dequeue_style_handles', $handles );

		if ( ! is_array( $handles ) ) {
			return array();
		}

		$handles = array_map( 'strval', $handles );
		$handles = array_filter( array_unique( $handles ) );

		return array_values( $handles );
	}

	/**
	 * @return string[]
	 */
	private function get_script_handles_to_dequeue(): array {
		$handles = $this->get_default_script_handles();
		$handles = apply_filters( 'myaccount_core_account_dequeue_script_handles', $handles );

		if ( ! is_array( $handles ) ) {
			return array();
		}

		$handles = array_map( 'strval', $handles );
		$handles = array_filter( array_unique( $handles ) );

		return array_values( $handles );
	}
}
