<?php

defined( 'ABSPATH' ) || exit;

class MyAccount_Core_Tracking_Entry {
	public string $provider;
	public string $carrier_name;
	public string $tracking_number;
	public string $tracking_url;
	public ?string $status_label;
	public ?string $status_detail;
	public ?string $ship_date;
	public bool $is_delivered;
	public bool $is_in_transit;
	public bool $is_partial_shipped;

	public function __construct( array $args = array() ) {
		$this->provider        = sanitize_key( (string) ( $args['provider'] ?? '' ) );
		$this->carrier_name    = sanitize_text_field( (string) ( $args['carrier_name'] ?? '' ) );
		$this->tracking_number = sanitize_text_field( (string) ( $args['tracking_number'] ?? '' ) );
		$this->tracking_url    = $this->sanitize_url_value( $args['tracking_url'] ?? '' );
		$this->status_label    = $this->sanitize_nullable_text( $args['status_label'] ?? null );
		$this->status_detail   = $this->sanitize_nullable_text( $args['status_detail'] ?? null );
		$this->ship_date       = $this->sanitize_nullable_text( $args['ship_date'] ?? null );
		$this->is_delivered    = ! empty( $args['is_delivered'] );
		$this->is_in_transit   = ! empty( $args['is_in_transit'] );
		$this->is_partial_shipped = ! empty( $args['is_partial_shipped'] );
	}

	public static function from_array( array $args ): MyAccount_Core_Tracking_Entry {
		return new self( $args );
	}

	public function has_tracking_url(): bool {
		return '' !== $this->tracking_url;
	}

	public function to_array(): array {
		return array(
			'provider'        => $this->provider,
			'carrier_name'    => $this->carrier_name,
			'tracking_number' => $this->tracking_number,
			'tracking_url'    => $this->tracking_url,
			'status_label'    => $this->status_label,
			'status_detail'   => $this->status_detail,
			'ship_date'       => $this->ship_date,
			'is_delivered'    => $this->is_delivered,
			'is_in_transit'   => $this->is_in_transit,
			'is_partial_shipped' => $this->is_partial_shipped,
		);
	}

	private function sanitize_nullable_text( $value ): ?string {
		$value = null === $value ? null : sanitize_text_field( (string) $value );

		return '' === $value ? null : $value;
	}

	private function sanitize_url_value( $value ): string {
		$url = esc_url_raw( (string) $value );

		return is_string( $url ) ? $url : '';
	}
}
