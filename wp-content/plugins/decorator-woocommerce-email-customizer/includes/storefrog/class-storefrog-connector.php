<?php
/**
 *  Storefrog connector class.
 *
 *  Handles authentication and connection between WooCommerce and StoreFrog service.
 *  Manages OAuth tokens, encryption, and admin interface integration.
 *
 *  @since 2.0.0
 *  @package Storefrog_Connector
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Storefrog_Connector' ) ) {
	/**
	 *  Storefrog connector class.
	 *
	 *  Handles authentication and connection between WooCommerce and StoreFrog service.
	 *  Manages OAuth tokens, encryption, and admin interface integration.
	 *
	 *  @since 2.0.0
	 */
	class Storefrog_Connector {

		/**
		 *  Storefrog url.
		 *
		 *  @var string
		 */
		private $storefrog_url = 'https://marketing.webtoffee.com/';

		/**
		 *  Token db url.
		 *
		 *  @var string
		 */
		private $token_db_url = 'https://api.marketing.webtoffee.com/api/v1/';

		/**
		 *  Script url.
		 *
		 *  @var string
		 */
		private $script_url = 'https://media.storefrog.com/script/script.js';

		/**
		 *  Nonce action.
		 *
		 *  @var string
		 */
		private $nonce_action = 'connect_storefrog';

		/**
		 *  Data option name.
		 *
		 *  @var string
		 */
		private $data_option_name = 'sf_connector_data';

		/**
		 *  Key option name.
		 *
		 *  @var string
		 */
		private $key_option_name = 'sf_connector_key';

		/**
		 *  App name.
		 *
		 *  @var string
		 */
		private $app_name = 'WebToffee Marketing';

		/**
		 *  WC API permission.
		 *
		 *  @var string
		 */
		private $wc_api_perm = 'read_write';


		/**
		 *  Connection information.
		 *
		 *  @var null|array
		 */
		private $connection_data = null;


		/**
		 *  Connector version.
		 *
		 *  @var string
		 */
		private $version = '1.0.4';


		/**
		 *  Show authorization denied warning.
		 *
		 *  @var boolean
		 */
		private $show_warning = false;

		/**
		 *  Show authorization denied warning.
		 *
		 *  @var boolean
		 */
		private $show_warning_option = 'wbte_decorator_show_warning';

		/**
		 *  Token db url.
		 *
		 *  @var string
		 */
		private $ema_services_url = 'https://ema-services.storefrog.com/api/v1/';

		/**
		 *  Constructer.
		 */
		public function __construct() {
			// Add new admin menu.
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
			add_action( 'init', array( $this, 'save_tokens' ) );
			add_action( 'wp_ajax_wbte_sf_disconnect', array( $this, 'disconnect_storefrog' ) );
			add_action( 'wp_ajax_wbte_sf_set_first_time_connect', array( $this, 'set_first_time_connect' ) );

			// Enqueue scripts to frontend.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Add custom attribute to the enqueued script.
			add_filter( 'script_loader_tag', array( $this, 'add_custom_attribute_to_script' ), 10, 2 );

			// Add ajax action to get cart data.
			add_action( 'wc_ajax_get_storefrog_data_object', array( $this, 'ajax_get_storefrog_data_object' ) );

			add_action( 'init', array( $this, 'after_auth_redirect' ), 11 );

			// Register WordPress settings.
			add_action( 'admin_init', array( $this, 'register_settings' ) );

			// Register REST API routes.
			add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
			
			// Add footer script for Storefrog data object initialization.
			add_action( 'wp_footer', array( $this, 'add_storefrog_footer_script' ), 99 );

			// Add back in stock notification.
			add_action( 'woocommerce_single_product_summary', array( $this, 'ema_single_page_back_in_stock_notification' ), 21 );
			add_action( 'wp_footer', array( $this, 'ema_single_page_variation_script' ), 100 );
	}

		/**
		 *  Register WordPress settings.
		 */
		public function register_settings() {
			register_setting( 'general', $this->show_warning_option, array(
				'show_in_rest' => array(
					'schema' => array(
						'type'    => 'boolean',
						'default' => false,
					),
				),
				'type'         => 'boolean',
				'default'      => false,
			) );
		}

		/**
		 *  Register REST API routes.
		 */
		public function register_rest_routes() {
			// Register with WooCommerce namespace for proper authentication
			register_rest_route( 'wc/v3', '/decorator/warning-status', array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_warning_status_rest' ),
				'permission_callback' => '__return_true', // WooCommerce handles auth
			) );

			register_rest_route( 'wc/v3', '/decorator/clear-warning', array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'clear_warning_rest' ),
				'permission_callback' => '__return_true', // WooCommerce handles auth
			) );
		}

		/**
		 *  Check admin permissions for REST API.
		 *
		 *  @return bool True if user has admin permissions.
		 */
		public function check_admin_permissions() {
			// Check if user is logged in and has manage_woocommerce capability
			if ( current_user_can( 'manage_woocommerce' ) ) {
				return true;
			}
			
			// For WooCommerce REST API authentication, allow access if Basic auth is present
			// WooCommerce will handle the authentication validation
			$auth_header = isset( $_SERVER['HTTP_AUTHORIZATION'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_AUTHORIZATION'] ) ) : '';
			if ( ! empty( $auth_header ) && strpos( $auth_header, 'Basic ' ) === 0 ) {
				return true;
			}
			
			return false;
		}

		/**
		 *  Get warning status via REST API.
		 *
		 *  @return WP_REST_Response Response object.
		 */
		public function get_warning_status_rest() {
			return new WP_REST_Response( array(
				'success' => true,
				'warning' => $this->get_warning_status(),
			), 200 );
		}

		/**
		 *  Clear warning via REST API.
		 *
		 *  @return WP_REST_Response Response object.
		 */
		public function clear_warning_rest() {
			$this->clear_warning();
			return new WP_REST_Response( array(
				'success' => true,
				'message' => 'Warning cleared successfully.',
			), 200 );
		}

		/**
		 *  Add new admin menu.
		 */
		public function add_admin_menu() {
			/**
			 *  Allow role.
			 *
			 *  @param string $allow_role Allow role.
			 */
			$allow_role = apply_filters( 'woocommerce_decorator_role', 'manage_woocommerce' );

			/**
			 *  Add menu pages.
			 */
			add_menu_page( __( 'WebToffee Marketing', 'decorator-woocommerce-email-customizer' ), __( 'WebToffee Marketing', 'decorator-woocommerce-email-customizer' ), $allow_role, 'wbte-decorator-connector', array( $this, 'render_admin_page' ), $this->get_menu_icon(), 56 );
			add_submenu_page( 'wbte-decorator-connector', __( 'Connector', 'decorator-woocommerce-email-customizer' ), __( 'Connector', 'decorator-woocommerce-email-customizer' ), $allow_role, 'wbte-decorator-connector', array( $this, 'render_admin_page' ) );
			add_submenu_page( 'wbte-decorator-connector', __( 'Email editor', 'decorator-woocommerce-email-customizer' ), __( 'Email editor', 'decorator-woocommerce-email-customizer' ), $allow_role, 'decorator-woocommerce-email-customizer' );
		}

		/**
		 * Get menu icon URL.
		 *
		 * @return string Data URI for the menu icon SVG.
		 */
		private function get_menu_icon() {
			$svg = '<svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M25.673 21.5837C25.673 21.974 25.3662 22.2941 24.9919 22.2941H20.2436C19.8693 22.2941 19.5625 21.974 19.5625 21.5837V16.6308C19.5625 16.2404 19.8693 15.9204 20.2436 15.9204H24.9919C25.3662 15.9204 25.673 16.2404 25.673 16.6308V21.5837Z" fill="#9DA1A6"/>
					<path d="M31.8566 15.3395C31.8566 15.7299 31.5498 16.0499 31.1755 16.0499H26.4272C26.0529 16.0499 25.7461 15.7299 25.7461 15.3395V10.3867C25.7461 9.9963 26.0529 9.67627 26.4272 9.67627H31.1755C31.5498 9.67627 31.8566 9.9963 31.8566 10.3867V15.3395Z" fill="#9DA1A6"/>
					<path d="M13.4738 21.5837C13.4738 21.9741 13.167 22.2941 12.7927 22.2941H8.04435C7.6701 22.2941 7.36328 21.9741 7.36328 21.5837V16.6308C7.36328 16.2404 7.6701 15.9204 8.04435 15.9204H12.7927C13.167 15.9204 13.4738 16.2404 13.4738 16.6308V21.5837Z" fill="#9DA1A6"/>
					<path d="M7.2941 15.3405C7.2941 15.7309 6.98728 16.0509 6.61303 16.0509H1.86466C1.49041 16.0509 1.18359 15.7321 1.18359 15.3405V10.3876C1.18359 9.99728 1.49041 9.67725 1.86466 9.67725H6.61303C6.98728 9.67725 7.2941 9.99728 7.2941 10.3876V15.3405Z" fill="#9DA1A6"/>
					</svg>';		
			return 'data:image/svg+xml;base64,' . base64_encode( $svg );
		}

		/**
		 *  Render admin page.
		 */
		public function render_admin_page() {

			$auth_url     = $this->get_auth_url();
			$asset_url    = $this->get_asset_url();
			$is_connected = $this->is_connected();
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce is not required here.
			$tab             = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'tab1';
			$tab             = $is_connected ? 'tab2' : $tab; // Show tab 2 when connected.
			$tab2_url        = admin_url( 'admin.php?page=wbte-decorator-connector&tab=tab2' );
			$dashboard_url   = $this->get_dashboard_url();
			$connection_data = $this->get_connection_data();
			$connected_email = isset( $connection_data['email'] ) ? $connection_data['email'] : 'StoreFrog';

			require_once RP_DECORATOR_PLUGIN_PATH . 'includes/storefrog/views/-connector-page.php';
		}

		/**
		 *  Save tokens and redirect to WC auth page
		 */
		public function save_tokens() {
			if ( isset( $_GET['sf_session_type'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), $this->nonce_action ) ) {
				$session_type  = sanitize_text_field( wp_unslash( $_GET['sf_session_type'] ) );
				$website_id    = isset( $_GET['sf_website_id'] ) ? sanitize_text_field( wp_unslash( $_GET['sf_website_id'] ) ) : '';
				$session_id    = isset( $_GET['sf_session_id'] ) ? sanitize_text_field( wp_unslash( $_GET['sf_session_id'] ) ) : '';
				$refresh_token = isset( $_GET['sf_refresh_token'] ) ? sanitize_text_field( wp_unslash( $_GET['sf_refresh_token'] ) ) : '';
				$app_name      = isset( $_GET['sf_app_name'] ) ? sanitize_text_field( wp_unslash( $_GET['sf_app_name'] ) ) : '';
				$expires_at    = isset( $_GET['sf_expires_at'] ) ? sanitize_text_field( wp_unslash( $_GET['sf_expires_at'] ) ) : '';
				$user_id       = isset( $_GET['sf_user_id'] ) ? sanitize_text_field( wp_unslash( $_GET['sf_user_id'] ) ) : '';
				$website_key   = isset( $_GET['sf_website_key'] ) ? sanitize_text_field( wp_unslash( $_GET['sf_website_key'] ) ) : '';
				$email         = isset( $_GET['sf_email'] ) ? sanitize_text_field( wp_unslash( $_GET['sf_email'] ) ) : '';

				if ( 'oauth' === $session_type
					&& $website_id
					&& $session_id
					&& $refresh_token
					&& $app_name
					&& $expires_at
					&& $user_id
					&& $website_key
					&& $email ) {

					// Here we are using nonce key as encryption key.
					$nonce = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );

					// Save connection data.
					$this->save_oauth_datas( $website_id, $session_id, $refresh_token, $app_name, $expires_at, $user_id, $nonce, $website_key, $email );

					// Redirect to WC auth page.
					$params       = array(
						'app_name'     => $this->app_name,
						'scope'        => $this->wc_api_perm,
						'user_id'      => $website_id,
						'return_url'   => admin_url( 'admin.php?page=wbte-decorator-connector&tab=tab2' ),
						'callback_url' => $this->get_token_url( $website_id, $session_id ),
					);
					$query_string = http_build_query( $params );

					wp_safe_redirect( $this->get_wc_auth_url() . '?' . $query_string );
					exit;
				}
			}
		}

		/**
		 *  Get auth url.
		 *
		 *  @return string Auth url.
		 */
		private function get_auth_url() {
			$nonce = wp_create_nonce( $this->nonce_action );
			$first_time = get_option('wt_ema_first_time_connect', false);
			$from_app = get_option('wt_ema_from_app', false);
			if( version_compare(RP_DECORATOR_VERSION, '2.1.5', '>=') && $from_app === false && $first_time === false ){
				return $this->storefrog_url . 'oauth?session_type=oauth&platform=woocommerce&_wpnonce=' . $nonce . '&redirect_uri=' . home_url() . '&utm_source=free_plugin_signup&utm_medium=connect_now_btn&utm_campaign=EMA';
			}
			
			return $this->storefrog_url . 'oauth?session_type=oauth&platform=woocommerce&_wpnonce=' . $nonce . '&redirect_uri=' . home_url();
		}

		/**
		 *  Get asset url.
		 *
		 *  @return string Asset url.
		 */
		private function get_asset_url() {
			return RP_DECORATOR_PLUGIN_URL . '/includes/storefrog/assets/';
		}

		/**
		 *  Get dashboard url.
		 *
		 *  @return string Dashboard url.
		 */
		private function get_dashboard_url() {
			return $this->storefrog_url . 'dashboard/';
		}

		/**
		 *  Get WC auth url.
		 *
		 *  @return string WC auth url.
		 */
		private function get_wc_auth_url() {
			return home_url() . '/wc-auth/v1/authorize';
		}

		/**
		 *  Get token storing url.
		 *
		 *  @param string $website_id Website id.
		 *  @param string $access_token Access token.
		 *  @return string Token url.
		 */
		private function get_token_url( $website_id, $access_token ) {
			return $this->token_db_url . 'website-auth/' . $website_id . '?session_id=' . rawurlencode( $access_token );
		}

		/**
		 *  Get refresh token url.
		 *
		 *  @return string Refresh token url.
		 */
		private function get_refresh_token_url() {
			return $this->token_db_url . 'token/';
		}

		/**
		 *  Encrypt and save oauth tokens.
		 *
		 *  @param string $website_id Website id.
		 *  @param string $session_id Session id.
		 *  @param string $refresh_token Refresh token.
		 *  @param string $app_name App name.
		 *  @param string $expires_at Expires at.
		 *  @param string $user_id User id.
		 *  @param string $nonce Nonce. Here we are using nonce as encryption key.
		 *  @param string $website_key Website key.
		 *  @param string $email Email.
		 */
		private function save_oauth_datas( $website_id, $session_id, $refresh_token, $app_name, $expires_at, $user_id, $nonce, $website_key, $email ) {

			// Save connection data.
			$data_arr = array(
				'refresh_token' => $this->encrypt_data( $refresh_token, $nonce . 'refresh' ),
				'access_token'  => $this->encrypt_data( $session_id, $nonce . 'token' ),
				'expires_at'    => $expires_at,
				'website_id'    => $website_id,
				'user_id'       => $user_id,
				'app_name'      => $app_name,
				'website_key'   => $website_key,
				'email'         => $email,
			);

			update_option( $this->data_option_name, $data_arr );

			// Save key.
			update_option( $this->key_option_name, $nonce );
		}

		/**
		 * Encrypt data securely.
		 *
		 * @param string $data The data to encrypt.
		 * @param string $key  A secure encryption key.
		 * @return string The encrypted data.
		 */
		private function encrypt_data( $data, $key ) {
			// Generate a secure hash of the key to ensure consistent length.
			$key_hash = hash( 'sha256', $key, true );

			// Repeat the key hash to match the length of the data.
			$key_repeated = str_repeat( $key_hash, ceil( strlen( $data ) / strlen( $key_hash ) ) );

			// XOR the data with the repeated key hash.
			$encrypted_data = $data ^ substr( $key_repeated, 0, strlen( $data ) );

			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Base64 encoding is used to store encrypted binary data as a string in the database.
			return base64_encode( $encrypted_data );
		}

		/**
		 * Decrypt data using base64 and a key.
		 *
		 * @param string $encrypted_data The encrypted data to decrypt.
		 * @param string $key            The encryption key.
		 * @return string The decrypted data.
		 */
		private function decrypt_data( $encrypted_data, $key ) {
			// Generate a secure hash of the key to ensure consistent length.
			$key_hash = hash( 'sha256', $key, true );

			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- Base64 decoding is used to decrypt binary data stored in the database.
			$data = base64_decode( $encrypted_data );

			// Repeat the key hash to match the length of the data.
			$key_repeated = str_repeat( $key_hash, ceil( strlen( $data ) / strlen( $key_hash ) ) );

			// XOR the data with the repeated key hash to decrypt.
			return $data ^ substr( $key_repeated, 0, strlen( $data ) );
		}

		/**
		 *  Get connection data.
		 *
		 *  @return array Connection data.
		 */
		private function get_connection_data() {
			$this->connection_data = is_null( $this->connection_data ) ? get_option( $this->data_option_name, array() ) : $this->connection_data;
			return $this->connection_data;
		}

		/**
		 *  Is connected.
		 */
		private function is_connected() {
			return ! empty( $this->get_connection_data() );
		}

		/**
		 *  Disconnect storefrog.
		 */
		public function disconnect_storefrog() {
			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'wbte_sf_disconnect' ) ) {
				$connection_data = $this->get_connection_data();
				$website_key     = isset( $connection_data['website_key'] ) ? $connection_data['website_key'] : '';
				$user_email      = isset( $connection_data['email'] ) ? $connection_data['email'] : '';
				if( $website_key && $user_email ){
					$response = wp_remote_post( $this->ema_services_url . 'disconnect?user=' . $website_key . '&email=' . $user_email, array(
						'method' => 'POST',
						'headers' => array(
							'Authorization' => 'Bearer ' . $website_key,
						),
					) );
					
					if ( is_wp_error( $response ) ) {
						wp_send_json_error( array( 'success' => false ) );
					}else{
						delete_option( $this->data_option_name );
						delete_option( $this->key_option_name );
						update_option( $this->show_warning_option, false );
						wp_send_json_success( array( 'success' => true ) );
					}
				}else{
					wp_send_json_error( array( 'success' => false ) );
				}
			} else {
				wp_send_json_error( array( 'success' => false ) );
			}
		}

		/**
		 *  Set first time connect flag when Connect Now button is clicked.
		 */
		public function set_first_time_connect() {
			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'wbte_sf_set_first_time_connect' ) && version_compare(RP_DECORATOR_VERSION, '2.1.5', '>=') ) {
				update_option('wt_ema_first_time_connect', true);
				wp_send_json_success( array( 'success' => true ) );
			} else {
				wp_send_json_error( array( 'success' => false ) );
			}
		}

		/**
		 *  Enqueue scripts to frontend.
		 */
		public function enqueue_scripts() {
			if ( $this->is_connected() ) {

				// Embed script.
				wp_enqueue_script( 'wbte_sf_embed', $this->script_url, array(), $this->version, true );

				// Plugin JS.
				wp_enqueue_script( 'wbte_sf_script', $this->get_asset_url() . 'js/storefrog.js', array( 'jquery', 'wbte_sf_embed', 'wc-blocks-checkout' ), $this->version, true );

				wp_localize_script(
					'wbte_sf_script',
					'wbte_sf_script_params',
					array(
						'home_url'            => esc_url( home_url() ),
						'nonce'               => wp_create_nonce( 'wbte_sf_nonce' ),
						'storefrogDataObject' => $this->get_data_object(),
					)
				);
			}
		}

		/**
		 *  Get data object.
		 *
		 *  @return array Data object.
		 */
		private function get_data_object() {

			$load_cart_data = false;
			if (wp_doing_ajax()) {
				$load_cart_data = true;
			}
			$load_cart_data = apply_filters('wt_ema_load_cart_html_table_data', $load_cart_data);

			$last_added_product_id = $this->get_last_added_product_id();


			// Prepare cart data.
			$cart_data = array(
				'cart' => array(
					'items'         => array(),
					'hash'          => '',
					'currency_code' => '',
					'total'         => 0,
					'subtotal'      => 0,
					'table'         => $load_cart_data ? $this->get_cart_table_html() : '',
				),
				'user' => array(
					'isLoggedIn' => is_user_logged_in(),
					'email'      => is_user_logged_in() ? wp_get_current_user()->user_email : '',
					'orders'     => is_user_logged_in() ? wc_get_customer_order_count(get_current_user_id()) : 0,
					'last_added_product_id' => isset( $last_added_product_id['variation_id'] ) ? $last_added_product_id['variation_id'] : 0,
					'last_added_product_id_parent' => isset( $last_added_product_id['product_id'] ) ? $last_added_product_id['product_id'] : 0,
					'last_added_product_category' => $this->get_last_added_product_category(),
					'store_currency' => function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : '$',	
				),
				'page' => array(
					'page_type'  => 'generic',
					'data_id'    => 0,
					'data_title' => '',
					'data_slug'  => '',
					'categories' => array(),
					'tags'       => array(),
					'brands'     => array(),
				),
			);

			// Add cart items if WC cart is available.
			if ( function_exists( 'WC' ) && WC()->cart ) {
				$cart_items = array();
				foreach ( WC()->cart->get_cart() as $cart_item ) {
					$product = $cart_item['data'];

					$cart_items[] = array(
						'product_id'   => $cart_item['product_id'],
						'variation_id' => $cart_item['variation_id'],
						'name'         => $product->get_name(),
						'quantity'     => $cart_item['quantity'],
						'price'        => wc_get_price_excluding_tax( $product ), // Product price without tax.
						'total'        => $cart_item['line_total'], // Line total without tax.
						'category'     => wp_get_post_terms($cart_item['product_id'], 'product_cat', array('fields' => 'slugs')),
					);
				}
				$cart_data['cart']['items']         = $cart_items;
				$cart_data['cart']['hash']          = WC()->cart->get_cart_hash();
				$cart_data['cart']['currency_code'] = get_woocommerce_currency();
				$cart_data['cart']['total']         = max( 0, WC()->cart->get_total( 'edit' ) - WC()->cart->get_total_tax() ); // Cart total without tax.
				$cart_data['cart']['subtotal']      = max( 0, WC()->cart->get_subtotal( 'edit' ) - WC()->cart->get_subtotal_tax() );
			}

			// Switch function to check current page.
			$current_page = get_queried_object();
			if ( $current_page ) {
				$cart_data['page']['data_id'] = isset( $current_page->ID ) ? $current_page->ID : ( isset( $current_page->term_id ) ? $current_page->term_id : 0 );
				if ( function_exists( 'is_shop' ) && is_shop() ) {
					$cart_data['page']['data_title'] = ! empty( $current_page->label ) ? $current_page->label : __( 'Products', 'decorator-woocommerce-email-customizer' );
					$cart_data['page']['data_slug']  = ! empty( $current_page->name ) ? $current_page->name : 'shop';
				} else {
					$cart_data['page']['data_title'] = ! empty( $current_page->post_title ) ? $current_page->post_title : '';
					$cart_data['page']['data_title'] = empty( $cart_data['page']['data_title'] ) && ! empty( $current_page->name ) ? $current_page->name : '';

					$cart_data['page']['data_slug'] = ! empty( $current_page->post_name ) ? $current_page->post_name : '';
					$cart_data['page']['data_slug'] = empty( $cart_data['page']['data_slug'] ) && ! empty( $current_page->slug ) ? $current_page->slug : '';
					$cart_data['page']['is_order_received_page'] = (function_exists('is_order_received_page') && is_order_received_page());
				}
			}

			switch ( true ) {
				// Single product page.
				case function_exists( 'is_product' ) && is_product():
					$product_id                      = get_the_ID();
					$cart_data['page']['page_type']  = 'product';
					$cart_data['page']['categories'] = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'slugs' ) );
					$cart_data['page']['tags']       = wp_list_pluck( get_the_terms( $product_id, 'product_tag' ), 'slug' );
					$cart_data['page']['brands']     = wp_list_pluck( get_the_terms( $product_id, 'product_brand' ), 'slug' );
					$cart_data['page']['product_name'] = get_the_title($product_id);
					// check simple or variable
					$product = wc_get_product($product_id);
					if($product->is_type('simple')){
						$cart_data['page']['product_type'] = 'simple';
					}else{
						$cart_data['page']['product_type'] = 'variable';
					}
					break;

				// Cart page.
				case function_exists( 'is_cart' ) && is_cart():
					$cart_data['page']['page_type'] = 'cart';
					break;

				// Checkout page.
				case function_exists( 'is_checkout' ) && is_checkout():
					$cart_data['page']['page_type'] = 'checkout';
					break;

				// Category page.
				case function_exists( 'is_product_category' ) && is_product_category():
					$cart_data['page']['page_type']  = 'category';
					$cart_data['page']['categories'] = array( $cart_data['page']['data_slug'] );
					break;

				// Tag page.
				case function_exists( 'is_product_tag' ) && is_product_tag():
					$cart_data['page']['page_type'] = 'tag';
					$cart_data['page']['tags']      = array( $cart_data['page']['data_slug'] );
					break;

				// Brand page.
				case is_tax( 'product_brand' ):
					$cart_data['page']['page_type'] = 'brand';
					$cart_data['page']['brands']    = array( $cart_data['page']['data_slug'] );
					break;

				// Home page.
				case function_exists( 'is_front_page' ) && is_front_page():
					$cart_data['page']['page_type'] = 'home';
					break;

				// Shop page.
				case function_exists( 'is_shop' ) && is_shop():
					$cart_data['page']['page_type'] = 'shop';
					break;

				// Default.
				default:
					$cart_data['page']['page_type'] = 'generic';
					break;
			}

			return $cart_data;
		}

		/**
		 *  Add custom attribute to the enqueued script.
		 *
		 *  @param string $tag Script tag.
		 *  @param string $handle Script handle.
		 *  @return string Script tag.
		 */
		public function add_custom_attribute_to_script( $tag, $handle ) {
			if ( 'wbte_sf_embed' === $handle && $this->is_connected() ) {
				$connection_data = $this->get_connection_data();
				$website_key     = isset( $connection_data['website_key'] ) ? $connection_data['website_key'] : '';
				$tag             = str_replace( ' src=', ' data-shop-id="' . esc_attr( $website_key ) . '" src=', $tag );
			}
			return $tag;
		}

		/**
		 *  Is token expired. Important: Call only when connected and token is set.
		 *
		 *  @throws Exception If Storefrog is not connected.
		 *  @return bool True if token is expired, false otherwise.
		 */
		private function is_token_expired() {
			if ( ! $this->is_connected() ) {
				// Throw error.
				throw new Exception( esc_html__( 'Storefrog is not connected.', 'decorator-woocommerce-email-customizer' ) );
			}

			$connection_data = $this->get_connection_data();
			return time() > $connection_data['expires_at'];
		}

		/**
		 * Renew token.
		 *
		 * @throws Exception If Storefrog is returned an error.
		 */
		private function renew_token() {
			if ( $this->is_connected() && $this->is_token_expired() ) {
				$connection_data = $this->get_connection_data();
				$refresh_key     = $this->get_key( 'refresh' );
				$refresh_token   = $this->decrypt_data( $connection_data['refresh_token'], $refresh_key );

				// Send a post request to the token url. With refresh token as body application/json.
				$response = wp_remote_post(
					$this->get_refresh_token_url(),
					array(
						'body'    => wp_json_encode( array( 'refresh_token' => $refresh_token ) ),
						'headers' => array( 'Content-Type' => 'application/json' ),
					)
				);

				if ( is_wp_error( $response ) ) {
					// Throw error.
					throw new Exception( esc_html__( 'Failed to renew token.', 'decorator-woocommerce-email-customizer' ) );
				}

				$response_body     = json_decode( wp_remote_retrieve_body( $response ), true );
				$new_access_token  = isset( $response_body['access_token'] ) ? $response_body['access_token'] : '';
				$new_refresh_token = isset( $response_body['refresh_token'] ) ? $response_body['refresh_token'] : '';
				$new_expires_at    = isset( $response_body['expires_at'] ) ? $response_body['expires_at'] : '';

				if ( ! $new_access_token || ! $new_refresh_token || ! $new_expires_at ) {
					// Throw error.
					throw new Exception( esc_html__( 'Failed to renew token.', 'decorator-woocommerce-email-customizer' ) );
				}

				$token_key                        = $this->get_key( 'token' );
				$connection_data['access_token']  = $this->encrypt_data( $new_access_token, $token_key );
				$connection_data['refresh_token'] = $this->encrypt_data( $new_refresh_token, $refresh_key );
				$connection_data['expires_at']    = $new_expires_at;

				// Update the connection data.
				update_option( $this->data_option_name, $connection_data );
			}
		}

		/**
		 *  Get encryption key.
		 *
		 *  @param string $type Key type.
		 *  @return string Key.
		 */
		private function get_key( $type ) {
			$key = get_option( $this->key_option_name );
			return 'refresh' === $type ? $key . 'refresh' : $key . 'token';
		}

		/**
		 *  Ajax get storefrog data object.
		 */
		public function ajax_get_storefrog_data_object() {
			if ( isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'wbte_sf_nonce' ) ) {
				wp_send_json_success( $this->get_data_object() );
			} else {
				wp_send_json_error( array( 'success' => false ) );
			}
		}

		/**
		 *  Get cart table html.
		 *
		 *  @return string Cart table html.
		 */
		private function get_cart_table_html() {
			if ( ! function_exists( 'wc_get_template' ) ) {
				return '';
			}

			ob_start();
			try {
				wc_get_template( 'cart/cart.php' );
				$cart_html = ob_get_clean();

				// Enable error handling for malformed HTML.
				libxml_use_internal_errors( true );

				$dom = new DOMDocument();
				// Add HTML5 doctype and UTF-8 encoding to prevent encoding issues.
				$dom->loadHTML( '<?xml encoding="UTF-8">' . $cart_html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );

				// Use XPath for more efficient querying.
				$xpath = new DOMXPath( $dom );
				$table = $xpath->query( "//table[contains(@class, 'shop_table') and contains(@class, 'cart')]" )->item( 0 );

				if ( $table ) {
					// Find and remove all product-remove cells (th and td).
					$remove_cells = $xpath->query( "//th[contains(@class, 'product-remove')]|//td[contains(@class, 'product-remove')]" );
					foreach ( $remove_cells as $cell ) {
						// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- We are using DOMXPath.
						$cell->parentNode->removeChild( $cell );
					}

					// Convert quantity inputs to plain text.
					$quantity_cells = $xpath->query( "//td[contains(@class, 'product-quantity')]" );
					foreach ( $quantity_cells as $cell ) {
						$input = $xpath->query( ".//input[@type='number']", $cell )->item( 0 );
						if ( $input ) {
							$quantity = $input->getAttribute( 'value' );
							// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- We are using DOMXPath.
							$cell->textContent = $quantity;
						}
					}

					// Remove actions td and its parent tr.
					$actions_td = $xpath->query( "//td[contains(@class, 'actions')]" );
					foreach ( $actions_td as $td ) {
						// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- We are using DOMXPath.
						$td->parentNode->removeChild( $td );
					}

					$table_html = $dom->saveHTML( $table );
					return $table_html;
				}
			} catch ( Exception $e ) {
				$unused = $e; // To prevent PHPCS warning.
				unset( $unused );
			}

			return '';
		}

		/**
		 *  After auth redirect.
		 *  If the user approves the API auth, redirect to the dashboard.
		 *  If the user denies the API auth, show a warning.
		 * 
		 *  @since 2.0.4
		 */
		public function after_auth_redirect(){
			if ( ! is_admin() ) {
				return;
			}
			if( 
				isset( $_GET['page'], $_GET['tab'], $_GET['success'] ) //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				&& 'wbte-decorator-connector' === $_GET['page'] //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				&& 'tab2' === $_GET['tab'] //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			){
				if( sanitize_text_field( wp_unslash( $_GET['success'] ) ) ){ //phpcs:ignore WordPress.Security.NonceVerification.Recommended
					update_option($this->show_warning_option, false);
				} else{
					$this->show_warning = true;
					update_option( $this->show_warning_option, true );
				}	
				wp_redirect( $this->get_dashboard_url() ); //phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
				exit;
			}else{
				if( isset( $_GET['page']) && 'wbte-decorator-connector' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) &&  get_option( $this->show_warning_option, false ) ){ //phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$this->show_warning = true;
				}
			}
		}
		

		/**
		 *  Clear warning option.
		 *  This method can be called to clear the warning state.
		 * 
		 *  @since 2.0.4
		 */
		public function clear_warning() {
			update_option( $this->show_warning_option, false );
			$this->show_warning = false;
		}

		/**
		 *  Get warning status.
		 *  Returns the current warning status.
		 * 
		 *  @since 2.0.4
		 *  @return bool True if warning is active, false otherwise.
		 */
		public function get_warning_status() {
			return get_option( $this->show_warning_option, false );
		}

		/**
		 *  Get last added product ID safely.
		 *  Works for both logged-in and guest users.
		 *  Returns variation ID if product is a variation, otherwise product ID.
		 * 
		 *  @since 2.0.9
		 *  @return int Product ID or variation ID or 0 if not available.
		 */
		private function get_last_added_product_id() {
			// Check if WooCommerce and cart are available
			if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
				return 0;
			}

			// Try to get the last added product ID using different methods
			$cart_items = WC()->cart->get_cart();
			
			if ( empty( $cart_items ) ) {
				return 0;
			}

			// Get the last item added to cart (most recent)
			$last_item = end( $cart_items );
			
			if ( isset( $last_item['product_id'] ) ) {
				// If it's a variation, return variation ID, otherwise return product ID
				if ( isset( $last_item['variation_id'] ) && $last_item['variation_id'] > 0 ) {
					return array( 'variation_id' => $last_item['variation_id'], 'product_id' => $last_item['product_id'] );
				} else {
					return array( 'variation_id' => $last_item['product_id'],'product_id' => $last_item['product_id'] );
				}
			}

			return 0;
		}

		/**
		 *  Get last added product category.
		 * 
		 *  @since 2.0.9
		 *  @return string Comma-separated product categories or empty string if not available.
		 */
		
		private function get_last_added_product_category() {
			$product_id = $this->get_last_added_product_id();
			$last_added_product_id = isset( $product_id['product_id'] ) ? $product_id['product_id'] : 0;
			if ( $last_added_product_id ) {
				// Check if WooCommerce and cart are available
				if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
					return '';
				}

				$cart_items = WC()->cart->get_cart();
				if ( empty( $cart_items ) ) {
					return '';
				}

				// Get the last item added to cart (most recent)
				$last_item = end( $cart_items );
				
				// if variation, get the parent product category
				if ( isset( $last_item['variation_id'] ) && $last_item['variation_id'] > 0 ) {
					$categories = wp_get_post_terms( $last_item['product_id'], 'product_cat', array( 'fields' => 'slugs' ) );
				} else {
					$categories = wp_get_post_terms( $last_added_product_id, 'product_cat', array( 'fields' => 'slugs' ) );
				}
				
				// Convert array to comma-separated string
				return is_array( $categories ) ? implode( ',', $categories ) : '';
			}
			return '';
		}

		/**
		 *  Add footer script for Storefrog data object initialization.
		 *  Calls getStorefrogDataObject() function when jQuery is ready.
		 * 
		 *  @since 2.0.9
		 */
		public function add_storefrog_footer_script() {
			if ( $this->is_connected() ) {
				?>
				<script>
					if (typeof jQuery !== 'undefined') {
						jQuery(document).ready(function() {
							if (typeof getStorefrogDataObject === 'function') {
								getStorefrogDataObject(true);
							}
						});
					}
				</script>
				<?php
			}
		}

		/**
		 *  Show back in stock notification button after quantity on product page.
		 *  Displays a hidden button that will be shown for out of stock products.
		 * 
		 *  @since 2.0.10
		 */
		public function ema_single_page_back_in_stock_notification() {
			if ( ! is_product() ) {
				return;
			}
		echo '<div class="ema-bis-wrap" data-product_id="">
				<button type="button"
					class="wt-notify-btn"
					aria-label="' . esc_attr__( 'Notify me when this product is back in stock', 'decorator-woocommerce-email-customizer' ) . '"
					data-product_id="" style="display:none">
					' . esc_html__( 'Notify me when available', 'decorator-woocommerce-email-customizer' ) . '
				</button>
			  </div>';
		}

		/**
		 *  Front-end logic for variable & simple products.
		 *  Handles showing/hiding the back in stock notification based on product stock status.
		 * 
		 *  @since 2.0.10
		 */
		public function ema_single_page_variation_script() {
			if ( ! is_product() ) {
				return;
			}

			global $product;

			?>
			<script>
			jQuery(function($){
				var $form = $('form.variations_form');
				var $wrap = $('.ema-bis-wrap');
				$wrap.hide();
				// ====== Variable Products ======
				if($form.length){
					$form.on('found_variation.wtStock', function(e, variation){
						if (variation && variation.is_in_stock === false) {
							//$wrap.show();
							$wrap.attr('data-product_id', variation.variation_id);
							$wrap.find('.wt-notify-btn').attr('data-product_id', variation.variation_id);
							$wrap.find('.wt-notify-btn').addClass('wt-notify-btn-style');
							$wrap.addClass('ema-bis-wrap-style');
						} else {
							// $wrap.hide();
							$wrap.find('.wt-notify-btn').attr('data-product_id', '');
							$wrap.find('.wt-notify-btn').removeClass('wt-notify-btn-style');
							$wrap.removeClass('ema-bis-wrap-style');
						}
					});

					$form.on('reset_data.wtStock hide_variation.wtStock', function(){
						$wrap.hide();
						$wrap.find('.wt-notify-btn').attr('data-product_id', '');
						$wrap.find('.wt-notify-btn').removeClass('wt-notify-btn-style');
						$wrap.removeClass('ema-bis-wrap-style');
					});
				}

				// ====== Simple Products ======
				<?php if ( $product && $product->is_type( 'simple' ) ) : ?>
					var isInStock = <?php echo esc_js($product->is_in_stock() ? 'true' : 'false'); ?>;
					var productId  = <?php echo esc_js($product->get_id()); ?>;

					if(!isInStock){
						//$wrap.show();
						$wrap.attr('data-product_id', productId);
						$wrap.find('.wt-notify-btn').attr('data-product_id', productId);
						$wrap.find('.wt-notify-btn').addClass('wt-notify-btn-style');
						$wrap.find('.wt-notify-btn').css('margin-bottom', '20px');
						$wrap.addClass('ema-bis-wrap-style');
					} else {
						//$wrap.hide();
						$wrap.find('.wt-notify-btn').attr('data-product_id', '');
						$wrap.find('.wt-notify-btn').removeClass('wt-notify-btn-style');
						$wrap.removeClass('ema-bis-wrap-style');
					}
				<?php endif; ?>
			});
			</script>
			<?php
		}
	}


	new Storefrog_Connector();
}
