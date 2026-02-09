<?php

namespace Codemanas\Typesense\WooCommerce\Frontend;

use Codemanas\Typesense\Backend\Admin;
use Codemanas\Typesense\Helpers\Templates;
use Codemanas\Typesense\Main\TypesenseAPI;
use Codemanas\Typesense\WooCommerce\Main\Fields\Fields;
use Codemanas\Typesense\WooCommerce\Main\Main;

class Frontend {
	public static $instance = null;

	public static function getInstance() {
		return is_null( self::$instance ) ? self::$instance = new self() : self::$instance;
	}

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ), 20 );

		$tsfwc_wc_settings = Fields::get_option( 'global_setting' );
		$replace_product_search = $tsfwc_wc_settings['replace_product_search'] ?? false;

		if( 'popup' == $replace_product_search ) {
			add_action( 'wp_footer', [ $this, 'load_popup' ] );
		}
	}

	public function load_scripts() {
        wp_register_script( 'cm-tsfwc-frontend-autocomplete', CM_TSFWC_ROOT_URI_PATH . 'assets/frontend/autocomplete.js', [ 'wp-util' ], CM_TSFWC_VERSION, true );

        $admin_settings         = Admin::get_default_settings();
        $search_config_settings = Admin::get_search_config_settings();
        $tsfwc_global_settings  = Fields::get_option( 'global_setting' );
        wp_localize_script( 'cm-tsfwc-frontend-autocomplete', 'cm_tsfwc_autocomplete_default_settings', apply_filters( 'cm_tsfwc_autocomplete_default_settings', [
                'debug'                      => SCRIPT_DEBUG,
                'search_api_key'             => $admin_settings['search_api_key'],
                'port'                       => $admin_settings['port'],
                'node'                       => $admin_settings['node'],
                'protocol'                   => $admin_settings['protocol'],
                'enabled_post_types'         => $search_config_settings['enabled_post_types'],
                'available_post_types'       => $search_config_settings['available_post_types'],
                'search_config'              => $search_config_settings['config'],
                'collection_name'            => TypesenseAPI::getInstance()->getCollectionNameFromSchema( 'product' ),
                'autocomplete_submit_action' => $tsfwc_global_settings['autocomplete_submit_action'] ?? 'keep_open',
        ] ) );

		$cat_filter = is_product_category() ? get_queried_object()->name : '';

		$search_query = ( is_shop() && is_search() ) ? get_search_query() : false;

        wp_register_script( 'cm-tsfwc-frontend-instant-search', CM_TSFWC_ROOT_URI_PATH . 'assets/frontend/instant-search.js', array( 'wp-util' ), CM_TSFWC_VERSION, true );

		$tsfwc_wc_settings                    = Fields::get_option( 'global_setting' );
		$tsfwc_wc_settings['default_sort_by'] = isset( $tsfwc_wc_settings['default_sort_by'] ) && ! empty( $tsfwc_wc_settings['default_sort_by'] ) ? $tsfwc_wc_settings['default_sort_by'] : 'recent';


		$localized_strings = [
			'sort_by'        => [
				'default'                    => __( 'Default', 'typesense-search-for-woocommerce' ),
				'recent'                     => __( 'Recent', 'typesense-search-for-woocommerce' ),
				'oldest'                     => __( 'Oldest', 'typesense-search-for-woocommerce' ),
				'sort_by_rating_low_to_high' => __( 'Sort by rating: low to high', 'typesense-search-for-woocommerce' ),
				'sort_by_rating_high_to_low' => __( 'Sort by rating: high to low', 'typesense-search-for-woocommerce' ),
				'sort_by_price_low_to_high'  => __( 'Sort by price: low to high', 'typesense-search-for-woocommerce' ),
				'sort_by_price_high_to_low'  => __( 'Sort by price: high to low', 'typesense-search-for-woocommerce' ),
				'sort_by_popularity'         => __( 'Sort by popularity', 'typesense-search-for-woocommerce' ),
			],
			'show_more_text' => __( 'Show more', 'typesense-search-for-woocommerce' ),
			'show_less_text' => __( 'Show less', 'typesense-search-for-woocommerce' ),
			'up'             => __( 'Up', 'typesense-search-for-woocommerce' ),
			'clear_all'      => __( 'Clear all', 'typesense-search-for-woocommerce' ),
			'query_label'    => __( 'Query', 'typesense-search-for-woocommerce' ),
		];

		wp_localize_script( 'cm-tsfwc-frontend-instant-search','cm_tsfwc_default_settings', apply_filters( 'cm_tsfwc_default_settings', [
			'search_api_key'       => $admin_settings['search_api_key'],
			'port'                 => $admin_settings['port'],
			'node'                 => $admin_settings['node'],
			'protocol'             => $admin_settings['protocol'],
			'enabled_post_types'   => $search_config_settings['enabled_post_types'],
			'available_post_types' => $search_config_settings['available_post_types'],
			'search_config'        => $search_config_settings['config'],
			'date_format'          => apply_filters( 'cm_tsfwc_date_format', get_option( 'date_format' ) ),
			'cat_filter'           => $cat_filter,
			'search_query'         => $search_query,
			'tsfwc_settings'       => $tsfwc_wc_settings,
			'localized_strings'    => $localized_strings,
			'collection_name'      => TypesenseAPI::getInstance()->getCollectionNameFromSchema( 'product' ),
		] ) );

		wp_enqueue_script( 'cm-tsfwc-frontend-instant-search' );

		wp_register_style( 'cm-tsfwc-frontend-style', CM_TSFWC_ROOT_URI_PATH . 'assets/frontend/style.css', [ 'cm-typesense-frontend-style' ], CM_TSFWC_VERSION, false );
		wp_enqueue_style( 'cm-tsfwc-frontend-style' );

		wp_register_script( 'cm-tsfwc-popup', CM_TSFWC_ROOT_URI_PATH . 'assets/frontend/popup.js', array( 'wp-util' ), CM_TSFWC_VERSION, true );
	}

	public function get_woocommerce_product_loop_start_class() {

		//for compatibility themes that change loop start html
		ob_start();
		woocommerce_product_loop_start();
		$loop_start_html = ob_get_clean();
		$matches         = [];
		//let's just get the class please
		preg_match( '/class="(.*?)"/i', $loop_start_html, $matches );
		if ( isset( $matches[1] ) ) {
			return $matches[1];
		}
	}

	public function load_popup() {
		?>

        <div class="cmtsfwc-InstantSearchPopup">
            <div class="cmtsfwc-InstantSearchPopup--results">
                <a href="#" class="cmtsfwc-InstantSearchPopup--closeIcon" title="close">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
				<?php Main::getInstance()->call_shortcode(); ?>
            </div>
        </div>
		<?php
		wp_enqueue_script( 'cm-tsfwc-popup' );
	}
}
