<?php

defined( 'ABSPATH' ) || exit;

class MyAccount_Core_Tracking_Adapter_Ast implements MyAccount_Core_Tracking_Adapter_Interface {
	public function get_provider_key(): string {
		return 'ast';
	}

	public function get_entries( WC_Order $order ): array {
		$entries = array();

		foreach ( $this->get_tracking_items( $order ) as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$tracking_number = sanitize_text_field( (string) ( $item['tracking_number'] ?? '' ) );
			$tracking_url    = $this->resolve_tracking_url( $item );

			if ( '' === $tracking_number || '' === $tracking_url ) {
				continue;
			}

			$normalized_status = $this->normalize_status_value( $item );
			$is_delivered      = in_array( $normalized_status, array( 'delivered', 'complete', 'completed' ), true );
			$is_partial_shipped = $this->is_partial_shipped( $item, $normalized_status );
			$is_in_transit     = ! $is_delivered;
			$status_label      = $this->resolve_status_label( $item, $normalized_status );
			$status_detail     = $this->resolve_status_detail( $item );

			$entries[] = MyAccount_Core_Tracking_Entry::from_array(
				array(
					'provider'        => $this->get_provider_key(),
					'carrier_name'    => $this->resolve_carrier_name( $item ),
					'tracking_number' => $tracking_number,
					'tracking_url'    => $tracking_url,
					'status_label'    => $status_label,
					'status_detail'   => $status_detail,
					'ship_date'       => $this->resolve_ship_date( $item ),
					'is_delivered'    => $is_delivered,
					'is_in_transit'   => $is_in_transit,
					'is_partial_shipped' => $is_partial_shipped,
				)
			);
		}

		return $entries;
	}

	public function suppress_view_order_output( WC_Order $order, array $entries ): void {
		unset( $order );

		if ( empty( $entries ) || ! function_exists( 'wc_advanced_shipment_tracking' ) ) {
			return;
		}

		$plugin = wc_advanced_shipment_tracking();

		if ( ! is_object( $plugin ) || ! isset( $plugin->actions ) || ! is_object( $plugin->actions ) ) {
			return;
		}

		if ( method_exists( $plugin->actions, 'show_tracking_info_order' ) ) {
			remove_action( 'woocommerce_view_order', array( $plugin->actions, 'show_tracking_info_order' ) );
		}
	}

	private function get_tracking_items( WC_Order $order ): array {
		$items = array();

		if ( function_exists( 'wc_advanced_shipment_tracking' ) ) {
			$plugin = wc_advanced_shipment_tracking();

			if ( is_object( $plugin ) && isset( $plugin->actions ) && is_object( $plugin->actions ) && method_exists( $plugin->actions, 'get_tracking_items' ) ) {
				$items = $plugin->actions->get_tracking_items( $order->get_id(), true );
			}
		}

		if ( ! is_array( $items ) || empty( $items ) ) {
			$items = $order->get_meta( '_wc_shipment_tracking_items', true );
		}

		return is_array( $items ) ? $items : array();
	}

	private function resolve_tracking_url( array $item ): string {
		$candidates = array(
			$item['ast_tracking_link'] ?? '',
			$item['tracking_link'] ?? '',
			$item['custom_tracking_link'] ?? '',
		);

		foreach ( $candidates as $candidate ) {
			$url = esc_url_raw( (string) $candidate );

			if ( '' !== $url ) {
				return $url;
			}
		}

		return '';
	}

	private function resolve_carrier_name( array $item ): string {
		$candidates = array(
			$item['formatted_tracking_provider'] ?? '',
			$item['tracking_provider'] ?? '',
			$item['custom_tracking_provider'] ?? '',
			$item['carrier_name'] ?? '',
		);

		foreach ( $candidates as $candidate ) {
			$name = sanitize_text_field( (string) $candidate );

			if ( '' !== $name ) {
				return $name;
			}
		}

		return __( 'Shipment tracking', 'myaccount-core' );
	}

	private function resolve_ship_date( array $item ): ?string {
		$value = $item['date_shipped'] ?? $item['ship_date'] ?? '';

		if ( '' === $value || null === $value ) {
			return null;
		}

		if ( is_numeric( $value ) ) {
			return date_i18n( get_option( 'date_format' ), (int) $value );
		}

		$timestamp = strtotime( (string) $value );

		if ( false !== $timestamp ) {
			return date_i18n( get_option( 'date_format' ), $timestamp );
		}

		$text = sanitize_text_field( (string) $value );

		return '' === $text ? null : $text;
	}

	private function resolve_status_label( array $item, string $normalized_status ): ?string {
		$candidates = array(
			$item['status_label'] ?? '',
			$item['tracking_status_name'] ?? '',
			$item['shipment_status_name'] ?? '',
			$item['status'] ?? '',
			$item['tracking_status'] ?? '',
			$item['shipment_status'] ?? '',
		);

		foreach ( $candidates as $candidate ) {
			$label = sanitize_text_field( (string) $candidate );

			if ( '' !== $label ) {
				return $this->humanize_status_label( $label );
			}
		}

		if ( 'delivered' === $normalized_status ) {
			return __( 'Delivered', 'myaccount-core' );
		}

		if ( $this->is_partial_shipped( $item, $normalized_status ) ) {
			return __( 'Partially Shipped', 'myaccount-core' );
		}

		return null;
	}

	private function resolve_status_detail( array $item ): ?string {
		$candidates = array(
			$item['status_detail'] ?? '',
			$item['status_message'] ?? '',
			$item['tracking_status_detail'] ?? '',
		);

		foreach ( $candidates as $candidate ) {
			$detail = sanitize_text_field( (string) $candidate );

			if ( '' !== $detail ) {
				return $detail;
			}
		}

		return null;
	}

	private function normalize_status_value( array $item ): string {
		if ( isset( $item['status_shipped'] ) ) {
			$status_shipped = (string) $item['status_shipped'];

			if ( '1' === $status_shipped ) {
				return 'shipped';
			}

			if ( '2' === $status_shipped ) {
				return 'partial_shipped';
			}
		}

		$candidates = array(
			$item['status'] ?? '',
			$item['tracking_status'] ?? '',
			$item['shipment_status'] ?? '',
			$item['status_label'] ?? '',
		);

		foreach ( $candidates as $candidate ) {
			$value = sanitize_key( str_replace( ' ', '_', strtolower( (string) $candidate ) ) );

			if ( '' !== $value ) {
				return $value;
			}
		}

		return '';
	}

	private function is_partial_shipped( array $item, string $normalized_status ): bool {
		if ( 'partial_shipped' === $normalized_status || 'partially_shipped' === $normalized_status ) {
			return true;
		}

		return isset( $item['status_shipped'] ) && '2' === (string) $item['status_shipped'];
	}

	private function humanize_status_label( string $value ): string {
		$value = str_replace( array( '-', '_' ), ' ', strtolower( $value ) );

		return ucwords( $value );
	}
}
