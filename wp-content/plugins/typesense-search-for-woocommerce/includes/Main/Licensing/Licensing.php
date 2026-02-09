<?php

namespace Codemanas\Typesense\WooCommerce\Main\Licensing;

use Automattic\WooCommerce\Internal\Admin\ProductForm\Field;
use Codemanas\Typesense\WooCommerce\Main\Fields\Fields;

class Licensing {

	/**
	 * Item ID of this product
	 *
	 * @var int
	 */
	private int $item_id;

	/**
	 * Validate with us otherwise fails
	 *
	 * @var string
	 */
	private string $store_url;

	/**
	 * Settings page link
	 *
	 * @var string
	 */
	private string $options_page;


	private string $pluginName = 'Typesense Search for WooCommerce';

	/**
	 * Hold my beer
	 *
	 * @var Fields Fields
	 */
	private Fields $fields;

	/**
	 * Create instance property
	 *
	 * @var null
	 */
	private static ?Licensing $_instance = null;

	/**
	 * Create only one instance so that it may not Repeat
	 *
	 * @since 1.0.0
	 */
	public static function get_instance(): ?Licensing {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( new Fields );
		}

		return self::$_instance;
	}

	/**
	 * Licensing constructor.
	 *
	 * @param Fields $fields
	 */
	public function __construct( Fields $fields ) {
		$this->fields       = $fields;
		$this->item_id      = 31670;
		$this->store_url    = 'https://www.codemanas.com';
		$this->options_page = 'admin.php?page=cm-typesense-addons';

		$this->call_hooks();
	}

	/**
	 * Calling WordPress hooks
	 */
	public function call_hooks() {
		add_filter( 'cm_typesense_pro_plugins', [ $this, 'enable_plugin_license_page' ] );
		add_action( 'admin_init', [ $this, 'maybe_activate_licensing' ] );
		add_action( 'admin_notices', [ $this, 'notices' ] );
		add_action( 'cm_typesense_licensing_area', [ $this, 'show_license_form' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_scripts' ], 5 );
	}

	public function enable_plugin_license_page( $items ) {
		$items['woocommerce'] = 'Search with Typesense';

		return $items;
	}

	public function load_scripts( $hook_suffix ): void {
		$script = [];
		if ( file_exists( CM_TSFWC_ROOT_DIR_PATH . '/assets/admin/index.asset.php' ) ) {
			$script = include_once CM_TSFWC_ROOT_DIR_PATH . '/assets/admin/index.asset.php';
		}
		$dependencies = $script['dependencies'];
		//  $dependencies[] = 'cm-typesense-admin-script';
		wp_register_script( 'cmtsfwc-admin-script', CM_TSFWC_ROOT_URI_PATH . 'assets/admin/index.js', $dependencies, $script['version'], true );
		wp_register_style( 'cmtsfwc-admin-style', CM_TSFWC_ROOT_URI_PATH . 'assets/admin/style-index.css', [ 'wp-components' ], CM_TSFWC_VERSION );
		wp_localize_script( 'cmtsfwc-admin-script', 'cmTypesenseAdmin', [
			'nonce'                         => wp_create_nonce( 'cm_typesense_ValidateNonce' ),
			'assets_url'                    => CODEMANAS_TYPESENSE_ROOT_URI_PATH . '/assets',
			'instant_search_customizer_url' => admin_url( '/customize.php?autofocus[section]=typesense_popup' ),
		] );
		$license_key = Fields::get_option( 'license_key' );
		wp_localize_script( 'cmtsfwc-admin-script', 'cmtsfwcAdmin', [
			'show_tab' => ! empty( $license_key )
		] );

		wp_enqueue_script( 'cmtsfwc-admin-script' );
		//wp_enqueue_style( 'cmtsfw-admin-style' );

	}

	/**
	 * Show license tab with forms
	 */
	public function show_license_form() {
		$license = Fields::get_option( 'license_key' );
		$status  = Fields::get_option( 'license_key_status' );
		$args    = [ 'license' => $license, 'status' => $status ];
		load_template( CM_TSFWC_ROOT_DIR_PATH . '/includes/Main/Licensing/views/tpl-licensing.php', true, $args );
	}

	public function ajaxLicenseHandler( $license_key, $action ): array {
		$response = [ 'success' => false, 'error' => 'Something went wrong' ];
		switch ( $action ) {
			case 'activate':
				$response = $this->activate_license( $license_key );
				break;
			case 'deactivate':
				$response = $this->deactivate_license( $license_key );
				break;
			default:
				break;
		}

		return $response;
	}

	/**
	 * Check Activate license or deactivate or reset
	 */
	public function maybe_activate_licensing() {
		$nonce = filter_input( INPUT_POST, 'cm_typesense_wc_licensing_nonce' );
		// run a quick security check
		if ( ( isset( $_POST['cm_tsfwc_recurring_activate'] ) || isset( $_POST['cm_tsfwc_recurring_deactivate'] ) ) && ! wp_verify_nonce( $nonce, 'cm_typesense_wc_verify_licensing_nonce' ) ) {
			return;
		}

		// listen for our activate button to be clicked
		if ( isset( $_POST['cm_tsfwc_recurring_activate'] ) ) {
			$license_field = sanitize_text_field( filter_input( INPUT_POST, 'cm_tsfwc_recurring_license_ley' ) );
			$response      = $this->activate_license( $license_field );
		}

		// listen for our deactivate button to be clicked
		if ( isset( $_POST['cm_tsfwc_recurring_deactivate'] ) ) {
			$license = trim( Fields::get_option( 'license_key' ) );
			$this->deactivate_license( $license );
		}
	}

	/**
	 * Activate the License
	 *
	 * @since  1.0.0
	 * @author Codemanas
	 */
	private function activate_license( $license_field ): array {
		$data = [ 'success' => true ];
		//Update License Key First
		Fields::set_option( 'license_key', $license_field );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => trim( $license_field ),
			'item_id'    => $this->item_id,
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post( $this->store_url, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params
		) );

		// make sure the response came back okay
		$license_data = false;
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'typesense-search-for-woocommerce' );
			}
		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( ! empty( $license_data ) && false === $license_data->success ) {
				switch ( $license_data->error ) {
					case 'expired' :
						$message = sprintf( __( 'Your license key expired on %s. Please check your email for renew notice related to your existing license.', 'typesense-search-for-woocommerce' ), date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) ) );
						break;
					case 'disabled' :
					case 'revoked' :
						$message = __( 'Your license key has been disabled.', 'typesense-search-for-woocommerce' );
						break;
					case 'missing' :
						$message = __( 'Invalid license.', 'typesense-search-for-woocommerce' );
						break;
					case 'invalid' :
					case 'site_inactive' :
						$message = __( 'Your license is not active for this URL.', 'typesense-search-for-woocommerce' );
						break;
					case 'item_name_mismatch' :
						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'typesense-search-for-woocommerce' ), $this->pluginName );
						break;
					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.', 'typesense-search-for-woocommerce' );
						break;
					default :
						$message = __( 'An error occurred, please try again.', 'typesense-search-for-woocommerce' );
						break;
				}
			}
		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$data['success'] = false;
			$data['message'] = $message;
			Fields::delete_option( 'license_key_status' );

			return $data;
		}

		// $license_data->license will be either "valid" or "invalid"
		Fields::set_option( 'license_key_status', ! empty( $license_data->license ) ? $license_data->license : false );
		$data['message'] = 'License Activated : Please refresh the page - to see WooCommerce Tab';

		return $data;
	}

	/**
	 * Deactivate licnse
	 *
	 * @since  1.0.0
	 * @author Codemanas
	 */
	private function deactivate_license( $license ): array {
		$data = [ 'success' => true, 'message' => 'Typesense for WooCommerce License Deactivated' ];
		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_id'    => $this->item_id,
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( $this->store_url, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params
		) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again later.', 'typesense-search-for-woocommerce' );
			}
			$data['success'] = false;
			$data['message'] = $message;

		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		if ( 200 === wp_remote_retrieve_response_code( $response ) && $license_data->success === false && $license_data->license === 'failed' ) {
			$data['message']       = __( 'An error occurred, please try again.', 'typesense-search-for-woocommerce' );
			$data['success']       = false;
			$data['$license_data'] = $license_data;
		}

		if ( $license_data->license == 'deactivated' ) {
			Fields::delete_option( 'license_key_status' );
		}

		return $data;
	}

	/**
	 * Print Admin Notices
	 */
	public function notices() {
		$status = Fields::get_option( 'license_key_status' );
		if ( empty( $status ) || $status === "invalid" ) {
			?>
            <div class="error">
                <p><strong><?php echo $this->pluginName; ?></strong>: Invalid License Key. Add your keys from:
                    <a href="<?php echo admin_url( $this->options_page ); ?>">Here</a></p>
            </div>
			<?php
		}

		if ( ! empty( $status ) && $status === "expired" ) {
			//Breaks if something is off here.
			?>
            <div class="error">
                <p><strong><?php echo $this->pluginName ?></strong>: Your license key has expired. License key is
                    required to receive
                    future updates and support. Please check your email for renewal notices.</p>
            </div>
			<?php
		}

		if ( isset( $_GET['vczapi_pro_addon_msg'] ) && ! empty( $_GET['message'] ) ) {
			switch ( $_GET['vczapi_pro_addon_msg'] ) {
				case 'false':
					$message = urldecode( $_GET['message'] );
					?>
                    <div class="error">
                        <p><?php echo $message; ?></p>
                    </div>
					<?php
					break;
				case 'true':
				default:
					break;
			}
		}
	}

	/**
	 * @return string
	 */
	public function get_store_url(): string {
		return $this->store_url;
	}

	/**
	 * @return int
	 */
	public function get_item_id(): int {
		return $this->item_id;
	}
}