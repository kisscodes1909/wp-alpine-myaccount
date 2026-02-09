<?php

namespace Codemanas\Typesense\Main;

use Codemanas\Typesense\Helpers\Templates;

class TemplateHooks {
	public static ?TemplateHooks $instance = null;

	public static function get_instance(): ?TemplateHooks {
		return is_null( self::$instance ) ? self::$instance = new self() : self::$instance;
	}

	public function __construct() {
		/*Default*/
		add_action( 'cm_typesense_instant_search_results_output', [ $this, 'search_header' ], 5, 4 );
		add_action( 'cm_typesense_instant_search_results_output', [ $this, 'filter_panel' ], 15, 4 );
		//also always required
		add_action( 'cm_typesense_instant_search_results_output', [ $this, 'main_panel' ], 20, 4 );

		//Header
		add_action( 'cm_typesense_instant_search_header', [ $this, 'search_box' ], 5, 4 );
		add_action( 'cm_typesense_instant_search_header', [ $this, 'index_switcher' ], 10, 4 );
		//stats
		add_action( 'cm_typesense_instant_search_header', [ $this, 'stats' ], 20, 4 );
		//filter panel
		add_action( 'cm_typesense_instant_search_header', [ $this, 'filter_panel_toggle' ], 25, 4 );
		//sort_by
		add_action( 'cm_typesense_instant_search_header', [ $this, 'sort_by' ], 30, 4 );
		//Refinements
		add_action( 'cm_typesense_instant_search_header', [ $this, 'show_refinements' ], 40, 4 );

		/*Main Panel Body*/
		add_action( 'cm_typesense_instant_search_results_main_panel_body', [ $this, 'main_panel_result_body' ], 5, 2 );
		add_action( 'cm_typesense_instant_search_results_main_panel_body', [ $this, 'pagination' ], 5, 2 );
	}

	/**
	 * Add Search Bar
	 *
	 * @return void
	 */
	public function search_header( $args, $config, $facet, $schema ) {
		cm_swt_get_template( 'instant-search/search-header.php', [
			'passed_args' => $args,
			'config'      => $config,
			'facet'       => $facet,
			'schema'      => $schema,
		] );
	}

	/**
	 * Add Filter Panel
	 *
	 * @param $args
	 * @param $config
	 * @param $facet
	 * @param $schema
	 *
	 * @return void
	 */
	public function filter_panel( $args, $config, $facet, $schema ) {
		cm_swt_get_template( 'instant-search/filter-panel.php', [
			'passed_args' => $args,
			'config'      => $config,
			'facet'       => $facet,
			'schema'      => $schema,
		] );
	}

	/**
	 * Add Main Panel
	 *
	 * @param $args
	 * @param $config
	 * @param $facet
	 * @param $schema
	 *
	 * @return void
	 */
	public function main_panel( $args, $config, $facet, $schema ) {
		cm_swt_get_template( 'instant-search/main-panel.php', [
			'passed_args' => $args,
			'config'      => $config,
			'facet'       => $facet,
			'schema'      => $schema,
		] );
	}

	public function search_box( $args, $config, $facet, $schema ) {
		cm_swt_get_template( 'instant-search/partials/search-box.php',
			[
				'passed_args' => $args,
				'config'      => $config,
				'facet'       => $facet,
				'schema'      => $schema,
			]
		);
	}

	/**
	 * Index switcher tabs
	 *
	 * @param $args
	 * @param $config
	 * @param $facet
	 * @param $schema
	 *
	 * @return void
	 */
	public function index_switcher( $args, $config, $facet, $schema ) {
		cm_swt_get_template( 'instant-search/partials/index-switcher.php', [
			'passed_args' => $args,
			'config'      => $config,
			'facet'       => $facet,
			'schema'      => $schema,
		] );
	}

	/**
	 * Sort By - Search Results
	 *
	 * @param $args
	 * @param $config
	 * @param $facet
	 * @param $schema
	 *
	 * @return void
	 */
	public function sort_by( $args, $config, $facet, $schema ) {
		cm_swt_get_template( 'instant-search/partials/sort-by.php', [
			'passed_args' => $args,
			'config'      => $config,
			'facet'       => $facet,
			'schema'      => $schema,
		] );
	}


	/**
	 * @param $args
	 * @param $config
	 * @param $facet
	 * @param $schema
	 *
	 * @return void
	 */
	public function stats( $args, $config, $facet, $schema ) {
		if ( $args['stats'] != 'show' ) {
			return;
		}
		cm_swt_get_template( 'instant-search/partials/stats.php', [
			'passed_args' => $args,
			'config'      => $config,
			'facet'       => $facet,
			'schema'      => $schema,
		] );
	}

	public function filter_panel_toggle( $args, $config, $facet, $schema ){
		cm_swt_get_template( 'instant-search/partials/filter-panel-toggle.php', [
			'passed_args' => $args,
			'config'      => $config,
			'facet'       => $facet,
			'schema'      => $schema,
		] );
	}

	/**
	 * @param $args
	 * @param $config
	 * @param $facet
	 * @param $schema
	 *
	 * @return void
	 */
	public function show_current_refinements( $args, $config, $facet, $schema ) {
		if ( $args['selected_filters'] != 'show' ) {
			return;
		}
		cm_swt_get_template( 'instant-search/partials/current-refinements.php', [
			'passed_args' => $args,
			'config'      => $config,
			'facet'       => $facet,
			'schema'      => $schema,
		] );
	}

	/**
	 * @param $args
	 * @param $config
	 * @param $facet
	 * @param $schema
	 *
	 * @return void
	 */
	public function show_clear_refinements( $args, $config, $facet, $schema ) {
		if ( $args['selected_filters'] != 'show' ) {
			return;
		}
		cm_swt_get_template( 'instant-search/partials/clear-refinements.php', [
			'passed_args' => $args,
			'config'      => $config,
			'facet'       => $facet,
			'schema'      => $schema,
		] );
	}


	/**
	 * @param $args
	 * @param $config
	 * @param $facet
	 * @param $schema
	 *
	 * @return void
	 */
	public function show_refinements( $args, $config, $facet, $schema ) {
		if ( $args['selected_filters'] != 'show' ) {
			return;
		}
		cm_swt_get_template( 'instant-search/partials/refinements.php', [
			'passed_args' => $args,
			'config'      => $config,
			'facet'       => $facet,
			'schema'      => $schema,
		] );
	}

	/**
	 * Result Body / Items
	 *
	 * @param $config
	 * @param $post_type
	 *
	 * @return void
	 */
	public function main_panel_result_body( $config, $post_type ) {
		cm_swt_get_template( 'instant-search/partials/item.php', [ 'config' => $config, 'post_type' => $post_type ] );
	}

	/**
	 * Add Pagination to Main Panel
	 *
	 * @param $config
	 * @param $post_type
	 *
	 * @return void
	 */
	public function pagination( $config, $post_type ) {
		cm_swt_get_template( 'instant-search/partials/pagination.php', [
			'config'    => $config,
			'post_type' => $post_type
		] );
	}

}