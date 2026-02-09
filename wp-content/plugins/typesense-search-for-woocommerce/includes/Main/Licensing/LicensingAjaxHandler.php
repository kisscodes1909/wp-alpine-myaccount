<?php

namespace Codemanas\Typesense\WooCommerce\Main\Licensing;

use Codemanas\Typesense\WooCommerce\Admin\Admin;
use Codemanas\Typesense\WooCommerce\Main\Fields\Fields;

class LicensingAjaxHandler {
	private static ?LicensingAjaxHandler $instance = null;

	public static function get_instance(): ?LicensingAjaxHandler {
		return is_null( self::$instance ) ? self::$instance = new self() : self::$instance;
	}

	protected function __construct() {
		add_action( 'init', [ $this, 'init_modules' ] );
	}

	public function init_modules(): void {
		//save WooCommerce Settings
		add_action( 'wp_ajax_cmtsfwc_get_settings', [ $this, 'get_settings' ] );
		add_action( 'wp_ajax_cmtsfwc_save_settings', [ $this, 'save_settings' ] );

		//handle licensing
		add_action( 'wp_ajax_cmtsfwc_handle_licensing', [ $this, 'handle_licensing' ] );
		add_action( 'wp_ajax_cmtsfwc_get_licensing', [ $this, 'get_licensing' ] );
	}

	private function validate_access( $posted_data ): bool {
		/*Bail Early*/
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		if ( empty( $posted_data['nonce'] ) ) {
			return false;
		}

		if ( ! wp_verify_nonce( $posted_data['nonce'], 'cm_typesense_ValidateNonce' ) ) {
			return false;
		}

		return true;
	}

	public function get_settings(): void {
		$posted_data          = [];
		$posted_data['nonce'] = filter_input( INPUT_GET, 'nonce' );
		if ( ! $this->validate_access( $posted_data ) ) {
			wp_send_json_error( 'Script kiddes' );
		}

		$settings              = Fields::get_option( 'global_setting' );
		$hierarchical_settings = Fields::get_option( 'hierarchical_settings' );

		if ( ! empty( $hierarchical_settings )
		     && (
			     isset( $hierarchical_settings['make_category_hierarchical_menu'] ) &&
			     $hierarchical_settings['make_category_hierarchical_menu']
		     ) ) {
			$settings['make_category_hierarchical_menu'] = $hierarchical_settings['make_category_hierarchical_menu'];
		}

		$sort_by_options = [
			[
				'value' => 'recent',
				'label' => __( 'Recent', 'typesense-search-for-woocommerce' )
			],
			[
				'value' => 'oldest',
				'label' => __( 'Oldest', 'typesense-search-for-woocommerce' )
			],
			[
				'value' => 'sort_by_rating_low_to_high',
				'label' => __( 'Sort by rating: low to high', 'typesense-search-for-woocommerce' )
			],
			[
				'value' => 'sort_by_rating_high_to_low',
				'label' => __( 'Sort by rating: high to low', 'typesense-search-for-woocommerce' )
			],
			[
				'value' => 'sort_by_price_low_to_high',
				'label' => __( 'Sort by price: low to high', 'typesense-search-for-woocommerce' )
			],
			[
				'value' => 'sort_by_price_high_to_low',
				'label' => __( 'Sort by price: high to low', 'typesense-search-for-woocommerce' )
			],
			[
				'value' => 'sort_by_popularity',
				'label' => __( 'Sort by popularity', 'typesense-search-for-woocommerce' )
			]
		];

        $pagination_type_options = [
            [
                'value' => 'default',
                'label' => __( 'Default', 'typesense-search-for-woocommerce' )
            ],
            [
                'value' => 'infinite',
                'label' => __( 'Infinite ( with button )', 'typesense-search-for-woocommerce' )
            ],
        ];

		wp_send_json_success( [
			'settings'        => $settings,
			'sort_by_options' => $sort_by_options,
			'pagination_type_options' => $pagination_type_options
		] );
	}

	public function save_settings(): void {
		$request_body = file_get_contents( 'php://input' );
		$posted_data  = json_decode( $request_body, true );

		if ( ! $this->validate_access( $posted_data ) ) {
			wp_send_json_error( 'Setting Not Saved' );
		}

		//save the data now
		Admin::getInstance()->save_settings( $posted_data['settings'] );

		wp_send_json_success( [ 'settings' => $posted_data['settings'] ] );

	}

	public function get_licensing() {
		wp_send_json( [
			'license_key_status' => Fields::get_option( 'license_key_status' ),
			'license_key'        => Fields::get_option( 'license_key' )
		] );
	}

	public function handle_licensing() {
		$request_body = file_get_contents( 'php://input' );
		$posted_data  = json_decode( $request_body, true );

		if ( ! $this->validate_access( $posted_data ) ) {
			wp_send_json_error( 'Setting Not Saved' );
		}
		$response = Licensing::get_instance()->ajaxLicenseHandler( $posted_data['license_key'], $posted_data['process'] );
		wp_send_json( $response );
	}

}
