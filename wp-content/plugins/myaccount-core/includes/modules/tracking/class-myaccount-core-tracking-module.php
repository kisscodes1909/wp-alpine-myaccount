<?php

defined( 'ABSPATH' ) || exit;

class MyAccount_Core_Tracking_Module {
	private static ?MyAccount_Core_Tracking_Module $instance = null;
	private MyAccount_Core_Tracking_Resolver $resolver;
	private bool $enabled = true;

	public static function instance(): MyAccount_Core_Tracking_Module {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function is_enabled(): bool {
		return (bool) apply_filters( 'myaccount_core_tracking_module_enabled', true );
	}

	private function __construct() {
		$this->enabled  = self::is_enabled();
		$this->resolver = MyAccount_Core_Tracking_Resolver::instance();

		if ( ! $this->enabled ) {
			return;
		}

		add_filter( 'myaccount_core_managed_templates', array( $this, 'register_managed_templates' ) );
	}

	/**
	 * @return array<int, MyAccount_Core_Tracking_Entry>
	 */
	public function get_entries( WC_Order $order ): array {
		if ( ! $this->enabled ) {
			return array();
		}

		return $this->resolver->get_entries( $order );
	}

	public function maybe_suppress_view_order_output( WC_Order $order ): void {
		if ( ! $this->enabled ) {
			return;
		}

		$this->resolver->maybe_suppress_view_order_output( $order );
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_timeline_context( WC_Order $order ): array {
		if ( ! $this->enabled ) {
			return array();
		}

		return $this->resolver->get_timeline_context( $order );
	}

	public function register_managed_templates( array $templates ): array {
		if ( ! $this->enabled ) {
			return $templates;
		}

		$templates[] = 'order/order-tracking-block.php';

		return array_values( array_unique( $templates ) );
	}
}
