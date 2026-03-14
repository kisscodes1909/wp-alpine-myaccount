<?php

defined( 'ABSPATH' ) || exit;

class MyAccount_Core_Template_Loader {
	private static ?MyAccount_Core_Template_Loader $instance = null;
	private string $plugin_dir;
	private array $managed_templates = array(
		'myaccount/my-account.php',
		'myaccount/navigation.php',
		'myaccount/page-heading.php',
		'myaccount/form-login.php',
		'myaccount/form-lost-password.php',
		'myaccount/form-reset-password.php',
		'myaccount/orders.php',
		'myaccount/view-order.php',
		'myaccount/form-edit-account.php',
		'myaccount/payment-methods.php',
		'myaccount/form-add-payment-method.php',
		'myaccount/apl-address.php',
		'myaccount/partials/ma-empty-state.php',
		'myaccount/ma-form-edit-address.php',
		'myaccount/ma-form-edit-change-password.php',
		'order/order-meta-data.php',
		'order/order-actions.php',
		'order/order-list-item-content.php',
		'order/order-total.php',
		'order/order-details-header.php',
		'order/order-status-card.php',
		'order/order-details-items-summary.php',
		'order/order-details-item.php',
		'order/order-details.php',
		'order/order-again.php',
		'ui/apl-toast.php',
		'ui/apl-popup.php',
		'ui/apl-loader.php',
	);

	public static function instance( string $plugin_dir ): MyAccount_Core_Template_Loader {
		if ( null === self::$instance ) {
			self::$instance = new self( $plugin_dir );
		}

		return self::$instance;
	}

	private function __construct( string $plugin_dir ) {
		$this->plugin_dir = trailingslashit( $plugin_dir );
		add_filter( 'woocommerce_locate_template', array( $this, 'locate_template' ), 20, 3 );
	}

	public function locate_template( string $template, string $template_name, string $template_path ): string {
		$normalized = ltrim( $template_name, '/' );

		if ( ! in_array( $normalized, $this->managed_templates, true ) ) {
			return $template;
		}

		$plugin_template = $this->plugin_dir . 'templates/woocommerce/' . $normalized;
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}

		return $template;
	}
}
