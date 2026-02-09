<?php

namespace Codemanas\Typesense\WooCommerce\Shortcode;

use Codemanas\Typesense\WooCommerce\Helpers\Templates;
use Codemanas\Typesense\WooCommerce\Main\Main;

class Shortcode {
	public static int $count = 0;
	public static ?Shortcode $instance = null;

	public static function getInstance(): ?Shortcode {
		return is_null( self::$instance ) ? new self() : self::$instance;
	}

	/**
	 * @return mixed
	 */
	public static function getCount() {
		return self::$count;
	}

	/**
	 * Shortcodes constructor.
	 */
	public function __construct() {
		add_shortcode( 'cm_tsfwc_search', array( $this, 'render_instant_search' ) );
		add_shortcode( 'cm_tsfwc_autocomplete_search', array( $this, 'render_autocomplete_search' ) );
		add_action( 'wp_footer', array( $this, 'load_php_templates' ) );
	}

	public function render_autocomplete_search( $atts ) {
		$atts = shortcode_atts( [
			'placeholder' => '',
		], $atts );

		Main::getInstance()->load_autocomplete_script();

		return Main::getInstance()->autocomplete_html( $atts['placeholder'] );
	}

	public function render_instant_search( $atts ) {
		$per_page = apply_filters( 'loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page() );
        self::$count ++;
		//the most confusing thing - wc has to get their things together.

		$atts = shortcode_atts( [
			'cat_filter'          => 'show',
			'price_filter'        => 'show',
			'rating_filter'       => 'show',
			'attribute_filter'    => 'show',
			'per_page'            => $per_page,
			'pagination'          => 'show',
            'show_more_text'      => __( 'Show More', 'typesense-search-for-woocommerce' ), // Option for button when infinite pagination is enabled
			'routing'             => 'disable',
			'sortby'              => 'show',
			'columns'             => wc_get_default_products_per_row(),
			'placeholder'         => 'Search products...',
			'query_by'            => 'post_title,post_content',
			'show_featured_first' => 'no',
			'custom_class'        => '',
			'autofocus'        => true,
            'unique_id' => 'tsfw_instant_search_' . self::$count
		], $atts );


		$atts['per_page_config'] = apply_filters( 'cm_tsfwc_per_page_config', [
			[ 'label' => __( 'Per page', 'typesense-search-for-woocommerce' ), 'value' => $atts['per_page'], 'default' => true ],
			[ 'label' => sprintf( __( '%d per page', 'typesense-search-for-woocommerce' ), 10 ), 'value' => 10 ], // one item should have default as true
			[ 'label' => sprintf( __( '%d per page', 'typesense-search-for-woocommerce' ), 20 ), 'value' => 20 ],
			[ 'label' => sprintf( __( '%d per page', 'typesense-search-for-woocommerce' ), 30 ), 'value' => 30 ],
			[ 'label' => sprintf( __( '%d per page', 'typesense-search-for-woocommerce' ), 40 ), 'value' => 40 ],
			[ 'label' => sprintf( __( '%d per page', 'typesense-search-for-woocommerce' ), 50 ), 'value' => 50 ],
		] );

		$atts = apply_filters( 'cm_tsfwc_shortcode_params', $atts );
		ob_start();

		Templates::getInstance()->include_file( 'instant-search.php', $atts );

		return ob_get_clean();
	}

	public function load_php_templates() {
		Templates::getInstance()->include_file( 'instant-search-hits.php', '', true );
		Templates::getInstance()->include_file( 'instant-search-no-results.php', '', true );

		//Autocomplete
		Templates::getInstance()->include_file( 'autocomplete/item.php' );
		Templates::getInstance()->include_file( 'autocomplete/header.php' );
		Templates::getInstance()->include_file( 'autocomplete/no-results.php' );
	}
}
