<?php

defined( 'ABSPATH' ) || exit;

class MyAccount_Core_Returns {
	public const META_KEY = '_myaccount_core_return_requests';

	private static ?MyAccount_Core_Returns $instance = null;

	public static function instance(): MyAccount_Core_Returns {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @return array<string, string>
	 */
	public function get_status_labels(): array {
		return array(
			'submitted' => __( 'Submitted', 'myaccount-core' ),
			'approved'  => __( 'Approved', 'myaccount-core' ),
			'rejected'  => __( 'Rejected', 'myaccount-core' ),
			'received'  => __( 'Received', 'myaccount-core' ),
			'completed' => __( 'Completed', 'myaccount-core' ),
		);
	}

	/**
	 * @return array<string, string>
	 */
	public function get_request_type_labels(): array {
		return array(
			'return'   => __( 'Return', 'myaccount-core' ),
			'exchange' => __( 'Exchange', 'myaccount-core' ),
		);
	}

	/**
	 * @return array<int, string>
	 */
	public function get_allowed_order_statuses(): array {
		$statuses = apply_filters( 'myaccount_core_returns_allowed_statuses', array( 'completed' ) );

		return array_values(
			array_filter(
				array_map( 'sanitize_key', is_array( $statuses ) ? $statuses : array() )
			)
		);
	}

	public function get_return_window_days(): int {
		$days = (int) apply_filters( 'myaccount_core_returns_window_days', 14 );

		return max( 1, $days );
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	public function get_requests( WC_Order $order ): array {
		$stored = $order->get_meta( self::META_KEY, true );

		if ( ! is_array( $stored ) ) {
			return array();
		}

		$normalized = array();

		foreach ( $stored as $request ) {
			if ( ! is_array( $request ) ) {
				continue;
			}

			$normalized[] = $this->normalize_request( $request );
		}

		return array_values( array_filter( $normalized ) );
	}

	/**
	 * @return array<string, mixed>|null
	 */
	public function get_request_by_id( WC_Order $order, string $request_id ): ?array {
		$request_id = sanitize_text_field( $request_id );

		if ( '' === $request_id ) {
			return null;
		}

		foreach ( $this->get_requests( $order ) as $request ) {
			if ( $request_id === (string) ( $request['id'] ?? '' ) ) {
				return $request;
			}
		}

		return null;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_policy_context( WC_Order $order ): array {
		$statuses      = $this->get_allowed_order_statuses();
		$reference_ts  = $this->get_reference_timestamp( $order );
		$window_days   = $this->get_return_window_days();
		$deadline_ts   = $reference_ts ? strtotime( '+' . $window_days . ' days', $reference_ts ) : null;
		$status        = $order->get_status();
		$status_ok     = in_array( $status, $statuses, true );
		$within_window = $reference_ts && $deadline_ts ? time() <= $deadline_ts : false;
		$eligible      = $status_ok && $within_window;

		$context = array(
			'is_eligible'        => $eligible,
			'allowed_statuses'   => $statuses,
			'window_days'        => $window_days,
			'reference_date'     => $reference_ts ? date_i18n( get_option( 'date_format' ), $reference_ts ) : '',
			'deadline_date'      => $deadline_ts ? date_i18n( get_option( 'date_format' ), $deadline_ts ) : '',
			'ineligibility_code' => '',
			'message'            => '',
		);

		if ( ! $status_ok ) {
			$context['ineligibility_code'] = 'order_status';
			$context['message']            = __( 'Returns are only available after the order is completed.', 'myaccount-core' );
		} elseif ( ! $within_window ) {
			$context['ineligibility_code'] = 'window_expired';
			$context['message']            = sprintf(
				/* translators: %d: return window in days */
				__( 'This order is outside the %d-day return window.', 'myaccount-core' ),
				$window_days
			);
		}

		return apply_filters( 'myaccount_core_returns_policy_context', $context, $order );
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	public function get_eligible_items( WC_Order $order ): array {
		$policy = $this->get_policy_context( $order );

		if ( empty( $policy['is_eligible'] ) ) {
			return array();
		}

		$items    = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
		$eligible = array();

		foreach ( $items as $item_id => $item ) {
			$product = $item->get_product();

			if ( ! $product instanceof WC_Product ) {
				continue;
			}

			if ( $this->is_item_excluded( $product, $item, $order ) ) {
				continue;
			}

			$remaining_qty = $this->get_returnable_quantity( $order, (int) $item_id );
			if ( $remaining_qty <= 0 ) {
				continue;
			}

			$eligible[] = array(
				'item_id'       => (int) $item_id,
				'product_id'    => $product->get_id(),
				'product_name'  => $item->get_name(),
				'ordered_qty'   => (int) $item->get_quantity(),
				'remaining_qty' => $remaining_qty,
				'image_html'    => $product->get_image( 'woocommerce_thumbnail', array( 'alt' => $item->get_name() ) ),
				'meta_inline'   => $this->get_item_meta_inline( $item, $order ),
			);
		}

		return array_values( $eligible );
	}

	public function order_allows_request_submission( WC_Order $order ): bool {
		$policy = $this->get_policy_context( $order );

		return ! empty( $policy['is_eligible'] ) && ! empty( $this->get_eligible_items( $order ) );
	}

	/**
	 * @param array<string, mixed> $payload Request payload from AJAX.
	 * @return array<string, mixed>|WP_Error
	 */
	public function create_request( WC_Order $order, array $payload ) {
		if ( ! $this->order_allows_request_submission( $order ) ) {
			return new WP_Error( 'returns_not_allowed', __( 'This order is not eligible for a return request.', 'myaccount-core' ) );
		}

		$request_type = sanitize_key( (string) ( $payload['requestType'] ?? '' ) );
		$reason       = sanitize_text_field( (string) ( $payload['reason'] ?? '' ) );
		$note         = sanitize_textarea_field( (string) ( $payload['note'] ?? '' ) );
		$items        = isset( $payload['items'] ) && is_array( $payload['items'] ) ? $payload['items'] : array();
		$type_labels  = $this->get_request_type_labels();
		$status_labels = $this->get_status_labels();

		if ( ! isset( $type_labels[ $request_type ] ) ) {
			return new WP_Error( 'invalid_request_type', __( 'Please choose a valid request type.', 'myaccount-core' ) );
		}

		if ( '' === $reason ) {
			return new WP_Error( 'missing_reason', __( 'Please tell us why you want to return or exchange these items.', 'myaccount-core' ) );
		}

		$eligible_items = array();
		foreach ( $this->get_eligible_items( $order ) as $item ) {
			$eligible_items[ (int) $item['item_id'] ] = $item;
		}

		$normalized_items = $this->normalize_requested_items( $items, $eligible_items );
		if ( is_wp_error( $normalized_items ) ) {
			return $normalized_items;
		}

		$requests    = $this->get_requests( $order );
		$new_request = array(
			'id'                 => wp_generate_uuid4(),
			'created_at'         => gmdate( 'c' ),
			'status'             => 'submitted',
			'status_label'       => $status_labels['submitted'],
			'request_type'       => $request_type,
			'request_type_label' => $type_labels[ $request_type ],
			'reason'             => $reason,
			'note'               => $note,
			'package_label'      => '',
			'items'              => $normalized_items,
		);

		array_unshift( $requests, $new_request );
		$order->update_meta_data( self::META_KEY, array_values( $requests ) );
		$order->save();

		$normalized_request = $this->normalize_request( $new_request );

		$this->add_request_order_note( $order, $normalized_request );

		/**
		 * Fires after a customer-facing return request is created and stored.
		 *
		 * @param WC_Order              $order              WooCommerce order.
		 * @param array<string, mixed>  $normalized_request Sanitized request payload as stored/displayed.
		 * @param array<string, mixed>  $payload            Original sanitized request payload passed to the service.
		 */
		do_action( 'myaccount_core_return_request_created', $order, $normalized_request, $payload );

		return $normalized_request;
	}

	/**
	 * @param array<string, mixed> $changes
	 * @return array<string, mixed>|WP_Error
	 */
	public function update_request( WC_Order $order, string $request_id, array $changes ) {
		$request_id      = sanitize_text_field( $request_id );
		$stored_requests = $order->get_meta( self::META_KEY, true );

		if ( '' === $request_id || ! is_array( $stored_requests ) ) {
			return new WP_Error( 'missing_request', __( 'We could not find that return request.', 'myaccount-core' ) );
		}

		$status_labels = $this->get_status_labels();
		$index         = null;

		foreach ( $stored_requests as $request_index => $request ) {
			if ( ! is_array( $request ) ) {
				continue;
			}

			if ( $request_id === sanitize_text_field( (string) ( $request['id'] ?? '' ) ) ) {
				$index = $request_index;
				break;
			}
		}

		if ( null === $index ) {
			return new WP_Error( 'missing_request', __( 'We could not find that return request.', 'myaccount-core' ) );
		}

		$previous_request = $this->normalize_request( $stored_requests[ $index ] );
		$next_request     = $stored_requests[ $index ];

		if ( isset( $changes['status'] ) ) {
			$status = sanitize_key( (string) $changes['status'] );
			if ( isset( $status_labels[ $status ] ) ) {
				$next_request['status'] = $status;
			}
		}

		if ( isset( $changes['package_label'] ) ) {
			$next_request['package_label'] = esc_url_raw( (string) $changes['package_label'] );
		}

		$next_status        = sanitize_key( (string) ( $next_request['status'] ?? '' ) );
		$previous_status    = sanitize_key( (string) ( $previous_request['status'] ?? '' ) );
		$next_package_label = esc_url_raw( (string) ( $next_request['package_label'] ?? '' ) );
		$prev_package_label = esc_url_raw( (string) ( $previous_request['package_label'] ?? '' ) );

		if ( $next_status === $previous_status && $next_package_label === $prev_package_label ) {
			return $previous_request;
		}

		$stored_requests[ $index ] = $next_request;
		$order->update_meta_data( self::META_KEY, array_values( $stored_requests ) );
		$order->save();

		$updated_request = $this->normalize_request( $next_request );

		$this->maybe_add_request_update_order_note( $order, $previous_request, $updated_request );

		/**
		 * Fires after a stored return request is updated from admin or another backend flow.
		 *
		 * @param WC_Order             $order            WooCommerce order.
		 * @param array<string, mixed> $updated_request  Sanitized updated request payload.
		 * @param array<string, mixed> $previous_request Sanitized request payload before update.
		 * @param array<string, mixed> $changes          Raw requested changes applied to the request.
		 */
		do_action( 'myaccount_core_return_request_updated', $order, $updated_request, $previous_request, $changes );

		return $updated_request;
	}

	public function user_owns_order( WC_Order $order, int $user_id ): bool {
		return $user_id > 0 && (int) $order->get_user_id() === $user_id;
	}

	public function get_returnable_quantity( WC_Order $order, int $item_id ): int {
		$item = $order->get_item( $item_id );
		if ( ! $item ) {
			return 0;
		}

		$ordered_qty  = (int) $item->get_quantity();
		$refunded_qty = absint( $order->get_qty_refunded_for_item( $item_id ) );
		$requested_qty = 0;

		foreach ( $this->get_requests( $order ) as $request ) {
			if ( 'rejected' === ( $request['status'] ?? '' ) ) {
				continue;
			}

			if ( empty( $request['items'] ) || ! is_array( $request['items'] ) ) {
				continue;
			}

			foreach ( $request['items'] as $requested_item ) {
				if ( (int) ( $requested_item['item_id'] ?? 0 ) === $item_id ) {
					$requested_qty += absint( $requested_item['qty'] ?? 0 );
				}
			}
		}

		return max( 0, $ordered_qty - $refunded_qty - $requested_qty );
	}

	/**
	 * @param array<string, mixed> $request Raw request data.
	 * @return array<string, mixed>
	 */
	private function normalize_request( array $request ): array {
		$status_labels = $this->get_status_labels();
		$type_labels   = $this->get_request_type_labels();
		$status        = sanitize_key( (string) ( $request['status'] ?? 'submitted' ) );
		$request_type  = sanitize_key( (string) ( $request['request_type'] ?? 'return' ) );
		$created_at    = (string) ( $request['created_at'] ?? '' );
		$items         = array();

		if ( isset( $request['items'] ) && is_array( $request['items'] ) ) {
			foreach ( $request['items'] as $item ) {
				if ( ! is_array( $item ) ) {
					continue;
				}

				$items[] = array(
					'item_id'      => absint( $item['item_id'] ?? 0 ),
					'qty'          => absint( $item['qty'] ?? 0 ),
					'product_name' => sanitize_text_field( (string) ( $item['product_name'] ?? '' ) ),
				);
			}
		}

		$timestamp = $created_at ? strtotime( $created_at ) : false;

		return array(
			'id'                 => sanitize_text_field( (string) ( $request['id'] ?? '' ) ),
			'created_at'         => $created_at,
			'created_at_label'   => $timestamp ? date_i18n( get_option( 'date_format' ), $timestamp ) : '',
			'status'             => isset( $status_labels[ $status ] ) ? $status : 'submitted',
			'status_label'       => $status_labels[ $status ] ?? $status_labels['submitted'],
			'request_type'       => isset( $type_labels[ $request_type ] ) ? $request_type : 'return',
			'request_type_label' => $type_labels[ $request_type ] ?? $type_labels['return'],
			'reason'             => sanitize_text_field( (string) ( $request['reason'] ?? '' ) ),
			'note'               => sanitize_textarea_field( (string) ( $request['note'] ?? '' ) ),
			'package_label'      => esc_url_raw( (string) ( $request['package_label'] ?? '' ) ),
			'items'              => array_values( $items ),
		);
	}

	/**
	 * @param array<int, mixed>                  $items
	 * @param array<int, array<string, mixed>>   $eligible_items
	 * @return array<int, array<string, mixed>>|WP_Error
	 */
	private function normalize_requested_items( array $items, array $eligible_items ) {
		$normalized_items = array();

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$item_id = isset( $item['item_id'] ) ? absint( $item['item_id'] ) : 0;
			$qty     = isset( $item['qty'] ) ? absint( $item['qty'] ) : 0;

			if ( $item_id <= 0 || $qty <= 0 ) {
				continue;
			}

			if ( ! isset( $eligible_items[ $item_id ] ) ) {
				return new WP_Error( 'invalid_item', __( 'One or more selected items cannot be returned.', 'myaccount-core' ) );
			}

			if ( ! isset( $normalized_items[ $item_id ] ) ) {
				$normalized_items[ $item_id ] = array(
					'item_id'      => $item_id,
					'qty'          => 0,
					'product_name' => (string) $eligible_items[ $item_id ]['product_name'],
				);
			}

			$normalized_items[ $item_id ]['qty'] += $qty;

			$remaining_qty = (int) $eligible_items[ $item_id ]['remaining_qty'];
			if ( (int) $normalized_items[ $item_id ]['qty'] > $remaining_qty ) {
				return new WP_Error( 'invalid_qty', __( 'Requested quantity exceeds what is still returnable for one of the items.', 'myaccount-core' ) );
			}
		}

		if ( empty( $normalized_items ) ) {
			return new WP_Error( 'missing_items', __( 'Please select at least one item to return or exchange.', 'myaccount-core' ) );
		}

		return array_values( $normalized_items );
	}

	/**
	 * @param array<string, mixed> $request
	 */
	private function add_request_order_note( WC_Order $order, array $request ): void {
		$item_summaries = array();

		foreach ( (array) ( $request['items'] ?? array() ) as $item ) {
			$product_name = sanitize_text_field( (string) ( $item['product_name'] ?? '' ) );
			$qty          = absint( $item['qty'] ?? 0 );

			if ( '' === $product_name || $qty <= 0 ) {
				continue;
			}

			$item_summaries[] = sprintf(
				/* translators: 1: product name, 2: requested quantity */
				__( '%1$s x %2$d', 'myaccount-core' ),
				$product_name,
				$qty
			);
		}

		$request_type = strtolower( wp_strip_all_tags( (string) ( $request['request_type_label'] ?? __( 'Return', 'myaccount-core' ) ) ) );
		$note_parts   = array(
			sprintf(
				/* translators: 1: request type, 2: requested items summary */
				__( 'Customer submitted a %1$s request for: %2$s.', 'myaccount-core' ),
				$request_type,
				implode( ', ', $item_summaries )
			),
			sprintf(
				/* translators: %s: customer-provided reason */
				__( 'Reason: %s', 'myaccount-core' ),
				sanitize_text_field( (string) ( $request['reason'] ?? '' ) )
			),
		);

		if ( ! empty( $request['note'] ) ) {
			$note_parts[] = sprintf(
				/* translators: %s: customer-provided note */
				__( 'Customer note: %s', 'myaccount-core' ),
				sanitize_textarea_field( (string) $request['note'] )
			);
		}

		$order->add_order_note( implode( ' ', array_filter( $note_parts ) ) );
	}

	/**
	 * @param array<string, mixed> $previous_request
	 * @param array<string, mixed> $updated_request
	 */
	private function maybe_add_request_update_order_note( WC_Order $order, array $previous_request, array $updated_request ): void {
		$note_parts = array();

		if ( (string) ( $previous_request['status'] ?? '' ) !== (string) ( $updated_request['status'] ?? '' ) ) {
			$note_parts[] = sprintf(
				/* translators: 1: previous status label, 2: updated status label */
				__( 'Return request status changed from %1$s to %2$s.', 'myaccount-core' ),
				(string) ( $previous_request['status_label'] ?? __( 'Unknown', 'myaccount-core' ) ),
				(string) ( $updated_request['status_label'] ?? __( 'Unknown', 'myaccount-core' ) )
			);
		}

		if ( (string) ( $previous_request['package_label'] ?? '' ) !== (string) ( $updated_request['package_label'] ?? '' ) && '' !== (string) ( $updated_request['package_label'] ?? '' ) ) {
			$note_parts[] = __( 'Return package label updated.', 'myaccount-core' );
		}

		if ( empty( $note_parts ) ) {
			return;
		}

		$order->add_order_note( implode( ' ', $note_parts ) );
	}

	private function get_reference_timestamp( WC_Order $order ): ?int {
		$date_completed = $order->get_date_completed();
		if ( $date_completed ) {
			return $date_completed->getTimestamp();
		}

		$date_paid = $order->get_date_paid();
		if ( $date_paid ) {
			return $date_paid->getTimestamp();
		}

		$date_created = $order->get_date_created();
		if ( $date_created ) {
			return $date_created->getTimestamp();
		}

		return null;
	}

	private function is_item_excluded( WC_Product $product, WC_Order_Item_Product $item, WC_Order $order ): bool {
		if ( $product->is_downloadable() ) {
			return true;
		}

		$product_type = $product->get_type();
		$excluded     = array( 'gift-card', 'gift_card' );
		$excluded     = apply_filters( 'myaccount_core_returns_excluded_product_types', $excluded, $order, $item, $product );

		if ( in_array( $product_type, $excluded, true ) ) {
			return true;
		}

		return (bool) apply_filters( 'myaccount_core_returns_item_is_excluded', false, $order, $item, $product );
	}

	private function get_item_meta_inline( WC_Order_Item_Product $item, WC_Order $order ): string {
		$meta_parts = array();

		foreach ( $item->get_all_formatted_meta_data() as $meta ) {
			$key = isset( $meta->display_key ) ? wp_strip_all_tags( (string) $meta->display_key ) : '';
			$val = isset( $meta->display_value ) ? wp_strip_all_tags( (string) $meta->display_value ) : '';
			$key = trim( $key );
			$val = trim( $val );

			if ( $key && $val ) {
				$meta_parts[] = $key . ': ' . $val;
			} elseif ( $val ) {
				$meta_parts[] = $val;
			}
		}

		return (string) apply_filters( 'myaccount_core_order_item_meta_inline', implode( ', ', array_filter( $meta_parts ) ), $item, $order );
	}
}
