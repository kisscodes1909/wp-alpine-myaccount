<?php

namespace Codemanas\Typesense\WooCommerce\Main;

class TemplateHooks {
	public static ?TemplateHooks $instance = null;

	public static function get_instance(): ?TemplateHooks {
		return is_null( self::$instance ) ? self::$instance = new self() : self::$instance;
	}

	public function __construct() {
		//always required - will fail if we don't have search_box
		add_action( 'cm_tsfwc_instant_search_results_header', [ $this, 'search_box' ], 5 );
		add_action( 'cm_tsfwc_instant_search_results_header', [ $this, 'search_stats' ], 10 );
		add_action( 'cm_tsfwc_instant_search_results_header', [ $this, 'search_refinements' ], 15 );

		//refinements
		add_action( 'cm_tsfwc_instant_search_refinements', [ $this, 'current_refinements' ], 5 );
		add_action( 'cm_tsfwc_instant_search_refinements', [ $this, 'clear_refinements' ], 10 );

		add_action( 'cm_tsfwc_instant_search_results_output', [ $this, 'filter_panel' ], 5, 2 );
		add_action( 'cm_tsfwc_instant_search_results_output', [ $this, 'main_panel' ], 10, 2 );

		/**Main Panel**/
		add_action( 'cm_tsfwc_instant_search_results_main_panel', [ $this, 'search_results_header' ], 5 );
		
		/**Results Header*/
		add_action( 'cm_tsfwc_instant_search_results_header_output', [ $this, 'results_title' ], 5 );
		add_action( 'cm_tsfwc_instant_search_results_header_output', [ $this, 'sort_by' ], 10 );
		add_action( 'cm_tsfwc_instant_search_results_header_output', [ $this, 'hits_per_page' ], 15 );

		//always required - will fail if we don't have results outputs
		add_action( 'cm_tsfwc_instant_search_results_main_panel', [ $this, 'search_results_output' ], 10 );
		add_action( 'cm_tsfwc_instant_search_results_main_panel', [ $this, 'pagination' ], 15 );

		/**Filter Panel**/
		add_action( 'cm_tsfwc_filter_panel_output', [ $this, 'category_filter' ], 5 );
		add_action( 'cm_tsfwc_filter_panel_output', [ $this, 'price_filter' ], 10 );
		add_action( 'cm_tsfwc_filter_panel_output', [ $this, 'rating_filter' ], 15 );
		add_action( 'cm_tsfwc_filter_panel_output', [ $this, 'product_attributes' ], 20 );
		add_action( 'cm_tsfwc_filter_panel_output', [ $this, 'custom_filters' ], 25 );
	}

	public function search_box() {
		cm_tsfwc_get_template( 'instant-search/partials/search-box.php' );
	}

	public function search_stats() {
		cm_tsfwc_get_template( 'instant-search/partials/search-stats.php' );
	}

	public function search_refinements() {
		cm_tsfwc_get_template( 'instant-search/partials/search-refinements.php' );
	}

	public function current_refinements() {
		cm_tsfwc_get_template( 'instant-search/partials/current-refinements.php' );
	}

	public function clear_refinements() {
		cm_tsfwc_get_template( 'instant-search/partials/clear-refinements.php' );
	}

	public function filter_panel( $args, $config ) {
		cm_tsfwc_get_template( 'instant-search/filter-panel.php', [ 'passed_args' => $args, 'config' => $config ] );
	}

	public function main_panel( $args, $config ) {
		cm_tsfwc_get_template( 'instant-search/main-panel.php', [ 'passed_args' => $args, 'config' => $config ] );
	}

	public function search_results_header( $args ) {
		cm_tsfwc_get_template( 'instant-search/partials/header.php', $args );
	}

	public function results_title( $args ) {
		cm_tsfwc_get_template( 'instant-search/partials/title.php', $args );
	}

	public function sort_by( $args ) {
		cm_tsfwc_get_template( 'instant-search/partials/sort-by.php', $args );
	}

	public function hits_per_page( $args ) {
		cm_tsfwc_get_template( 'instant-search/partials/hits-per-page.php', $args );
	}

	public function search_results_output( $args ) {
		cm_tsfwc_get_template( 'instant-search/partials/results.php', $args);
	}

	public function pagination( $config ) {
		cm_tsfwc_get_template( 'instant-search/partials/pagination.php', [ 'config' => $config ] );
	}

	public function category_filter( $passed_args ) {
		cm_tsfwc_get_template( 'instant-search/partials/category-filter.php', [ 'passed_args' => $passed_args ] );
	}

	public function price_filter( $passed_args ) {
		cm_tsfwc_get_template( 'instant-search/partials/price-filter.php', [ 'passed_args' => $passed_args ] );
	}

	public function rating_filter( $passed_args ) {
		cm_tsfwc_get_template( 'instant-search/partials/rating-filter.php', [ 'passed_args' => $passed_args ] );
	}

	public function product_attributes( $passed_args ) {
		cm_tsfwc_get_template( 'instant-search/partials/product-attributes.php', [ 'passed_args' => $passed_args ] );
	}

	public function custom_filters( $passed_args ) {
		cm_tsfwc_get_template( 'instant-search/partials/custom-filters.php', [ 'passed_args' => $passed_args ] );
	}
}