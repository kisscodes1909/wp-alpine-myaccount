<?php

namespace Codemanas\Typesense\WooCommerce;

use Codemanas\Typesense\WooCommerce\Admin\Admin;
use Codemanas\Typesense\WooCommerce\Admin\Customizer;
use Codemanas\Typesense\WooCommerce\Frontend\Frontend;
use Codemanas\Typesense\WooCommerce\Helpers\WPMLCompat;
use Codemanas\Typesense\WooCommerce\Main\Fields\Fields;
use Codemanas\Typesense\WooCommerce\Main\Licensing\Licensing;
use Codemanas\Typesense\WooCommerce\Main\Licensing\LicensingAjaxHandler;
use Codemanas\Typesense\WooCommerce\Main\Licensing\Updater;
use Codemanas\Typesense\WooCommerce\Main\Main;
use Codemanas\Typesense\WooCommerce\Main\TemplateHooks;
use Codemanas\Typesense\WooCommerce\Shortcode\Shortcode;

final class Bootstrap {
	const MINIMUM_PHP_VERSION = '7.4';
	private string $message = '';
	public static ?Bootstrap $_instance = null;

	/**
	 * @return Bootstrap|null
	 */
	public static function getInstance(): ?Bootstrap {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self:: $_instance;
	}

	/**
	 * pluginName constructor.
	 */
	public function __construct() {
		$this->autoload();
		add_action( 'plugins_loaded', array( $this, 'initPlugin' ) );
		register_activation_hook( CM_TSFWC_FILE_PATH, [ $this, 'plugin_activated' ] );
		register_deactivation_hook( CM_TSFWC_FILE_PATH, [ $this, 'plugin_deactivated' ] );
		add_action( 'in_plugin_update_message-' . CM_TSFWC_TYPESENSE_BASE_FILE, [ $this, 'plugin_update_message' ] );
	}

