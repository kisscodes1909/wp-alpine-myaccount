<?php

namespace Codemanas\Typesense\WooCommerce\Helpers;

use Codemanas\Typesense\Main\TypesenseAPI;

class WPMLCompat {
	public static ?WPMLCompat $instance = null;

	public static function get_instance(): ?WPMLCompat {
		return is_null( self::$instance ) ? self::$instance = new self() : self::$instance;
	}

	public function __construct() {
		//check if wpml is active
		add_filter( 'cm_tsfwc_data_before_entry', [ $this, 'add_lang_facet' ], 10, 3 );
		add_action( 'cm_tsfwc_custom_attributes', [ $this, 'add_lang_filter_widget' ] );
		add_action( 'wcml_after_sync_product_data', [ $this, 'after_product_sync' ], 10, 2 );
		add_filter( 'tsfwc_current_lang', [ $this, 'get_wpml_current_lang' ] );
		add_filter( 'cm_typesense_before_bulk_index', [ $this, 'switch_language' ] );
	}

	public function add_lang_facet( $formatted_data, $raw_data, $object_id ) {
		//wpml compatibility check
		$args     = [ 'element_id' => $object_id, 'element_type' => 'post_id' ];
		$language = apply_filters( 'wpml_element_language_code', null, $args );
		if ( $language !== null ) {
			$formatted_data['lang_attribute_filter'] = [ $language ];
		}

		return $formatted_data;
	}

	public function add_lang_filter_widget(): void {
		?>
        <div
                class="cmtsfwc-Filter-customAttributes lang_attribute_filter"
                data-facet_name="lang_attribute_filter"
                data-title="<?php _e( 'Filter by Language', 'typesense-search-for-woocommerce' ); ?>"
                style="display:none"></div>
		<?php
	}

	public function after_product_sync( $original_product_id, $tr_product_id ): void {
		$tr_post  = get_post( $tr_product_id );
		$document = TypesenseAPI::getInstance()->formatDocumentForEntry( $tr_post, $tr_product_id, 'product' );
		TypesenseAPI::getInstance()->upsertDocument( 'product', $document );
	}

	public function get_wpml_current_lang() {
		return apply_filters( 'wpml_current_language', null );
	}

	public function switch_language(): void {
		$current_language = apply_filters( 'wpml_current_language', null );
		do_action( 'wpml_switch_language', $current_language );
	}
}