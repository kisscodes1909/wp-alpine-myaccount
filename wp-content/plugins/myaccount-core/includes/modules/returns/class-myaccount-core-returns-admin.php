<?php

defined( 'ABSPATH' ) || exit;

class MyAccount_Core_Returns_Admin {
	private const META_BOX_ID   = 'myaccount-core-return-requests';
	private const NONCE_ACTION  = 'myaccount_core_save_return_requests';
	private const NONCE_NAME    = '_myaccount_core_return_requests_nonce';
	private static ?MyAccount_Core_Returns_Admin $instance = null;

	/** @var array<string, bool> */
	private array $registered_screens = array();

	public static function instance(): MyAccount_Core_Returns_Admin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'add_meta_boxes', array( $this, 'register_meta_box' ), 20, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		if ( function_exists( 'wc_get_page_screen_id' ) ) {
			add_action( 'add_meta_boxes_' . wc_get_page_screen_id( 'shop-order' ), array( $this, 'register_hpos_meta_box' ), 20, 1 );
		}

		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_meta_box' ), 20, 2 );
	}

	/**
	 * @param string               $screen_id
	 * @param WP_Post|WC_Order|null $post_or_order
	 */
	public function register_meta_box( string $screen_id, $post_or_order = null ): void {
		if ( ! in_array( $screen_id, $this->get_order_screen_ids(), true ) ) {
			return;
		}

		$this->add_meta_box_to_screen( $screen_id );
	}

	public function register_hpos_meta_box( WC_Order $order ): void {
		if ( ! function_exists( 'wc_get_page_screen_id' ) ) {
			return;
		}

		$this->add_meta_box_to_screen( wc_get_page_screen_id( 'shop-order' ) );
	}

	private function add_meta_box_to_screen( string $screen_id ): void {
		if ( isset( $this->registered_screens[ $screen_id ] ) ) {
			return;
		}

		add_meta_box(
			self::META_BOX_ID,
			__( 'Return Requests', 'myaccount-core' ),
			array( $this, 'render_meta_box' ),
			$screen_id,
			'side',
			'default'
		);

		$this->registered_screens[ $screen_id ] = true;
	}

	/**
	 * @param WP_Post|WC_Order $post_or_order
	 */
	public function render_meta_box( $post_or_order ): void {
		$order = $this->resolve_order( $post_or_order );

		if ( ! $order instanceof WC_Order ) {
			echo '<p>' . esc_html__( 'We could not load return requests for this order.', 'myaccount-core' ) . '</p>';
			return;
		}

		$returns = MyAccount_Core_Returns_Service::instance();
		$requests = $returns->get_requests( $order );

		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );

		echo '<p>' . esc_html__( 'Manage customer return requests for this order.', 'myaccount-core' ) . '</p>';

		if ( empty( $requests ) ) {
			echo '<p>' . esc_html__( 'No return requests have been submitted for this order yet.', 'myaccount-core' ) . '</p>';
			return;
		}

		foreach ( $requests as $request ) {
			$request_id = sanitize_text_field( (string) ( $request['id'] ?? '' ) );

			if ( '' === $request_id ) {
				continue;
			}

			$this->render_request_card( $request );
		}
	}

	public function enqueue_admin_assets(): void {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( ! $screen || ! in_array( $screen->id, $this->get_order_screen_ids(), true ) ) {
			return;
		}

		$css = '
			.acf-field[data-name="order_return_request"],
			[data-name="order_return_request"].acf-field,
			[data-name="order_return_request"] .acf-row-handle,
			[data-name="order_return_request"] .acf-actions {
				display: none !important;
			}
		';

		wp_register_style( 'myaccount-core-returns-admin', false, array(), '1.0.0' );
		wp_enqueue_style( 'myaccount-core-returns-admin' );
		wp_add_inline_style( 'myaccount-core-returns-admin', $css );
	}

	public function save_meta_box( int $order_id, $order = null ): void {
		$nonce = isset( $_POST[ self::NONCE_NAME ] ) ? sanitize_text_field( wp_unslash( $_POST[ self::NONCE_NAME ] ) ) : '';

		if ( '' === $nonce || ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_shop_orders' ) && ! current_user_can( 'edit_post', $order_id ) ) {
			return;
		}

		$order = $this->resolve_order( $order ?: $order_id );

		if ( ! $order instanceof WC_Order ) {
			return;
		}

		$updates_raw         = isset( $_POST['myaccount_core_return_updates'] ) ? wp_unslash( $_POST['myaccount_core_return_updates'] ) : array();
		$approve_request_id  = isset( $_POST['myaccount_core_approve_return_request'] ) ? sanitize_text_field( wp_unslash( $_POST['myaccount_core_approve_return_request'] ) ) : '';
		$returns             = MyAccount_Core_Returns_Service::instance();

		if ( ! is_array( $updates_raw ) && '' === $approve_request_id ) {
			return;
		}

		foreach ( is_array( $updates_raw ) ? $updates_raw : array() as $request_id => $update ) {
			if ( ! is_array( $update ) ) {
				continue;
			}

			$request_id = sanitize_text_field( (string) $request_id );

			if ( '' === $request_id ) {
				continue;
			}

			$changes = array(
				'status'        => isset( $update['status'] ) ? sanitize_key( (string) $update['status'] ) : '',
				'package_label' => isset( $update['package_label'] ) ? esc_url_raw( (string) $update['package_label'] ) : '',
			);

			if ( $approve_request_id === $request_id ) {
				$changes['status'] = 'approved';
			}

			$previous_request = $returns->get_request_by_id( $order, $request_id );
			$result           = $returns->update_request( $order, $request_id, $changes );

			if ( is_wp_error( $result ) || ! is_array( $previous_request ) ) {
				continue;
			}

			if ( 'approved' === (string) ( $result['status'] ?? '' ) && 'approved' !== (string) ( $previous_request['status'] ?? '' ) ) {
				/**
				 * Fires when an admin approves a return request from the Woo order screen.
				 *
				 * @param WC_Order             $order            WooCommerce order.
				 * @param array<string, mixed> $approved_request Sanitized approved request payload.
				 * @param array<string, mixed> $previous_request Sanitized request payload before approval.
				 */
				do_action( 'myaccount_core_return_request_approved', $order, $result, $previous_request );
			}
		}
	}

	/**
	 * @param array<string, mixed> $request
	 */
	private function render_request_card( array $request ): void {
		$request_id     = sanitize_text_field( (string) ( $request['id'] ?? '' ) );
		$status         = sanitize_key( (string) ( $request['status'] ?? 'submitted' ) );
		$status_label   = sanitize_text_field( (string) ( $request['status_label'] ?? '' ) );
		$type_label     = sanitize_text_field( (string) ( $request['request_type_label'] ?? '' ) );
		$created_label  = sanitize_text_field( (string) ( $request['created_at_label'] ?? '' ) );
		$reason         = sanitize_text_field( (string) ( $request['reason'] ?? '' ) );
		$note           = sanitize_textarea_field( (string) ( $request['note'] ?? '' ) );
		$package_label  = esc_url( (string) ( $request['package_label'] ?? '' ) );
		$status_options = MyAccount_Core_Returns_Service::instance()->get_status_labels();

		echo '<div style="margin: 0 0 16px; padding: 12px; border: 1px solid #dcdcde; background: #fff;">';
		echo '<p style="margin: 0 0 8px;"><strong>' . esc_html( sprintf( __( 'Request #%s', 'myaccount-core' ), strtoupper( substr( $request_id, 0, 8 ) ) ) ) . '</strong></p>';

		if ( '' !== $created_label || '' !== $type_label ) {
			echo '<p style="margin: 0 0 8px; color: #50575e;">';
			echo esc_html( trim( $type_label . ( $created_label ? ' • ' . $created_label : '' ) ) );
			echo '</p>';
		}

		if ( ! empty( $request['items'] ) && is_array( $request['items'] ) ) {
			echo '<ul style="margin: 0 0 8px 18px; list-style: disc;">';

			foreach ( $request['items'] as $item ) {
				$product_name = sanitize_text_field( (string) ( $item['product_name'] ?? '' ) );
				$qty          = absint( $item['qty'] ?? 0 );

				if ( '' === $product_name || $qty <= 0 ) {
					continue;
				}

				echo '<li>' . esc_html( sprintf( __( '%1$s x %2$d', 'myaccount-core' ), $product_name, $qty ) ) . '</li>';
			}

			echo '</ul>';
		}

		if ( '' !== $reason ) {
			echo '<p style="margin: 0 0 8px;"><strong>' . esc_html__( 'Reason:', 'myaccount-core' ) . '</strong> ' . esc_html( $reason ) . '</p>';
		}

		if ( '' !== $note ) {
			echo '<p style="margin: 0 0 8px;"><strong>' . esc_html__( 'Customer note:', 'myaccount-core' ) . '</strong> ' . esc_html( $note ) . '</p>';
		}

		echo '<p style="margin: 0 0 8px;">';
		echo '<label for="myaccount-core-return-status-' . esc_attr( $request_id ) . '" style="display:block; margin: 0 0 4px;"><strong>' . esc_html__( 'Status', 'myaccount-core' ) . '</strong></label>';
		echo '<select id="myaccount-core-return-status-' . esc_attr( $request_id ) . '" name="myaccount_core_return_updates[' . esc_attr( $request_id ) . '][status]" style="width: 100%;">';

		foreach ( $status_options as $option_value => $option_label ) {
			printf(
				'<option value="%1$s"%2$s>%3$s</option>',
				esc_attr( $option_value ),
				selected( $status, $option_value, false ),
				esc_html( $option_label )
			);
		}

		echo '</select>';
		echo '</p>';

		echo '<p style="margin: 0 0 8px;">';
		echo '<label for="myaccount-core-package-label-' . esc_attr( $request_id ) . '" style="display:block; margin: 0 0 4px;"><strong>' . esc_html__( 'Package label URL', 'myaccount-core' ) . '</strong></label>';
		echo '<input id="myaccount-core-package-label-' . esc_attr( $request_id ) . '" type="url" name="myaccount_core_return_updates[' . esc_attr( $request_id ) . '][package_label]" value="' . esc_attr( $package_label ) . '" style="width: 100%;" placeholder="https://..." />';
		echo '</p>';

		if ( '' !== $package_label ) {
			echo '<p style="margin: 0 0 12px;"><a class="button-link" href="' . esc_url( $package_label ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Open current label', 'myaccount-core' ) . '</a></p>';
		}

		echo '<p style="margin: 0;">';
		echo '<button type="submit" class="button button-secondary" name="myaccount_core_approve_return_request" value="' . esc_attr( $request_id ) . '">' . esc_html__( 'Approve Request', 'myaccount-core' ) . '</button>';
		echo '</p>';
		echo '<p style="margin: 8px 0 0; color: #50575e;">' . esc_html( sprintf( __( 'Current status: %s. Use the main Update button to save manual changes.', 'myaccount-core' ), $status_label ) ) . '</p>';
		echo '</div>';
	}

	/**
	 * @param int|WP_Post|WC_Order $subject
	 */
	private function resolve_order( $subject ): ?WC_Order {
		if ( $subject instanceof WC_Order ) {
			return $subject;
		}

		if ( $subject instanceof WP_Post ) {
			$order = wc_get_order( $subject->ID );
			return $order instanceof WC_Order ? $order : null;
		}

		if ( is_numeric( $subject ) ) {
			$order = wc_get_order( absint( $subject ) );
			return $order instanceof WC_Order ? $order : null;
		}

		return null;
	}

	/**
	 * @return array<int, string>
	 */
	private function get_order_screen_ids(): array {
		$screen_ids = array( 'shop_order' );

		if ( function_exists( 'wc_get_page_screen_id' ) ) {
			$screen_ids[] = wc_get_page_screen_id( 'shop-order' );
		}

		return array_values( array_unique( array_filter( $screen_ids ) ) );
	}
}
