<?php

defined( 'ABSPATH' ) || exit;

interface MyAccount_Core_Tracking_Adapter_Interface {
	public function get_provider_key(): string;

	/**
	 * @param WC_Order $order Order object.
	 * @return array<int, MyAccount_Core_Tracking_Entry>
	 */
	public function get_entries( WC_Order $order ): array;

	/**
	 * @param WC_Order                                     $order   Order object.
	 * @param array<int, MyAccount_Core_Tracking_Entry>    $entries Tracking entries.
	 */
	public function suppress_view_order_output( WC_Order $order, array $entries ): void;
}
