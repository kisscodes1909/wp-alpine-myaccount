<?php

defined( 'ABSPATH' ) || exit;

class MyAccount_Core_Tracking_Resolver {
	private static ?MyAccount_Core_Tracking_Resolver $instance = null;

	/** @var array<int, MyAccount_Core_Tracking_Adapter_Interface> */
	private array $adapters = array();

	/** @var array<int, array<int, MyAccount_Core_Tracking_Entry>> */
	private array $entries_cache = array();

	/** @var array<int, string|null> */
	private array $provider_cache = array();

	/** @var array<int, array<string, mixed>> */
	private array $timeline_cache = array();

	public static function instance(): MyAccount_Core_Tracking_Resolver {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->adapters = array(
			new MyAccount_Core_Tracking_Adapter_Ast(),
		);
	}

	/**
	 * @return array<int, MyAccount_Core_Tracking_Entry>
	 */
	public function get_entries( WC_Order $order ): array {
		$order_id = $order->get_id();

		if ( isset( $this->entries_cache[ $order_id ] ) ) {
			return $this->entries_cache[ $order_id ];
		}

		$entries       = array();
		$provider_key  = null;

		foreach ( $this->adapters as $adapter ) {
			$entries = $this->normalize_entries( $adapter->get_entries( $order ) );

			if ( ! empty( $entries ) ) {
				$provider_key = $adapter->get_provider_key();
				break;
			}
		}

		$entries = $this->normalize_entries(
			apply_filters(
				'myaccount_core_tracking_entries',
				$entries,
				$order,
				$provider_key
			)
		);

		$this->entries_cache[ $order_id ]   = $entries;
		$this->provider_cache[ $order_id ]  = $provider_key;

		return $entries;
	}

	public function maybe_suppress_view_order_output( WC_Order $order ): void {
		$order_id     = $order->get_id();
		$entries      = $this->get_entries( $order );
		$provider_key = $this->provider_cache[ $order_id ] ?? null;

		if ( empty( $entries ) || ! $provider_key ) {
			return;
		}

		foreach ( $this->adapters as $adapter ) {
			if ( $adapter->get_provider_key() === $provider_key ) {
				$adapter->suppress_view_order_output( $order, $entries );
				return;
			}
		}
	}

	public function get_timeline_context( WC_Order $order ): array {
		$order_id = $order->get_id();

		if ( isset( $this->timeline_cache[ $order_id ] ) ) {
			return $this->timeline_cache[ $order_id ];
		}

		$status  = $order->get_status();
		$entries = $this->get_entries( $order );
		$context = array(
			'mode'           => 'woocommerce',
			'step_count'     => 3,
			'current_step'   => $this->resolve_woocommerce_step( $status, $order ),
			'current_key'    => $this->resolve_woocommerce_step_key( $status ),
			'has_tracking'   => ! empty( $entries ),
			'all_delivered'  => false,
			'has_partial_shipment' => false,
			'latest_ship_date' => null,
		);

		if ( ! empty( $entries ) && ! in_array( $status, array( 'cancelled', 'failed', 'refunded' ), true ) ) {
			$all_delivered = $this->all_entries_delivered( $entries ) || $this->is_order_marked_delivered( $status );

			$context = array(
				'mode'             => 'tracking',
				'step_count'       => 4,
				'current_step'     => $all_delivered ? 4 : 3,
				'current_key'      => $all_delivered ? 'delivered' : 'shipped',
				'has_tracking'     => true,
				'all_delivered'    => $all_delivered,
				'has_partial_shipment' => $this->has_partial_shipment( $entries ),
				'latest_ship_date' => $this->get_latest_ship_date( $entries ),
			);
		}

		$context = apply_filters( 'myaccount_core_order_status_card_timeline_context', $context, $order, $entries );

		$context['step_count']   = max( 1, (int) ( $context['step_count'] ?? 3 ) );
		$context['current_step'] = min( $context['step_count'], max( 1, (int) ( $context['current_step'] ?? 1 ) ) );

		$this->timeline_cache[ $order_id ] = $context;

		return $context;
	}

	/**
	 * @param array<int, MyAccount_Core_Tracking_Entry|array<string, mixed>> $entries Entries.
	 * @return array<int, MyAccount_Core_Tracking_Entry>
	 */
	private function normalize_entries( array $entries ): array {
		$normalized = array();

		foreach ( $entries as $entry ) {
			if ( $entry instanceof MyAccount_Core_Tracking_Entry ) {
				if ( $entry->has_tracking_url() ) {
					$normalized[] = $entry;
				}
				continue;
			}

			if ( is_array( $entry ) ) {
				$item = MyAccount_Core_Tracking_Entry::from_array( $entry );

				if ( $item->has_tracking_url() ) {
					$normalized[] = $item;
				}
			}
		}

		return array_values( $normalized );
	}

	private function resolve_woocommerce_step( string $status, WC_Order $order ): int {
		if ( in_array( $status, array( 'cancelled', 'failed', 'pending', 'on-hold' ), true ) ) {
			return 1;
		}

		if ( 'processing' === $status ) {
			return 2;
		}

		if ( in_array( $status, array( 'completed', 'refunded' ), true ) ) {
			return 3;
		}

		$current_step = (int) apply_filters( 'myaccount_core_order_status_card_timeline_step', 2, $status, $order );

		return min( 3, max( 1, $current_step ) );
	}

	private function resolve_woocommerce_step_key( string $status ): string {
		if ( in_array( $status, array( 'cancelled', 'failed', 'pending', 'on-hold' ), true ) ) {
			return 'placed';
		}

		if ( 'processing' === $status ) {
			return 'processing';
		}

		if ( in_array( $status, array( 'completed', 'refunded' ), true ) ) {
			return 'complete';
		}

		return 'processing';
	}

	/**
	 * @param array<int, MyAccount_Core_Tracking_Entry> $entries Tracking entries.
	 */
	private function all_entries_delivered( array $entries ): bool {
		if ( empty( $entries ) ) {
			return false;
		}

		foreach ( $entries as $entry ) {
			if ( ! $entry->is_delivered ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param array<int, MyAccount_Core_Tracking_Entry> $entries Tracking entries.
	 */
	private function get_latest_ship_date( array $entries ): ?string {
		$dates = array();

		foreach ( $entries as $entry ) {
			if ( $entry->ship_date ) {
				$dates[] = $entry->ship_date;
			}
		}

		if ( empty( $dates ) ) {
			return null;
		}

		return end( $dates );
	}

	/**
	 * @param array<int, MyAccount_Core_Tracking_Entry> $entries Tracking entries.
	 */
	private function has_partial_shipment( array $entries ): bool {
		foreach ( $entries as $entry ) {
			if ( $entry->is_partial_shipped ) {
				return true;
			}
		}

		return false;
	}

	private function is_order_marked_delivered( string $status ): bool {
		$delivered_statuses = apply_filters(
			'myaccount_core_tracking_delivered_order_statuses',
			array(
				'completed',
				'delivered',
				'wc-delivered',
			)
		);

		return in_array( $status, $delivered_statuses, true );
	}

}