	/**
	 * @return bool
	 */
	private function checkDependencies(): bool {
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			$this->message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
				esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'typesense-search-for-woocommerce' ),
				'<strong>' . esc_html__( 'Typesense Search For WooCommerce', 'typesense-search-for-woocommerce' ) . '</strong>',
				'<strong>' . esc_html__( 'PHP', 'typesense-search-for-woocommerce' ) . '</strong>',
				self::MINIMUM_PHP_VERSION
			);
			add_action( 'admin_notices', [ $this, 'add_admin_notice' ] );

			return false;
		}

		//check for Search with Typesense is active
		if ( ! $this->is_plugin_active( 'search-with-typesense/codemanas-typesense.php' ) ) {
			$this->message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
				esc_html__( '"%1$s" requires "%2$s" plugin to work - please get it from the %3$s.', 'typesense-search-for-woocommerce' ),
				'<strong>' . esc_html__( 'Typesense Search For WooCommerce', 'typesense-search-for-woocommerce' ) . '</strong>',
				'<strong>' . esc_html__( 'Search with Typesense', 'typesense-search-for-woocommerce' ) . '</strong>',
				'<a href="' . esc_url( 'https://wordpress.org/plugins/search-with-typesense/' ) . '" target="_blank" rel="nofollow noopener">' . esc_html__( 'WordPress Repo', 'typesense-search-for-woocommerce' ) . '</a>',
			);
			add_action( 'admin_notices', [ $this, 'add_admin_notice' ] );

			return false;
		}

		//check for WooCommerce is active
		if ( ! $this->is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$this->message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
				esc_html__( '"%1$s" requires "%2$s" plugin to work - please get it from the %3$s.', 'typesense-search-for-woocommerce' ),
				'<strong>' . esc_html__( 'Typesense Search For WooCommerce', 'typesense-search-for-woocommerce' ) . '</strong>',
				'<strong>' . esc_html__( 'WooCommerce', 'typesense-search-for-woocommerce' ) . '</strong>',
				'<a href="' . esc_url( 'https://wordpress.org/plugins/woocommerce/' ) . '" target="_blank" rel="nofollow noopener">' . esc_html__( 'WordPress Repo', 'typesense-search-for-woocommerce' ) . '</a>',
			);
			add_action( 'admin_notices', [ $this, 'add_admin_notice' ] );

			return false;
		}


		return true;
	}

	/**
	 * @param $plugin
	 *
	 * @return bool
	 */
	private function is_plugin_active( $plugin ): bool {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true );
	}

	public function add_admin_notice(): void {

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		printf( '<div class="notice notice-error is-dismissible"><p>%1$s</p></div>', $this->message );

	}

	public function plugin_activated(): void {
		//other plugins can get this option and check if plugin is activated
		update_option( 'cm_tsfwc_plugin_activate', 'activated' );
	}

	public function plugin_deactivated(): void {
		delete_option( 'cm_tsfwc_plugin_activate' );
	}

	/**
	 * Autoload - PSR 4 Compliance
	 */
	public function autoload(): void {
		require_once CM_TSFWC_ROOT_DIR_PATH . '/vendor/autoload.php';
	}

	public function load_textdomain(): void {
		load_plugin_textdomain( 'typesense-search-for-woocommerce', false, dirname( plugin_basename( CM_TSFWC_FILE_PATH ) ) . '/languages' );
	}

	public function initPlugin(): void {

		$dependenciesPassed = $this->checkDependencies();
		if ( ! $dependenciesPassed ) {
			return;
		}
		$licensing = Licensing::get_instance();
		LicensingAjaxHandler::get_instance();
		new Updater( $licensing->get_store_url(),
			CM_TSFWC_FILE_PATH, [
				'version' => CM_TSFWC_VERSION,
				// current version number
				'license' => Fields::get_option( 'license_key' ),
				// license key (used get_option above to retrieve from DB)
				'item_id' => $licensing->get_item_id(),
				// id of this plugin
				'author'  => 'Codemanas',
				// author of this plugin
				'beta'    => false
				// set to true if you wish customers to receive update notifications of beta releases
			] );

		if ( ! empty( Fields::get_option( 'license_key' ) ) ) {
			Frontend::getInstance();
			Shortcode::getInstance();
			Main::getInstance();
			//@todo how to initiate functions in namespaced way
			require_once CM_TSFWC_ROOT_DIR_PATH . '/includes/Helpers/template-functions.php';
			TemplateHooks::get_instance();
			Admin::getInstance();
			Customizer::getInstance();
			if ( $this->check_wpml_dependencies() ) {
				WPMLCompat::get_instance();
			}
		}
		add_action( 'init', [ $this, 'load_textdomain' ] );
	}

	private function check_wpml_dependencies(): bool {
		return ( class_exists( 'Sitepress' ) && class_exists( 'woocommerce_wpml' ) );
	}

	public function plugin_update_message( $plugin_data ): void {
		$this->version_update_warning( CM_TSFWC_VERSION, $plugin_data['new_version'] );
	}

	public function version_update_warning( $current_version, $new_version ): void {
		$current_version_minor_part = explode( '.', $current_version )[1];
		$new_version_minor_part     = explode( '.', $new_version )[1];
		if ( $current_version_minor_part === $new_version_minor_part ) {
			return;
		}
		?>
        <style>
            .cmtsfwc-MajorUpdateNotice {
                display: flex;
                max-width: 1000px;
                margin: 0.5em 0;
            }

            .cmtsfwc-MajorUpdateMessage {
                margin-left: 1rem;
            }

            .cmtsfwc-MajorUpdateMessage-desc {
                margin-top: .5rem;
            }

            .cmtsfwc-MajorUpdateNotice + p {
                display: none;
            }
        </style>

        <hr>
        <div class="cmtsfwc-MajorUpdateNotice">
            <span class="dashicons dashicons-info" style="color: #f56e28;"></span>
            <div class="cmtsfwc-MajorUpdateMessage">
                <strong class="cmtsfwc-MajorUpdateMessage-title">
					<?php esc_html_e( 'Heads up, Please backup before upgrade!', 'typesense-search-for-woocommerce' ); ?>
                </strong>
                <div class="cmtsfwc-MajorUpdateMessage-desc">
					<?php
					esc_html_e( 'The latest update includes some substantial changes across different areas of the plugin. We highly recommend you backup your site before upgrading, and make sure you first update in a staging environment', 'typesense-search-for-woocommerce' );
					?>
                </div>
            </div>
        </div>
		<?php
	}

}

Bootstrap::getInstance();